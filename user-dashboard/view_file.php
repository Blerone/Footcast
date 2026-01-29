<?php
    declare(strict_types=1);

    session_start();
    include("../../db_connection.php");
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];

    $parlays = [];
    $stmt = $db->prepare('SELECT id, stake, total_odds, potential_payout, status, created_at FROM parlays WHERE user_id = ? ORDER BY created_at DESC');
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $parlays[] = $row;
            }
        }
        $stmt->close();
    }

    $selectionsByParlay = [];
    if (!empty($parlays)) {
        $ids = array_map(static fn($row) => (int) $row['id'], $parlays);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmtSel = $db->prepare(
            "SELECT parlay_id, match_id, bet_type, bet_value, bet_category, odds, status FROM parlay_selections WHERE parlay_id IN ({$placeholders}) ORDER BY id ASC"
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

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Parlays</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <link rel="stylesheet" href="./assets/css/bets.css">
</head>
<body>
    <?php
        include("./assets/php/nav.php")
    ?>
    <div class="parlay-wrapper">
        <h1>Your Parlays</h1>
        <?php if (empty($parlays)): ?>
            <p>No parlays placed yet.</p>
        <?php else: ?>
            <?php foreach ($parlays as $parlay): ?>
                <div class="parlay-card">
                    <div class="parlay-header">
                        <div>
                            <strong>Parlay 
                            <div class="parlay-meta">
                                Stake: <?php echo htmlspecialchars($parlay['stake'], ENT_QUOTES, 'UTF-8'); ?> |
                                Odds: <?php echo htmlspecialchars($parlay['total_odds'], ENT_QUOTES, 'UTF-8'); ?> |
                                Payout: <?php echo htmlspecialchars($parlay['potential_payout'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                        <div class="parlay-status"><?php echo htmlspecialchars($parlay['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <?php if (!empty($selectionsByParlay[$parlay['id']])): ?>
                        <ul class="parlay-list">
                            <?php foreach ($selectionsByParlay[$parlay['id']] as $selection): ?>
                                <li>
                                    <?php echo htmlspecialchars($selection['bet_type'], ENT_QUOTES, 'UTF-8'); ?> -
                                    <?php echo htmlspecialchars($selection['bet_value'], ENT_QUOTES, 'UTF-8'); ?>
                                    (<?php echo htmlspecialchars($selection['odds'], ENT_QUOTES, 'UTF-8'); ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div
    <script src="assets/js/nav.js"></script>
</body>
</html>
