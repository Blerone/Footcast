<?php
declare(strict_types=1);

final class HomepageRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function getHeroRows(): array{
        $stmt = $this->db->prepare(
            'SELECT id, sports_text, bet_text, updated_at
             FROM home_page_hero
             ORDER BY id DESC'
        );
        return $this->fetchAll($stmt);
    }

    public function getSectionsRows(): array{
        $stmt = $this->db->prepare(
            'SELECT id, trusted_by_title, about_title, about_highlight, bet_steps_title, popular_leagues_title, favorites_title, updated_at
             FROM home_page_sections
             ORDER BY id DESC'
        );
        return $this->fetchAll($stmt);
    }

    public function getStepsRows(): array{
        $stmt = $this->db->prepare(
            'SELECT id, step_number, step_title, sort_order
             FROM home_page_steps
             ORDER BY sort_order ASC, id ASC'
        );
        return $this->fetchAll($stmt);
    }

    public function getBannerRows(): array{
        $stmt = $this->db->prepare(
            'SELECT id, home_team, away_team, days_value, hours_value, minutes_value, seconds_value,
                    days_label, hours_label, minutes_label, seconds_label, odds_first, odds_second, odds_third, updated_at
             FROM home_page_banner
             ORDER BY id DESC'
        );
        return $this->fetchAll($stmt);
    }

    public function getLeaguesRows(): array{
        $stmt = $this->db->prepare(
            'SELECT id, league_name, stats_value, stats_label, top_scorer_label, goals_text, sort_order, is_active
             FROM home_page_leagues
             ORDER BY sort_order ASC, id ASC'
        );
        return $this->fetchAll($stmt);
    }

    public function getFavoritesRows(): array{
        $stmt = $this->db->prepare(
            'SELECT id, item_label, item_name, sort_order, is_active
             FROM home_page_favorites
             ORDER BY sort_order ASC, id ASC'
        );
        return $this->fetchAll($stmt);
    }

    public function getHeroById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, sports_text, bet_text FROM home_page_hero WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateHero(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE home_page_hero SET sports_text = ?, bet_text = ? WHERE id = ?',
            'ssi',
            [$data['sports_text'], $data['bet_text'], $id]
        );
    }

    public function getSectionsById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, trusted_by_title, about_title, about_highlight, about_body, bet_steps_title, popular_leagues_title, favorites_title
             FROM home_page_sections
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateSections(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE home_page_sections
             SET trusted_by_title = ?, about_title = ?, about_highlight = ?, about_body = ?, bet_steps_title = ?,
                 popular_leagues_title = ?, favorites_title = ?
             WHERE id = ?',
            'sssssssi',
            [
                $data['trusted_by_title'],
                $data['about_title'],
                $data['about_highlight'],
                $data['about_body'],
                $data['bet_steps_title'],
                $data['popular_leagues_title'],
                $data['favorites_title'],
                $id
            ]
        );
    }

    public function getStepById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, step_number, step_title, sort_order FROM home_page_steps WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateStep(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE home_page_steps SET step_number = ?, step_title = ?, sort_order = ? WHERE id = ?',
            'isii',
            [$data['step_number'], $data['step_title'], $data['sort_order'], $id]
        );
    }

    public function getBannerById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, home_team, away_team, days_value, hours_value, minutes_value, seconds_value,
                    days_label, hours_label, minutes_label, seconds_label, odds_first, odds_second, odds_third
             FROM home_page_banner
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateBanner(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE home_page_banner
             SET home_team = ?, away_team = ?, days_value = ?, hours_value = ?, minutes_value = ?, seconds_value = ?,
                 days_label = ?, hours_label = ?, minutes_label = ?, seconds_label = ?, odds_first = ?, odds_second = ?, odds_third = ?
             WHERE id = ?',
            'ssiiiisssssssi',
            [
                $data['home_team'],
                $data['away_team'],
                $data['days_value'],
                $data['hours_value'],
                $data['minutes_value'],
                $data['seconds_value'],
                $data['days_label'],
                $data['hours_label'],
                $data['minutes_label'],
                $data['seconds_label'],
                $data['odds_first'],
                $data['odds_second'],
                $data['odds_third'],
                $id
            ]
        );
    }

    public function getLeagueById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, league_name, stats_value, stats_label, top_scorer_label, goals_text, sort_order, is_active
             FROM home_page_leagues
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateLeague(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE home_page_leagues
             SET league_name = ?, stats_value = ?, stats_label = ?, top_scorer_label = ?, goals_text = ?, sort_order = ?, is_active = ?
             WHERE id = ?',
            'sssssiii',
            [
                $data['league_name'],
                $data['stats_value'],
                $data['stats_label'],
                $data['top_scorer_label'],
                $data['goals_text'],
                $data['sort_order'],
                $data['is_active'],
                $id
            ]
        );
    }

    public function getFavoriteById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, item_label, item_name, sort_order, is_active
             FROM home_page_favorites
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateFavorite(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE home_page_favorites
             SET item_label = ?, item_name = ?, sort_order = ?, is_active = ?
             WHERE id = ?',
            'ssiii',
            [
                $data['item_label'],
                $data['item_name'],
                $data['sort_order'],
                $data['is_active'],
                $id
            ]
        );
    }

    private function fetchAll(?mysqli_stmt $stmt): array{
        if (!$stmt) {
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        $stmt->close();
        return $rows;
    }

    private function fetchOne(string $sql, int $id): ?array{
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    private function executeUpdate(string $sql, string $types, array $values): bool{
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }
}
