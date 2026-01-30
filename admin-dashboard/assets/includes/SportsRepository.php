<?php
declare(strict_types=1);

final class SportsRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function getSections(): array{
        $stmt = $this->db->prepare(
            'SELECT id, popular_sports_title, top_leagues_title, newsletter_title, newsletter_placeholder, newsletter_button_text, updated_at
             FROM sports_page_sections
             ORDER BY id DESC'
        );
        return $this->fetchAll($stmt);
    }

    public function getSports(): array{
        $stmt = $this->db->prepare(
            'SELECT id, sport_name, matches_count, matches_label, sort_order, is_active
             FROM sports_page_sports
             ORDER BY sort_order ASC, id ASC'
        );
        return $this->fetchAll($stmt);
    }

    public function getLeagues(): array{
        $stmt = $this->db->prepare(
            'SELECT id, league_title, league_country, matches_count, matches_label, sort_order, is_active
             FROM sports_page_leagues
             ORDER BY sort_order ASC, id ASC'
        );
        return $this->fetchAll($stmt);
    }

    public function getSectionById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, popular_sports_title, top_leagues_title, newsletter_title, newsletter_placeholder, newsletter_button_text
             FROM sports_page_sections
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateSection(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE sports_page_sections
             SET popular_sports_title = ?, top_leagues_title = ?, newsletter_title = ?, newsletter_placeholder = ?, newsletter_button_text = ?
             WHERE id = ?',
            'sssssi',
            [
                $data['popular_sports_title'],
                $data['top_leagues_title'],
                $data['newsletter_title'],
                $data['newsletter_placeholder'],
                $data['newsletter_button_text'],
                $id
            ]
        );
    }

    public function getSportById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, sport_name, matches_count, matches_label, sort_order, is_active
             FROM sports_page_sports
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateSport(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE sports_page_sports
             SET sport_name = ?, matches_count = ?, matches_label = ?, sort_order = ?, is_active = ?
             WHERE id = ?',
            'sisiii',
            [
                $data['sport_name'],
                $data['matches_count'],
                $data['matches_label'],
                $data['sort_order'],
                $data['is_active'],
                $id
            ]
        );
    }

    public function getLeagueById(int $id): ?array{
        return $this->fetchOne(
            'SELECT id, league_title, league_country, matches_count, matches_label, sort_order, is_active
             FROM sports_page_leagues
             WHERE id = ? LIMIT 1',
            $id
        );
    }

    public function updateLeague(int $id, array $data): bool{
        return $this->executeUpdate(
            'UPDATE sports_page_leagues
             SET league_title = ?, league_country = ?, matches_count = ?, matches_label = ?, sort_order = ?, is_active = ?
             WHERE id = ?',
            'ssissii',
            [
                $data['league_title'],
                $data['league_country'],
                $data['matches_count'],
                $data['matches_label'],
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
