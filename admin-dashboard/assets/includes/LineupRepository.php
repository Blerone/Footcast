<?php
declare(strict_types=1);

final class LineupRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function getMatches(): array{
        $stmt = $this->db->prepare(
            'SELECT id, home_team, away_team, competition, match_date, status
             FROM lineup_matches
             ORDER BY match_date DESC'
        );
        return $this->fetchAll($stmt);
    }

    public function getPlayersByMatch(int $matchId): array{
        $stmt = $this->db->prepare(
            'SELECT id, team_side, player_name, player_number, position_label, pos_x, pos_y, is_starter
             FROM lineup_players
             WHERE lineup_match_id = ?
             ORDER BY is_starter DESC, team_side ASC, id ASC'
        );
        if ($stmt) {
            $stmt->bind_param('i', $matchId);
        }
        return $this->fetchAll($stmt);
    }

    public function getSubstitutionsByMatch(int $matchId): array{
        $stmt = $this->db->prepare(
            'SELECT id, team_side, minute, player_out, player_in
             FROM lineup_substitutions
             WHERE lineup_match_id = ?
             ORDER BY minute ASC, id ASC'
        );
        if ($stmt) {
            $stmt->bind_param('i', $matchId);
        }
        return $this->fetchAll($stmt);
    }

    public function getInjuriesByMatch(int $matchId): array{
        $stmt = $this->db->prepare(
            'SELECT id, team_side, player_name, reason, type
             FROM lineup_injuries
             WHERE lineup_match_id = ?
             ORDER BY id ASC'
        );
        if ($stmt) {
            $stmt->bind_param('i', $matchId);
        }
        return $this->fetchAll($stmt);
    }

    public function createMatch(array $data): bool{
        $stmt = $this->db->prepare(
            'INSERT INTO lineup_matches
             (home_team, away_team, competition, match_date, home_logo, away_logo, home_formation, away_formation, home_coach, away_coach, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'sssssssssss',
            $data['home_team'],
            $data['away_team'],
            $data['competition'],
            $data['match_date'],
            $data['home_logo'],
            $data['away_logo'],
            $data['home_formation'],
            $data['away_formation'],
            $data['home_coach'],
            $data['away_coach'],
            $data['status']
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function createPlayer(array $data): bool{
        $stmt = $this->db->prepare(
            'INSERT INTO lineup_players
             (lineup_match_id, team_side, player_name, player_number, position_label, pos_x, pos_y, is_starter)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'ississdi',
            $data['lineup_match_id'],
            $data['team_side'],
            $data['player_name'],
            $data['player_number'],
            $data['position_label'],
            $data['pos_x'],
            $data['pos_y'],
            $data['is_starter']
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function createSubstitution(array $data): bool{
        $stmt = $this->db->prepare(
            'INSERT INTO lineup_substitutions
             (lineup_match_id, team_side, minute, player_out, player_in)
             VALUES (?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'isiss',
            $data['lineup_match_id'],
            $data['team_side'],
            $data['minute'],
            $data['player_out'],
            $data['player_in']
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function createInjury(array $data): bool{
        $stmt = $this->db->prepare(
            'INSERT INTO lineup_injuries
             (lineup_match_id, team_side, player_name, reason, type)
             VALUES (?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'issss',
            $data['lineup_match_id'],
            $data['team_side'],
            $data['player_name'],
            $data['reason'],
            $data['type']
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function deleteMatch(int $matchId): bool{
        return $this->executeDelete('DELETE FROM lineup_matches WHERE id = ?', $matchId);
    }

    public function deletePlayer(int $playerId): bool{
        return $this->executeDelete('DELETE FROM lineup_players WHERE id = ?', $playerId);
    }

    public function deleteSubstitution(int $subId): bool{
        return $this->executeDelete('DELETE FROM lineup_substitutions WHERE id = ?', $subId);
    }

    public function deleteInjury(int $injuryId): bool{
        return $this->executeDelete('DELETE FROM lineup_injuries WHERE id = ?', $injuryId);
    }

    private function executeDelete(string $sql, int $id): bool{
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
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
}
