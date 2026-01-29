<?php
declare(strict_types=1);

final class UserRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function getUsersWithStats(): array{
        $sql = "
            SELECT
                u.id,
                u.username,
                u.email,
                u.balance,
                u.created_at,
                COALESCE(sb.total_bets, 0)       AS single_bets,
                COALESCE(sb.won_bets, 0)         AS single_won,
                COALESCE(sb.lost_bets, 0)        AS single_lost,
                COALESCE(sb.pending_bets, 0)     AS single_pending,
                COALESCE(sb.total_wagered, 0)    AS single_wagered,
                COALESCE(sb.total_won, 0)        AS single_winnings,
                COALESCE(pb.total_parlays, 0)    AS parlay_bets,
                COALESCE(pb.won_parlays, 0)      AS parlay_won,
                COALESCE(pb.lost_parlays, 0)     AS parlay_lost,
                COALESCE(pb.pending_parlays, 0)  AS parlay_pending,
                COALESCE(pb.total_stake, 0)      AS parlay_wagered,
                COALESCE(pb.total_won, 0)        AS parlay_winnings
            FROM users u
            LEFT JOIN (
                SELECT
                    user_id,
                    COUNT(*) AS total_bets,
                    SUM(status = 'won') AS won_bets,
                    SUM(status = 'lost') AS lost_bets,
                    SUM(status = 'pending') AS pending_bets,
                    COALESCE(SUM(amount), 0) AS total_wagered,
                    COALESCE(SUM(CASE WHEN status = 'won' THEN potential_payout ELSE 0 END), 0) AS total_won
                FROM bets
                GROUP BY user_id
            ) sb ON sb.user_id = u.id
            LEFT JOIN (
                SELECT
                    user_id,
                    COUNT(*) AS total_parlays,
                    SUM(status = 'won') AS won_parlays,
                    SUM(status = 'lost') AS lost_parlays,
                    SUM(status = 'pending') AS pending_parlays,
                    COALESCE(SUM(stake), 0) AS total_stake,
                    COALESCE(SUM(CASE WHEN status = 'won' THEN potential_payout ELSE 0 END), 0) AS total_won
                FROM parlays
                GROUP BY user_id
            ) pb ON pb.user_id = u.id
            ORDER BY u.created_at DESC
        ";

        $result = $this->db->query($sql);
        if (!$result) {
            return [];
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $row['total_bets']     = (int) $row['single_bets'] + (int) $row['parlay_bets'];
            $row['won_bets']       = (int) $row['single_won'] + (int) $row['parlay_won'];
            $row['lost_bets']      = (int) $row['single_lost'] + (int) $row['parlay_lost'];
            $row['pending_bets']   = (int) $row['single_pending'] + (int) $row['parlay_pending'];
            $row['total_wagered']  = (float) $row['single_wagered'] + (float) $row['parlay_wagered'];
            $row['total_winnings'] = (float) $row['single_winnings'] + (float) $row['parlay_winnings'];
            $users[] = $row;
        }

        return $users;
    }

    public function getUsersBasic(): array{
        $stmt = $this->db->prepare(
            'SELECT id, username, email, balance, created_at
             FROM users
             ORDER BY created_at DESC'
        );
        if (!$stmt) {
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['total_bets'] = 0;
                $row['won_bets'] = 0;
                $row['lost_bets'] = 0;
                $row['pending_bets'] = 0;
                $row['total_wagered'] = 0.0;
                $row['total_winnings'] = 0.0;
                $users[] = $row;
            }
        }
        $stmt->close();
        return $users;
    }

    public function deleteUser(int $userId): bool{
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }
}
