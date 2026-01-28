<?php
    declare(strict_types=1);

    session_start();

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/php-error.log');

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/football_api.php';

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Login required.']);
        exit;
    }

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $conn = getDBConnection();

    $stats = [
        'matches_synced'   => 0,
        'bets_processed'   => 0,
        'bets_won'         => 0,
        'bets_lost'        => 0,
        'parlay_selections_processed' => 0,
        'parlays_settled'  => 0,
        'users_credited'   => 0,
    ];

    try {
        $fixtureIds = [];

        $pendingSinglesQuery = "
            SELECT DISTINCT m.api_fixture_id
            FROM matches m
            JOIN bets b ON b.match_id = m.id
            WHERE b.status = 'pending'
            AND b.cashed_out_amount IS NULL
            AND m.api_fixture_id IS NOT NULL
        ";
        if ($res = $conn->query($pendingSinglesQuery)) {
            while ($row = $res->fetch_assoc()) {
                $fixtureIds[] = (int)$row['api_fixture_id'];
            }
            $res->close();
        }

        $pendingParlaysQuery = "
            SELECT DISTINCT m.api_fixture_id
            FROM matches m
            JOIN parlay_selections ps ON ps.match_id = m.id
            JOIN parlays p ON p.id = ps.parlay_id
            WHERE ps.status = 'pending'
            AND p.status = 'pending'
            AND m.api_fixture_id IS NOT NULL
        ";
        if ($res = $conn->query($pendingParlaysQuery)) {
            while ($row = $res->fetch_assoc()) {
                $fixtureIds[] = (int)$row['api_fixture_id'];
            }
            $res->close();
        }

        $fixtureIds = array_values(array_unique(array_filter($fixtureIds)));

        if (!empty($fixtureIds)) {
            $syncedFixtures = [];
            foreach ($fixtureIds as $fixtureId) {
                if (isset($syncedFixtures[$fixtureId])) {
                    continue;
                }

                $matchResult = getMatchById($fixtureId);
                if (!($matchResult['success'] ?? false) || !isset($matchResult['match'])) {
                    continue;
                }

                $match = $matchResult['match'];
                $statusShort = strtoupper((string)($match['fixture']['status']['short'] ?? ''));

                $homeScore = $match['goals']['home'] ?? null;
                $awayScore = $match['goals']['away'] ?? null;
                $home1h = $match['goals_1h']['home'] ?? ($match['score']['halftime']['home'] ?? null);
                $away1h = $match['goals_1h']['away'] ?? ($match['score']['halftime']['away'] ?? null);

                $dbStatus = 'upcoming';
                if (in_array($statusShort, ['FINISHED', 'FT', 'AWARDED'], true)) {
                    $dbStatus = 'finished';
                } elseif (in_array($statusShort, ['LIVE', 'IN_PLAY', '1H', '2H', 'HT'], true)) {
                    $dbStatus = 'live';
                }

                if ($homeScore === null || $awayScore === null) {
                    continue;
                }

                $stmt = $conn->prepare("
                    UPDATE matches
                    SET home_score = ?, away_score = ?, home_score_1h = ?, away_score_1h = ?, status = ?
                    WHERE api_fixture_id = ?
                ");
                if (!$stmt) {
                    continue;
                }

                $home1hInt = ($home1h === null) ? null : (int)$home1h;
                $away1hInt = ($away1h === null) ? null : (int)$away1h;

                $stmt->bind_param(
                    'iiiisi',
                    $homeScore,
                    $awayScore,
                    $home1hInt,
                    $away1hInt,
                    $dbStatus,
                    $fixtureId
                );
                $stmt->execute();
                $stmt->close();

                $syncedFixtures[$fixtureId] = true;
                $stats['matches_synced']++;
            }
        }

        $conn->begin_transaction();

        $processedMatches = [];

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

        if ($matchesRes = $conn->query($matchesSql)) {
            while ($matchRow = $matchesRes->fetch_assoc()) {
                $matchId = (int)$matchRow['match_id'];
                $apiFixtureId = (int)$matchRow['api_fixture_id'];

                if (!isset($processedMatches[$matchId])) {
                    $processedMatches[$matchId] = getMatchStatistics($conn, $matchId, $apiFixtureId);
                }
                $statistics = $processedMatches[$matchId];

                $betsStmt = $conn->prepare("
                    SELECT id, user_id, bet_type, amount, odds, potential_payout, status
                    FROM bets
                    WHERE match_id = ?
                    AND status = 'pending'
                    AND cashed_out_amount IS NULL
                ");
                if (!$betsStmt) {
                    continue;
                }

                $betsStmt->bind_param('i', $matchId);
                $betsStmt->execute();
                $betsRes = $betsStmt->get_result();

                while ($bet = $betsRes->fetch_assoc()) {
                    $evaluation = evaluateBet($bet, $matchRow, $statistics);
                    $betWon = (bool)($evaluation['won'] ?? false);
                    $newStatus = $betWon ? 'won' : 'lost';

                    $u = $conn->prepare("UPDATE bets SET status = ? WHERE id = ? AND status = 'pending'");
                    if ($u) {
                        $betId = (int)$bet['id'];
                        $u->bind_param('si', $newStatus, $betId);
                        $u->execute();
                        $affected = $u->affected_rows;
                        $u->close();

                        if ($affected > 0 && $betWon) {
                            $payout = (float)($bet['potential_payout'] ?? 0.0);
                            $userId = (int)$bet['user_id'];
                            if ($payout > 0) {
                                creditUserBalance($conn, $userId, $payout);
                                $stats['users_credited']++;
                            }
                        }
                    }

                    $stats['bets_processed']++;
                    if ($betWon) $stats['bets_won']++;
                    else $stats['bets_lost']++;
                }

                $betsStmt->close();
            }
            $matchesRes->close();
        }

        $selStmt = $conn->prepare("
            SELECT
                s.id AS selection_id,
                s.parlay_id,
                s.match_id,
                s.bet_type,
                m.api_fixture_id,
                m.home_score,
                m.away_score,
                m.home_score_1h,
                m.away_score_1h
            FROM parlay_selections s
            JOIN matches m ON m.id = s.match_id
            JOIN parlays p ON p.id = s.parlay_id
            WHERE s.status = 'pending'
            AND p.status = 'pending'
            AND m.status = 'finished'
            AND m.home_score IS NOT NULL
            AND m.away_score IS NOT NULL
        ");

        if ($selStmt) {
            $selStmt->execute();
            $selRes = $selStmt->get_result();

            while ($s = $selRes->fetch_assoc()) {
                $matchId = (int)$s['match_id'];
                $apiFixtureId = (int)$s['api_fixture_id'];

                if (!isset($processedMatches[$matchId])) {
                    $processedMatches[$matchId] = getMatchStatistics($conn, $matchId, $apiFixtureId);
                }
                $statistics = $processedMatches[$matchId];

                $matchData = [
                    'home_score'     => $s['home_score'],
                    'away_score'     => $s['away_score'],
                    'home_score_1h'  => $s['home_score_1h'],
                    'away_score_1h'  => $s['away_score_1h'],
                ];
                $betData = ['bet_type' => $s['bet_type']];

                $evaluation = evaluateBet($betData, $matchData, $statistics);
                $newSelStatus = ($evaluation['won'] ?? false) ? 'won' : 'lost';

                $u = $conn->prepare("UPDATE parlay_selections SET status = ? WHERE id = ? AND status = 'pending'");
                if ($u) {
                    $selId = (int)$s['selection_id'];
                    $u->bind_param('si', $newSelStatus, $selId);
                    $u->execute();
                    $u->close();
                }

                $stats['parlay_selections_processed']++;
            }

            $selStmt->close();
        }

        $parlaysReady = $conn->query("
            SELECT p.id, p.user_id, p.potential_payout
            FROM parlays p
            WHERE p.status = 'pending'
            AND NOT EXISTS (
                SELECT 1
                FROM parlay_selections s
                WHERE s.parlay_id = p.id
                    AND s.status = 'pending'
            )
        ");

        if ($parlaysReady) {
            while ($p = $parlaysReady->fetch_assoc()) {
                $pid = (int)$p['id'];

                $check = $conn->prepare("
                    SELECT COUNT(*) AS lost_cnt
                    FROM parlay_selections
                    WHERE parlay_id = ? AND status = 'lost'
                ");
                if (!$check) {
                    continue;
                }

                $check->bind_param('i', $pid);
                $check->execute();
                $lostRow = $check->get_result()->fetch_assoc();
                $lostCnt = (int)($lostRow['lost_cnt'] ?? 0);
                $check->close();

                $newStatus = ($lostCnt === 0) ? 'won' : 'lost';

                $upd = $conn->prepare("UPDATE parlays SET status = ? WHERE id = ? AND status = 'pending'");
                if ($upd) {
                    $upd->bind_param('si', $newStatus, $pid);
                    $upd->execute();
                    $affected = $upd->affected_rows;
                    $upd->close();

                    if ($affected > 0) {
                        $stats['parlays_settled']++;

                        if ($newStatus === 'won') {
                            $payout = (float)($p['potential_payout'] ?? 0.0);
                            $userId = (int)$p['user_id'];
                            if ($payout > 0) {
                                creditUserBalance($conn, $userId, $payout);
                                $stats['users_credited']++;
                            }
                        }
                    }
                }
            }
            $parlaysReady->close();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Bets settled successfully',
            'stats'   => $stats,
        ]);
    } catch (Throwable $e) {
        if (isset($conn) && $conn instanceof mysqli) {
            @ $conn->rollback();
        }
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'stats'   => $stats,
        ]);
    } finally {
        closeDBConnection($conn);
    }

    function creditUserBalance(mysqli $conn, int $userId, float $amount): void{
        if ($userId <= 0 || $amount <= 0) {
            return;
        }
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        if (!$stmt) {
            return;
        }
        $stmt->bind_param('di', $amount, $userId);
        $stmt->execute();
        $stmt->close();
    }

    function getMatchStatistics(mysqli $conn, int $matchId, int $apiFixtureId): array{
        if ($apiFixtureId <= 0) {
            return [];
        }

        $result = getMatchById($apiFixtureId);
        if (!($result['success'] ?? false) || !isset($result['match']['statistics'])) {
            return [];
        }

        return (array)$result['match']['statistics'];
    }

    function evaluateBet(array $bet, array $matchData, array $statistics): array{
        $type = trim((string)($bet['bet_type'] ?? ''));

        $homeFull = array_key_exists('home_score', $matchData) ? (int)$matchData['home_score'] : null;
        $awayFull = array_key_exists('away_score', $matchData) ? (int)$matchData['away_score'] : null;
        $homeHalf = array_key_exists('home_score_1h', $matchData) ? (int)$matchData['home_score_1h'] : null;
        $awayHalf = array_key_exists('away_score_1h', $matchData) ? (int)$matchData['away_score_1h'] : null;

        if (in_array($type, ['home_win', 'away_win', 'draw'], true)) {
            if ($homeFull === null || $awayFull === null) return ['won' => false];
            if ($homeFull === $awayFull) return ['won' => ($type === 'draw')];
            $winner = ($homeFull > $awayFull) ? 'home_win' : 'away_win';
            return ['won' => ($type === $winner)];
        }

        if (in_array($type, ['1h_home_win', '1h_away_win', '1h_draw'], true)) {
            if ($homeHalf === null || $awayHalf === null) return ['won' => false];
            if ($homeHalf === $awayHalf) return ['won' => ($type === '1h_draw')];
            $winner = ($homeHalf > $awayHalf) ? '1h_home_win' : '1h_away_win';
            return ['won' => ($type === $winner)];
        }

        if (in_array($type, ['2h_home_win', '2h_away_win', '2h_draw'], true)) {
            if ($homeHalf === null || $awayHalf === null || $homeFull === null || $awayFull === null) return ['won' => false];
            $home2h = $homeFull - $homeHalf;
            $away2h = $awayFull - $awayHalf;
            if ($home2h === $away2h) return ['won' => ($type === '2h_draw')];
            $winner = ($home2h > $away2h) ? '2h_home_win' : '2h_away_win';
            return ['won' => ($type === $winner)];
        }

        if (preg_match(
            '/^(corners(_1h)?|yellow_cards(_1h)?|cards|shots_on_target(_1h)?|offsides(_1h)?|fouls(_1h)?|throw_ins(_1h)?|shots_towards_goal|posts_crossbars(_1h)?)(_over_|_under_)([0-9]+(?:\.[0-9]+)?)$/',
            $type,
            $m
        )) {
            $metricRaw = $m[1];
            $direction = trim($m[3], '_'); 
            $threshold = (float)$m[4];

            $home = null;
            $away = null;

            switch ($metricRaw) {
                case 'corners':
                    $home = $statistics['corners']['home'] ?? null;
                    $away = $statistics['corners']['away'] ?? null;
                    break;
                case 'corners_1h':
                    $home = $statistics['corners_1h']['home'] ?? null;
                    $away = $statistics['corners_1h']['away'] ?? null;
                    break;
                case 'yellow_cards':
                    $home = $statistics['yellow_cards']['home'] ?? null;
                    $away = $statistics['yellow_cards']['away'] ?? null;
                    break;
                case 'yellow_cards_1h':
                    $home = $statistics['yellow_cards_1h']['home'] ?? null;
                    $away = $statistics['yellow_cards_1h']['away'] ?? null;
                    break;
                case 'cards':
                    $home = $statistics['yellow_cards']['home'] ?? null;
                    $away = $statistics['yellow_cards']['away'] ?? null;
                    break;
                case 'shots_on_target':
                    $home = $statistics['shots_on_target']['home'] ?? null;
                    $away = $statistics['shots_on_target']['away'] ?? null;
                    break;
                case 'shots_on_target_1h':
                    $home = $statistics['shots_on_target_1h']['home'] ?? null;
                    $away = $statistics['shots_on_target_1h']['away'] ?? null;
                    break;
                case 'offsides':
                    $home = $statistics['offsides']['home'] ?? null;
                    $away = $statistics['offsides']['away'] ?? null;
                    break;
                case 'offsides_1h':
                    $home = $statistics['offsides_1h']['home'] ?? null;
                    $away = $statistics['offsides_1h']['away'] ?? null;
                    break;
                case 'fouls':
                    $home = $statistics['fouls']['home'] ?? null;
                    $away = $statistics['fouls']['away'] ?? null;
                    break;
                case 'fouls_1h':
                    $home = $statistics['fouls_1h']['home'] ?? null;
                    $away = $statistics['fouls_1h']['away'] ?? null;
                    break;
                case 'throw_ins':
                    $home = $statistics['throw_ins']['home'] ?? null;
                    $away = $statistics['throw_ins']['away'] ?? null;
                    break;
                case 'throw_ins_1h':
                    $home = $statistics['throw_ins_1h']['home'] ?? null;
                    $away = $statistics['throw_ins_1h']['away'] ?? null;
                    break;
                case 'shots_towards_goal':
                    $home = $statistics['shots_towards_goal']['home'] ?? null;
                    $away = $statistics['shots_towards_goal']['away'] ?? null;
                    break;
                case 'posts_crossbars':
                    $home = $statistics['posts_crossbars']['home'] ?? null;
                    $away = $statistics['posts_crossbars']['away'] ?? null;
                    break;
                case 'posts_crossbars_1h':
                    $home = $statistics['posts_crossbars_1h']['home'] ?? null;
                    $away = $statistics['posts_crossbars_1h']['away'] ?? null;
                    break;
            }

            if ($home === null || $away === null) {
                return ['won' => false];
            }

            $total = (float)$home + (float)$away;

            if ($direction === 'over') {
                return ['won' => ($total > $threshold)];
            }
            return ['won' => ($total < $threshold)];
        }
        return ['won' => false];
    }
?>