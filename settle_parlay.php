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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $parlayId = isset($payload['parlay_id']) ? (int) $payload['parlay_id'] : 0;
    $status = isset($payload['status']) ? (string) $payload['status'] : '';
    $allowedStatuses = ['won', 'lost'];

    if ($parlayId <= 0 || !in_array($status, $allowedStatuses, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid settle request.']);
        exit;
    }

    $db = footcast_db();
    $db->begin_transaction();

    try {
        $userId = (int) $_SESSION['user_id'];
        $stmtParlay = $db->prepare('SELECT status, potential_payout FROM parlays WHERE id = ? AND user_id = ? FOR UPDATE');
        if (!$stmtParlay) {
            throw new RuntimeException('Failed to prepare parlay lookup.');
        }
        $stmtParlay->bind_param('ii', $parlayId, $userId);
        $stmtParlay->execute();
        $result = $stmtParlay->get_result();
        $parlay = $result ? $result->fetch_assoc() : null;
        $stmtParlay->close();

        if (!$parlay) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Parlay not found.']);
            $db->rollback();
            $db->close();
            exit;
        }

        if ($parlay['status'] !== 'pending') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parlay already settled.']);
            $db->rollback();
            $db->close();
            exit;
        }

        $stmtUpdate = $db->prepare('UPDATE parlays SET status = ? WHERE id = ? AND user_id = ?');
        if (!$stmtUpdate) {
            throw new RuntimeException('Failed to prepare parlay update.');
        }
        $stmtUpdate->bind_param('sii', $status, $parlayId, $userId);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        $stmtSelections = $db->prepare('UPDATE parlay_selections SET status = ? WHERE parlay_id = ?');
        if ($stmtSelections) {
            $stmtSelections->bind_param('si', $status, $parlayId);
            $stmtSelections->execute();
            $stmtSelections->close();
        }

        $newBalance = null;
        if ($status === 'won') {
            $payout = (float) $parlay['potential_payout'];
            $stmtBalance = $db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
            if (!$stmtBalance) {
                throw new RuntimeException('Failed to prepare balance update.');
            }
            $stmtBalance->bind_param('di', $payout, $userId);
            $stmtBalance->execute();
            $stmtBalance->close();

            $stmtBalanceRead = $db->prepare('SELECT balance FROM users WHERE id = ?');
            if ($stmtBalanceRead) {
                $stmtBalanceRead->bind_param('i', $userId);
                $stmtBalanceRead->execute();
                $balanceResult = $stmtBalanceRead->get_result();
                $balanceRow = $balanceResult ? $balanceResult->fetch_assoc() : null;
                $newBalance = $balanceRow ? (float) $balanceRow['balance'] : null;
                $stmtBalanceRead->close();
            }
        }

        $db->commit();
        $db->close();

        echo json_encode([
            'success' => true,
            'status' => $status,
            'new_balance' => $newBalance,
        ]);
    } catch (Throwable $error) {
        $db->rollback();
        $db->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to settle parlay.']);
    }
?>