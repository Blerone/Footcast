<?php
    declare(strict_types=1);
    session_start();

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/football_api.php';
    require_once __DIR__ . '/assets/includes/BetSettlementService.php';

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Login required.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $conn = Database::getConnection();
    $settlementService = new BetSettlementService($conn);

    $stats = [
        'bets_processed'   => 0,
        'bets_won'         => 0,
        'bets_lost'        => 0,
        'parlays_settled'  => 0,
    ];

    try {
        $conn->begin_transaction();
        $matchesSql = "
            SELECT DISTINCT
                m.id AS match_id,
                m.api_fixture_id,
                m.home_score,
                m.away_score,
                m.home_score_1h,
                m.away_score_1h
            FROM matches m
            JOIN bets b ON b.match_id = m.id
            WHERE b.status = 'pending'
            AND b.cashed_out_amount IS NULL
            AND m.status = 'finished'
            AND m.home_score IS NOT NULL
            AND m.away_score IS NOT NULL
        ";

        $matchesRes = $conn->query($matchesSql);
        $processedMatches = [];

        if ($matchesRes) {
            while ($matchRow = $matchesRes->fetch_assoc()) {
                $matchId = (int) $matchRow['match_id'];
                $apiFixtureId = (int) $matchRow['api_fixture_id'];

                if (!isset($processedMatches[$matchId])) {
                    $processedMatches[$matchId] = $settlementService->getMatchStatistics($matchId, $apiFixtureId);
                }
                $statistics = $processedMatches[$matchId];

                $betsStmt = $conn->prepare(
                    "SELECT id, user_id, bet_type, amount, odds, potential_payout
                    FROM bets
                    WHERE match_id = ? AND status = 'pending' AND cashed_out_amount IS NULL"
                );
                if (!$betsStmt) {
                    continue;
                }
                $betsStmt->bind_param('i', $matchId);
                $betsStmt->execute();
                $betsRes = $betsStmt->get_result();

                while ($bet = $betsRes->fetch_assoc()) {
                    $evaluation = BetSettlementService::evaluateBet($bet, $matchRow, $statistics);
                    $betWon = (bool) ($evaluation['won'] ?? false);
                    $newStatus = $betWon ? 'won' : 'lost';

                    $u = $conn->prepare('UPDATE bets SET status = ? WHERE id = ?');
                    if ($u) {
                        $u->bind_param('si', $newStatus, $bet['id']);
                        $u->execute();
                        $u->close();
                    }

                    $stats['bets_processed']++;
                    if ($betWon) {
                        $stats['bets_won']++;
                    } else {
                        $stats['bets_lost']++;
                    }
                }
                $betsStmt->close();
            }
        }

        $selStmt = $conn->prepare(
            "SELECT
                s.id,
                s.parlay_id,
                s.match_id,
                s.bet_type,
                m.id AS match_id,
                m.api_fixture_id,
                m.home_score,
                m.away_score,
                m.home_score_1h,
                m.away_score_1h
            FROM parlay_selections s
            JOIN matches m ON m.id = s.match_id
            WHERE s.status = 'pending'
            AND m.status = 'finished'
            AND m.home_score IS NOT NULL
            AND m.away_score IS NOT NULL"
        );

        if ($selStmt) {
            $selStmt->execute();
            $selRes = $selStmt->get_result();

            while ($s = $selRes->fetch_assoc()) {
                $matchId = (int) $s['match_id'];
                $apiFixtureId = (int) $s['api_fixture_id'];

                if (!isset($processedMatches[$matchId])) {
                    $processedMatches[$matchId] = $settlementService->getMatchStatistics($matchId, $apiFixtureId);
                }
                $statistics = $processedMatches[$matchId];

                $matchData = [
                    'home_score'     => $s['home_score'],
                    'away_score'     => $s['away_score'],
                    'home_score_1h'  => $s['home_score_1h'],
                    'away_score_1h'  => $s['away_score_1h'],
                ];
                $betData = ['bet_type' => $s['bet_type']];
                $evaluation = BetSettlementService::evaluateBet($betData, $matchData, $statistics);
                $status = ($evaluation['won'] ?? false) ? 'won' : 'lost';

                $u = $conn->prepare('UPDATE parlay_selections SET status = ? WHERE id = ?');
                if ($u) {
                    $u->bind_param('si', $status, $s['id']);
                    $u->execute();
                    $u->close();
                }
            }
            $selStmt->close();
        }

        $res2 = $conn->query(
            "SELECT id, user_id, potential_payout
            FROM parlays
            WHERE status = 'pending'
            AND NOT EXISTS (
                SELECT 1
                FROM parlay_selections s
                WHERE s.parlay_id = parlays.id
                    AND s.status = 'pending'
            )"
        );

        if ($res2) {
            while ($p = $res2->fetch_assoc()) {
                $pid = (int) $p['id'];
                $check = $conn->prepare(
                    "SELECT COUNT(*) AS lost_cnt
                    FROM parlay_selections
                    WHERE parlay_id = ? AND status = 'lost'"
                );
                if (!$check) {
                    continue;
                }
                $check->bind_param('i', $pid);
                $check->execute();
                $lostRow = $check->get_result()->fetch_assoc();
                $lostCnt = (int) ($lostRow['lost_cnt'] ?? 0);
                $check->close();

                $newStatus = $lostCnt === 0 ? 'won' : 'lost';
                $upd = $conn->prepare('UPDATE parlays SET status = ? WHERE id = ?');
                if ($upd) {
                    $upd->bind_param('si', $newStatus, $pid);
                    $upd->execute();
                    $upd->close();
                }

                if ($newStatus === 'won') {
                    $stats['parlays_settled']++;
                }
            }
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Manual settlement completed',
            'stats'   => $stats,
        ]);
    } catch (Throwable $e) {
        if ($conn && $conn->errno === 0) {
            @$conn->rollback();
        }
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'stats'   => $stats,
        ]);
    } finally {
        Database::closeConnection($conn);
    }
?>