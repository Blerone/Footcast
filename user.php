<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$db = footcast_db();
$stmt = $db->prepare('SELECT id, username, balance FROM users WHERE id = ? LIMIT 1');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load user.']);
    $db->close();
    exit;
}

$userId = (int) $_SESSION['user_id'];
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();
$db->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

echo json_encode(['success' => true, 'user' => $user]);
