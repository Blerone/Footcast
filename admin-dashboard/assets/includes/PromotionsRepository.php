<?php
declare(strict_types=1);

final class PromotionsRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function getAll(): array{
        $stmt = $this->db->prepare(
            'SELECT id, title, description, promo_code, tag_label, tag_style, icon_name, card_style, is_active, sort_order, start_date, end_date, updated_at
             FROM promotions
             ORDER BY sort_order ASC, updated_at DESC'
        );
        return $this->fetchAll($stmt);
    }

    public function getById(int $id): ?array{
        $stmt = $this->db->prepare(
            'SELECT id, title, description, promo_code, tag_label, tag_style, icon_name, card_style, is_active,
                    sort_order, start_date, end_date
             FROM promotions
             WHERE id = ?
             LIMIT 1'
        );
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

    public function create(array $data): bool{
        $stmt = $this->db->prepare(
            'INSERT INTO promotions
             (title, description, promo_code, tag_label, tag_style, icon_name, card_style, is_active, sort_order, start_date, end_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'sssssssiiss',
            $data['title'],
            $data['description'],
            $data['promo_code'],
            $data['tag_label'],
            $data['tag_style'],
            $data['icon_name'],
            $data['card_style'],
            $data['is_active'],
            $data['sort_order'],
            $data['start_date'],
            $data['end_date']
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function update(int $id, array $data): bool{
        $stmt = $this->db->prepare(
            'UPDATE promotions
             SET title = ?, description = ?, promo_code = ?, tag_label = ?, tag_style = ?, icon_name = ?, card_style = ?,
                 is_active = ?, sort_order = ?, start_date = ?, end_date = ?
             WHERE id = ?'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'sssssssiissi',
            $data['title'],
            $data['description'],
            $data['promo_code'],
            $data['tag_label'],
            $data['tag_style'],
            $data['icon_name'],
            $data['card_style'],
            $data['is_active'],
            $data['sort_order'],
            $data['start_date'],
            $data['end_date'],
            $id
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function delete(int $id): bool{
        $stmt = $this->db->prepare('DELETE FROM promotions WHERE id = ?');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    private function fetchAll(?mysqli_stmt $stmt): array {
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
