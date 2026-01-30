<?php
declare(strict_types=1);

final class ContactRepository{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function create(array $data): bool{
        $stmt = $this->db->prepare(
            'INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param(
            'sssss',
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['status']
        );
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function getRecent(int $limit = 10): array{
        $limit = max(1, min(50, $limit));
        $stmt = $this->db->prepare(
            'SELECT id, name, email, subject, message, status, created_at
             FROM contact_messages
             ORDER BY created_at DESC
             LIMIT ?'
        );
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $limit);
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
