<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/db_connection.php';
    require_once __DIR__ . '/config/football_api.php';

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Login required.']);
        exit;
    }

    $statusFilter = $_GET['status'] ?? null;
    $allowedStatuses = ['pending', 'won', 'lost', 'cancelled'];
    if ($statusFilter !== null && !in_array($statusFilter, $allowedStatuses, true)) {
        $statusFilter = null;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];

    $parlays = [];
    if ($statusFilter) {
        $stmt = $db->prepare('SELECT id, user_id, stake, total_odds, potential_payout, status, created_at FROM parlays WHERE user_id = ? AND status = ? ORDER BY created_at DESC');
        if ($stmt) {
            $stmt->bind_param('is', $userId, $statusFilter);
        }
    } else {
        $stmt = $db->prepare('SELECT id, user_id, stake, total_odds, potential_payout, status, created_at FROM parlays WHERE user_id = ? ORDER BY created_at DESC');
        if ($stmt) {
            $stmt->bind_param('i', $userId);
        }
    }

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to load parlays.']);
        $db->close();
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $parlays[] = $row;
        }
    }
    $stmt->close();

    $selectionsByParlay = [];
    if (!empty($parlays)) {
        $ids = array_map(static fn($row) => (int) $row['id'], $parlays);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmtSel = $db->prepare(
            "SELECT ps.parlay_id, ps.match_id, ps.bet_type, ps.bet_value, ps.bet_category, ps.odds, ps.status,
                    m.api_fixture_id, m.home_team, m.away_team, m.match_date, m.status AS match_status,
                    m.home_score, m.away_score, m.home_score_1h, m.away_score_1h
            FROM parlay_selections ps
            LEFT JOIN matches m ON m.id = ps.match_id
            WHERE ps.parlay_id IN ({$placeholders})
            ORDER BY ps.id ASC"
        );
        if ($stmtSel) {
            $stmtSel->bind_param($types, ...$ids);
            $stmtSel->execute();
            $result = $stmtSel->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $selectionsByParlay[$row['parlay_id']][] = $row;
                }
            }
            $stmtSel->close();
        }
    }

    autoSettleParlays($db, $parlays, $selectionsByParlay);

    if ($statusFilter) {
        $parlays = array_values(array_filter(
            $parlays,
            static fn($parlay) => ($parlay['status'] ?? '') === $statusFilter
        ));
        $validIds = array_flip(array_map(static fn($parlay) => (int) $parlay['id'], $parlays));
        $selectionsByParlay = array_intersect_key($selectionsByParlay, $validIds);
    }

    $db->close();

    foreach ($parlays as &$parlay) {
        $parlay['selections'] = $selectionsByParlay[$parlay['id']] ?? [];
    }
    unset($parlay);

    echo json_encode(['success' => true, 'parlays' => $parlays]);

    function autoSettleParlays(mysqli $db, array &$parlays, array &$selectionsByParlay): void
    {
        $matchCache = [];
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $cancelledMatchStatuses = ['CANCELLED', 'POSTPONED', 'SUSPENDED'];
        $finishedMatchStatuses = ['FINISHED', 'AWARDED'];

        foreach ($parlays as $index => &$parlay) {
            if (($parlay['status'] ?? '') !== 'pending') {
                continue;
            }
            $parlayId = (int) $parlay['id'];
            $parlaySelections = $selectionsByParlay[$parlayId] ?? [];
            if (empty($parlaySelections)) {
                continue;
            }

            $updatedSelections = [];
            $hasPending = false;
            $hasLost = false;
            $hasCancelled = false;

            foreach ($parlaySelections as $selection) {
                $currentStatus = $selection['status'] ?? 'pending';
                $apiFixtureId = isset($selection['api_fixture_id']) ? (int) $selection['api_fixture_id'] : 0;

                if ($currentStatus !== 'pending' || $apiFixtureId <= 0) {
                    $updatedSelections[] = $selection;
                    if ($currentStatus === 'pending') {
                        $hasPending = true;
                    } elseif ($currentStatus === 'lost') {
                        $hasLost = true;
                    } elseif ($currentStatus === 'cancelled') {
                        $hasCancelled = true;
                    }
                    continue;
                }

                $matchDate = parseMatchDateUtc($selection['match_date'] ?? null);
                if ($matchDate && $matchDate > $now) {
                    $updatedSelections[] = $selection;
                    $hasPending = true;
                    continue;
                }

                if (!array_key_exists($apiFixtureId, $matchCache)) {
                    $matchCache[$apiFixtureId] = fetchMatchResult($apiFixtureId);
                }

                $matchResult = $matchCache[$apiFixtureId];
                if (!$matchResult) {
                    $updatedSelections[] = $selection;
                    $hasPending = true;
                    continue;
                }

                $matchStatus = strtoupper((string) ($matchResult['status'] ?? ''));
                if (in_array($matchStatus, $cancelledMatchStatuses, true)) {
                    $selection['status'] = 'cancelled';
                    $hasCancelled = true;
                    $updatedSelections[] = $selection;
                    continue;
                }

                if (!in_array($matchStatus, $finishedMatchStatuses, true)) {
                    $updatedSelections[] = $selection;
                    $hasPending = true;
                    continue;
                }

                $evaluation = evaluateSelection($selection, $matchResult);
                if ($evaluation === 'pending') {
                    $hasPending = true;
                } elseif ($evaluation === 'lost') {
                    $hasLost = true;
                } elseif ($evaluation === 'cancelled') {
                    $hasCancelled = true;
                }
                $selection['status'] = $evaluation;
                $updatedSelections[] = $selection;
            }

            $newParlayStatus = 'pending';
            if (!$hasPending) {
                if ($hasLost) {
                    $newParlayStatus = 'lost';
                } elseif ($hasCancelled) {
                    $newParlayStatus = 'cancelled';
                } else {
                    $newParlayStatus = 'won';
                }
            }

            persistParlaySettlement($db, $parlay, $parlaySelections, $updatedSelections, $newParlayStatus);
            $selectionsByParlay[$parlayId] = $updatedSelections;
        }
        unset($parlay);
    }

    function parseMatchDateUtc(?string $raw): ?DateTimeImmutable
    {
        if (!$raw) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (Throwable $error) {
            return null;
        }
    }

    function fetchMatchResult(int $apiFixtureId): ?array
    {
        $result = makeFootballAPIRequest("/matches/{$apiFixtureId}", [], true, 180);
        if (!$result['success']) {
            return null;
        }
        $match = $result['data']['match'] ?? null;
        if (!is_array($match)) {
            return null;
        }

        return [
            'status' => $match['status'] ?? null,
            'full_time' => [
                'home' => $match['score']['fullTime']['home'] ?? null,
                'away' => $match['score']['fullTime']['away'] ?? null,
            ],
            'half_time' => [
                'home' => $match['score']['halfTime']['home'] ?? null,
                'away' => $match['score']['halfTime']['away'] ?? null,
            ],
        ];
    }

    function evaluateSelection(array $selection, array $matchResult): string
    {
        $betType = (string) ($selection['bet_type'] ?? '');
        $full = $matchResult['full_time'] ?? [];
        $half = $matchResult['half_time'] ?? [];
        $homeFull = $full['home'];
        $awayFull = $full['away'];
        $homeHalf = $half['home'];
        $awayHalf = $half['away'];
        $market = strtolower(trim($betType));
        $betValue = strtolower(trim((string) ($selection['bet_value'] ?? '')));
        $homeTeam = strtolower(trim((string) ($selection['home_team'] ?? '')));
        $awayTeam = strtolower(trim((string) ($selection['away_team'] ?? '')));

        if (in_array($betType, ['home_win', 'away_win', 'draw'], true) || $market === 'match result') {
            if ($market === 'match result') {
                if ($betValue === 'draw') {
                    $betType = 'draw';
                } elseif ($betValue === $homeTeam) {
                    $betType = 'home_win';
                } elseif ($betValue === $awayTeam) {
                    $betType = 'away_win';
                } else {
                    return 'cancelled';
                }
            }
            if ($homeFull === null || $awayFull === null) {
                return 'cancelled';
            }
            if ($homeFull === $awayFull) {
                return $betType === 'draw' ? 'won' : 'lost';
            }
            $winner = $homeFull > $awayFull ? 'home_win' : 'away_win';
            return $betType === $winner ? 'won' : 'lost';
        }

        if (in_array($betType, ['1h_home_win', '1h_away_win', '1h_draw'], true) || $market === '1st half result') {
            if ($market === '1st half result') {
                if ($betValue === 'draw') {
                    $betType = '1h_draw';
                } elseif ($betValue === $homeTeam) {
                    $betType = '1h_home_win';
                } elseif ($betValue === $awayTeam) {
                    $betType = '1h_away_win';
                } else {
                    return 'cancelled';
                }
            }
            if ($homeHalf === null || $awayHalf === null) {
                return 'cancelled';
            }
            if ($homeHalf === $awayHalf) {
                return $betType === '1h_draw' ? 'won' : 'lost';
            }
            $winner = $homeHalf > $awayHalf ? '1h_home_win' : '1h_away_win';
            return $betType === $winner ? 'won' : 'lost';
        }

        if (in_array($betType, ['2h_home_win', '2h_away_win', '2h_draw'], true) || $market === '2nd half result') {
            if ($market === '2nd half result') {
                if ($betValue === 'draw') {
                    $betType = '2h_draw';
                } elseif ($betValue === $homeTeam) {
                    $betType = '2h_home_win';
                } elseif ($betValue === $awayTeam) {
                    $betType = '2h_away_win';
                } else {
                    return 'cancelled';
                }
            }
            if ($homeHalf === null || $awayHalf === null || $homeFull === null || $awayFull === null) {
                return 'cancelled';
            }
            $homeSecondHalf = $homeFull - $homeHalf;
            $awaySecondHalf = $awayFull - $awayHalf;
            if ($homeSecondHalf === $awaySecondHalf) {
                return $betType === '2h_draw' ? 'won' : 'lost';
            }
            $winner = $homeSecondHalf > $awaySecondHalf ? '2h_home_win' : '2h_away_win';
            return $betType === $winner ? 'won' : 'lost';
        }

        return 'cancelled';
    }

    function persistParlaySettlement(mysqli $db, array &$parlay, array $beforeSelections, array $afterSelections, string $newStatus): void
    {
        $parlayId = (int) $parlay['id'];
        $userId = (int) $parlay['user_id'];
        $currentStatus = (string) $parlay['status'];
        $statusChanged = $newStatus !== $currentStatus;
        $selectionUpdates = [];

        foreach ($afterSelections as $index => $selection) {
            $beforeStatus = $beforeSelections[$index]['status'] ?? 'pending';
            if ($beforeStatus !== $selection['status']) {
                $selectionUpdates[] = [
                    'match_id' => (int) ($selection['match_id'] ?? 0),
                    'status' => (string) $selection['status'],
                ];
            }
        }

        if (!$statusChanged && empty($selectionUpdates)) {
            return;
        }

        $db->begin_transaction();
        try {
            if (!empty($selectionUpdates)) {
                $stmtSel = $db->prepare('UPDATE parlay_selections SET status = ? WHERE parlay_id = ? AND match_id = ?');
                if ($stmtSel) {
                    foreach ($selectionUpdates as $update) {
                        $stmtSel->bind_param('sii', $update['status'], $parlayId, $update['match_id']);
                        $stmtSel->execute();
                    }
                    $stmtSel->close();
                }
            }

            if ($statusChanged) {
                $stmtParlay = $db->prepare('UPDATE parlays SET status = ? WHERE id = ? AND status = ?');
                if ($stmtParlay) {
                    $stmtParlay->bind_param('sis', $newStatus, $parlayId, $currentStatus);
                    $stmtParlay->execute();
                    $stmtParlay->close();
                }

                if ($newStatus === 'won') {
                    $payout = (float) $parlay['potential_payout'];
                    $stmtBalance = $db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
                    if ($stmtBalance) {
                        $stmtBalance->bind_param('di', $payout, $userId);
                        $stmtBalance->execute();
                        $stmtBalance->close();
                    }
                }

                $parlay['status'] = $newStatus;
            }

            $db->commit();
        } catch (Throwable $error) {
            $db->rollback();
        }
    }
?>