<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/db_connection.php';

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Login required.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed.']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);

    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid payload.']);
        exit;
    }

    $selections = $payload['selections'] ?? [];
    $combinedOdds = isset($payload['combinedOdds']) ? (float) $payload['combinedOdds'] : 0.0;
    $stake = isset($payload['stake']) ? (float) $payload['stake'] : 0.0;

    if (!is_array($selections) || count($selections) === 0) {
        http_response_code(400);
        echo json_encode(['message' => 'No selections provided.']);
        exit;
    }

    if ($stake <= 0 || $combinedOdds <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid stake or odds.']);
        exit;
    }

    $potentialPayout = $stake * $combinedOdds;

    $db = footcast_db();
    $db->begin_transaction();

    try {
        $userId = (int) $_SESSION['user_id'];
        $stmtBalance = $db->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
        if (!$stmtBalance) {
            throw new RuntimeException('Failed to prepare balance lookup.');
        }
        $stmtBalance->bind_param('i', $userId);
        $stmtBalance->execute();
        $balanceResult = $stmtBalance->get_result();
        $balanceRow = $balanceResult ? $balanceResult->fetch_assoc() : null;
        $stmtBalance->close();

        if (!$balanceRow) {
            throw new RuntimeException('User balance not found.');
        }

        $currentBalance = (float) $balanceRow['balance'];
        if ($currentBalance < $stake) {
            http_response_code(400);
            echo json_encode(['message' => 'Insufficient balance.']);
            $db->rollback();
            $db->close();
            exit;
        }

        $newBalance = $currentBalance - $stake;
        $stmtUpdateBalance = $db->prepare('UPDATE users SET balance = ? WHERE id = ?');
        if (!$stmtUpdateBalance) {
            throw new RuntimeException('Failed to prepare balance update.');
        }
        $stmtUpdateBalance->bind_param('di', $newBalance, $userId);
        $stmtUpdateBalance->execute();
        $stmtUpdateBalance->close();

        $stmt = $db->prepare('INSERT INTO parlays (user_id, stake, total_odds, potential_payout) VALUES (?, ?, ?, ?)');
        if (!$stmt) {
            throw new RuntimeException('Failed to prepare parlay insert.');
        }
        $totalOdds = round($combinedOdds, 2);
        $stmt->bind_param('iddd', $userId, $stake, $totalOdds, $potentialPayout);
        $stmt->execute();
        $parlayId = $stmt->insert_id;
        $stmt->close();

        $stmtSel = $db->prepare(
            'INSERT INTO parlay_selections (parlay_id, match_id, bet_type, bet_value, bet_category, odds) VALUES (?, ?, ?, ?, ?, ?)'
        );
        if (!$stmtSel) {
            throw new RuntimeException('Failed to prepare selections insert.');
        }

        $stmtMatchSelect = $db->prepare('SELECT id FROM matches WHERE api_fixture_id = ? LIMIT 1');
        if (!$stmtMatchSelect) {
            throw new RuntimeException('Failed to prepare match lookup.');
        }
        $stmtMatchInsert = $db->prepare('INSERT INTO matches (api_fixture_id, home_team, away_team, match_date, status) VALUES (?, ?, ?, ?, ?)');
        if (!$stmtMatchInsert) {
            throw new RuntimeException('Failed to prepare match insert.');
        }

        foreach ($selections as $selection) {
            if (!is_array($selection)) {
                continue;
            }
            $apiFixtureId = isset($selection['apiFixtureId']) ? (int) $selection['apiFixtureId'] : 0;
            $matchId = 0;
            if ($apiFixtureId > 0) {
                $stmtMatchSelect->bind_param('i', $apiFixtureId);
                $stmtMatchSelect->execute();
                $resultMatch = $stmtMatchSelect->get_result();
                $found = $resultMatch ? $resultMatch->fetch_assoc() : null;
                if ($found) {
                    $matchId = (int) $found['id'];
                } else {
                    $homeTeam = (string) ($selection['home'] ?? 'Home');
                    $awayTeam = (string) ($selection['away'] ?? 'Away');
                    $matchDateRaw = (string) ($selection['matchDate'] ?? '');
                    $matchDate = date('Y-m-d H:i:s');
                    if ($matchDateRaw) {
                        $parsed = date_create($matchDateRaw);
                        if ($parsed) {
                            $matchDate = $parsed->format('Y-m-d H:i:s');
                        }
                    }
                    $status = 'upcoming';
                    $stmtMatchInsert->bind_param('issss', $apiFixtureId, $homeTeam, $awayTeam, $matchDate, $status);
                    $stmtMatchInsert->execute();
                    $matchId = $stmtMatchInsert->insert_id;
                }
            }
            if ($matchId === 0) {
                throw new RuntimeException('Invalid match reference.');
            }
            $betType = (string) ($selection['market'] ?? 'Market');
            $betValue = (string) ($selection['outcome'] ?? '');
            $betCategory = (string) ($selection['league'] ?? '');
            $odds = isset($selection['odds']) ? (float) $selection['odds'] : 0.0;
            $stmtSel->bind_param('iisssd', $parlayId, $matchId, $betType, $betValue, $betCategory, $odds);
            $stmtSel->execute();
        }

        $stmtMatchSelect->close();
        $stmtMatchInsert->close();
        $stmtSel->close();
        $db->commit();
        $db->close();

        echo json_encode([
            'success' => true,
            'parlayId' => $parlayId,
            'new_balance' => $newBalance,
        ]);
    } catch (Throwable $error) {
        $db->rollback();
        $db->close();
        http_response_code(500);
        echo json_encode(['message' => 'Failed to save bet slip.']);
    }
?>