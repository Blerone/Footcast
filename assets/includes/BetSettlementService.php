<?php
declare(strict_types=1);

final class BetSettlementService
{
    public function __construct(
        private mysqli $conn
    ) {
    }

    public function creditUserBalance(int $userId, float $amount): void
    {
        if ($userId <= 0 || $amount <= 0) {
            return;
        }
        $stmt = $this->conn->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
        if (!$stmt) {
            return;
        }
        $stmt->bind_param('di', $amount, $userId);
        $stmt->execute();
        $stmt->close();
    }

    public function getMatchStatistics(int $matchId, int $apiFixtureId): array
    {
        if ($apiFixtureId <= 0) {
            return [];
        }

        if (!function_exists('getMatchById')) {
            return [];
        }

        $result = getMatchById($apiFixtureId);
        if (!($result['success'] ?? false) || !isset($result['match']['statistics'])) {
            return [];
        }

        return (array) $result['match']['statistics'];
    }

    public static function evaluateBet(array $bet, array $matchData, array $statistics): array
    {
        $type = trim((string) ($bet['bet_type'] ?? ''));

        $homeFull = array_key_exists('home_score', $matchData) ? (int) $matchData['home_score'] : null;
        $awayFull = array_key_exists('away_score', $matchData) ? (int) $matchData['away_score'] : null;
        $homeHalf = array_key_exists('home_score_1h', $matchData) ? (int) $matchData['home_score_1h'] : null;
        $awayHalf = array_key_exists('away_score_1h', $matchData) ? (int) $matchData['away_score_1h'] : null;

        if (in_array($type, ['home_win', 'away_win', 'draw'], true)) {
            if ($homeFull === null || $awayFull === null) {
                return ['won' => false];
            }
            if ($homeFull === $awayFull) {
                return ['won' => ($type === 'draw')];
            }
            $winner = ($homeFull > $awayFull) ? 'home_win' : 'away_win';
            return ['won' => ($type === $winner)];
        }

        if (in_array($type, ['1h_home_win', '1h_away_win', '1h_draw'], true)) {
            if ($homeHalf === null || $awayHalf === null) {
                return ['won' => false];
            }
            if ($homeHalf === $awayHalf) {
                return ['won' => ($type === '1h_draw')];
            }
            $winner = ($homeHalf > $awayHalf) ? '1h_home_win' : '1h_away_win';
            return ['won' => ($type === $winner)];
        }

        if (in_array($type, ['2h_home_win', '2h_away_win', '2h_draw'], true)) {
            if ($homeHalf === null || $awayHalf === null || $homeFull === null || $awayFull === null) {
                return ['won' => false];
            }
            $home2h = $homeFull - $homeHalf;
            $away2h = $awayFull - $awayHalf;
            if ($home2h === $away2h) {
                return ['won' => ($type === '2h_draw')];
            }
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
            $threshold = (float) $m[4];

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

            $total = (float) $home + (float) $away;

            if ($direction === 'over') {
                return ['won' => ($total > $threshold)];
            }
            return ['won' => ($total < $threshold)];
        }

        return ['won' => false];
    }
}
