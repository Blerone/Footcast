<?php
declare(strict_types=1);

final class ParlayService
{
    private const CANCELLED_MATCH_STATUSES = ['CANCELLED', 'POSTPONED', 'SUSPENDED'];
    private const FINISHED_MATCH_STATUSES = ['FINISHED', 'AWARDED'];

    public function __construct(
        private mysqli $db
    ) {
    }

    public function autoSettleParlays(array &$parlays, array &$selectionsByParlay): void
    {
        $matchCache = [];
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

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

                $matchDate = $this->parseMatchDateUtc($selection['match_date'] ?? null);
                if ($matchDate && $matchDate > $now) {
                    $updatedSelections[] = $selection;
                    $hasPending = true;
                    continue;
                }

                if (!array_key_exists($apiFixtureId, $matchCache)) {
                    $matchCache[$apiFixtureId] = $this->fetchMatchResult($apiFixtureId);
                }

                $matchResult = $matchCache[$apiFixtureId];
                if (!$matchResult) {
                    $updatedSelections[] = $selection;
                    $hasPending = true;
                    continue;
                }

                $matchStatus = strtoupper((string) ($matchResult['status'] ?? ''));
                if (in_array($matchStatus, self::CANCELLED_MATCH_STATUSES, true)) {
                    $selection['status'] = 'cancelled';
                    $hasCancelled = true;
                    $updatedSelections[] = $selection;
                    continue;
                }

                if (!in_array($matchStatus, self::FINISHED_MATCH_STATUSES, true)) {
                    $updatedSelections[] = $selection;
                    $hasPending = true;
                    continue;
                }

                $evaluation = $this->evaluateSelection($selection, $matchResult);
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

            $this->persistParlaySettlement($parlay, $parlaySelections, $updatedSelections, $newParlayStatus);
            $selectionsByParlay[$parlayId] = $updatedSelections;
        }
        unset($parlay);
    }

    public function persistParlaySettlement(array &$parlay, array $beforeSelections, array $afterSelections, string $newStatus): void
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

        $this->db->begin_transaction();
        try {
            if (!empty($selectionUpdates)) {
                $stmtSel = $this->db->prepare('UPDATE parlay_selections SET status = ? WHERE parlay_id = ? AND match_id = ?');
                if ($stmtSel) {
                    foreach ($selectionUpdates as $update) {
                        $stmtSel->bind_param('sii', $update['status'], $parlayId, $update['match_id']);
                        $stmtSel->execute();
                    }
                    $stmtSel->close();
                }
            }

            if ($statusChanged) {
                $stmtParlay = $this->db->prepare('UPDATE parlays SET status = ? WHERE id = ? AND status = ?');
                if ($stmtParlay) {
                    $stmtParlay->bind_param('sis', $newStatus, $parlayId, $currentStatus);
                    $stmtParlay->execute();
                    $stmtParlay->close();
                }

                if ($newStatus === 'won') {
                    $payout = (float) $parlay['potential_payout'];
                    $stmtBalance = $this->db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
                    if ($stmtBalance) {
                        $stmtBalance->bind_param('di', $payout, $userId);
                        $stmtBalance->execute();
                        $stmtBalance->close();
                    }
                }

                $parlay['status'] = $newStatus;
            }

            $this->db->commit();
        } catch (Throwable $error) {
            $this->db->rollback();
        }
    }

    private function parseMatchDateUtc(?string $raw): ?DateTimeImmutable
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

    private function fetchMatchResult(int $apiFixtureId): ?array
    {
        if (!function_exists('makeFootballAPIRequest')) {
            return null;
        }
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

    private function evaluateSelection(array $selection, array $matchResult): string
    {
        $betType = (string) ($selection['bet_type'] ?? '');
        $full = $matchResult['full_time'] ?? [];
        $half = $matchResult['half_time'] ?? [];
        $homeFull = $full['home'] ?? null;
        $awayFull = $full['away'] ?? null;
        $homeHalf = $half['home'] ?? null;
        $awayHalf = $half['away'] ?? null;
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
}
