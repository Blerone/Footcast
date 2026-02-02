<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/football_api.php';
require_once __DIR__ . '/assets/includes/ParlayService.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$statusFilter = $_GET['status'] ?? null;
$allowedStatuses = ['pending', 'won', 'lost', 'cancelled'];
if ($statusFilter !== null && !in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = null;
}

$db = Database::getConnection();
$parlayService = new ParlayService($db);
    $userId = (int) $_SESSION['user_id'];

    $parlays = [];
    if ($statusFilter) {
        $stmt = $db->prepare('SELECT id, user_id, stake, total_odds, potential_payout, status, created_at FROM parlays WHERE user_id = ? AND status = ? ORDER BY created_at DESC');
        if ($stmt) {
            $stmt->bind_param('is', $userId, $statusFilter);
        }
    } else {
        $stmt = $db->prepare('SELECT id, user_id, stake, total_odds, potential_payout, status, created_at FROM parlays WHERE user_id = ? ORDER BY created_at DESC');
        if ($stmt) {
            $stmt->bind_param('i', $userId);
        }
    }

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to load parlays.']);
        Database::closeConnection($db);
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $parlays[] = $row;
        }
    }
    $stmt->close();

    $selectionsByParlay = [];
    if (!empty($parlays)) {
        $ids = array_map(static fn($row) => (int) $row['id'], $parlays);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmtSel = $db->prepare(
            "SELECT ps.parlay_id, ps.match_id, ps.bet_type, ps.bet_value, ps.bet_category, ps.odds, ps.status,
                    m.api_fixture_id, m.home_team, m.away_team, m.match_date, m.status AS match_status,
                    m.home_score, m.away_score, m.home_score_1h, m.away_score_1h
            FROM parlay_selections ps
            LEFT JOIN matches m ON m.id = ps.match_id
            WHERE ps.parlay_id IN ({$placeholders})
            ORDER BY ps.id ASC"
        );
        if ($stmtSel) {
            $stmtSel->bind_param($types, ...$ids);
            $stmtSel->execute();
            $result = $stmtSel->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $selectionsByParlay[$row['parlay_id']][] = $row;
                }
            }
            $stmtSel->close();
        }
    }

    $parlayService->autoSettleParlays($parlays, $selectionsByParlay);

    if ($statusFilter) {
        $parlays = array_values(array_filter(
            $parlays,
            static fn($parlay) => ($parlay['status'] ?? '') === $statusFilter
        ));
        $validIds = array_flip(array_map(static fn($parlay) => (int) $parlay['id'], $parlays));
        $selectionsByParlay = array_intersect_key($selectionsByParlay, $validIds);
    }

    Database::closeConnection($db);

    foreach ($parlays as &$parlay) {
        $parlay['selections'] = $selectionsByParlay[$parlay['id']] ?? [];
    }
    unset($parlay);

    echo json_encode(['success' => true, 'parlays' => $parlays]);
?>