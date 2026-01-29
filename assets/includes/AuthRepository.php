<?php
declare(strict_types=1);

final class AuthRepository
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT id, username, email, password, role FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $user ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result ? (bool) $result->fetch_assoc() : false;
        $stmt->close();
        return $exists;
    }

    public function createUser(string $username, string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param('sss', $username, $email, $passwordHash);
        $ok = $stmt->execute();
        $insertId = $ok ? (int) $stmt->insert_id : 0;
        $stmt->close();
        return $insertId;
    }
}
