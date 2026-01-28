<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../db_connection.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];

    $stmtUser = $db->prepare('SELECT id, username, email, role FROM users WHERE id = ? LIMIT 1');
    if (!$stmtUser) {
        http_response_code(500);
        echo 'Failed to load user.';
        exit;
    }
    $stmtUser->bind_param('i', $userId);
    $stmtUser->execute();
    $userResult = $stmtUser->get_result();
    $currentUser = $userResult ? $userResult->fetch_assoc() : null;
    $stmtUser->close();

    if (!$currentUser || ($currentUser['role'] ?? 'user') !== 'admin') {
        header('Location: ../index.php');
        exit;
    }

    $bets = [];
    $betsSql = "
        SELECT
            ps.id,
            u.username,
            CONCAT(m.home_team, ' vs ', m.away_team) AS match_name,
            ps.bet_type,
            ps.odds,
            ps.status,
            p.stake,
            p.potential_payout,
            p.created_at
        FROM parlay_selections ps
        JOIN parlays p ON p.id = ps.parlay_id
        JOIN users u ON u.id = p.user_id
        JOIN matches m ON m.id = ps.match_id
        ORDER BY p.created_at DESC, ps.id DESC
        LIMIT 200
    ";

    if ($res = $db->query($betsSql)) {
        while ($row = $res->fetch_assoc()) {
            $bets[] = $row;
        }
    }

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bets</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/bets.css">
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">

        <section class="admin-section admin-section-active">
            <div class="admin-section-header">
                <h2>All Bets</h2>
            </div>
            <div class="admin-bet-filters">
                <button class="admin-filter-btn active" data-status="all">All</button>
                <button class="admin-filter-btn" data-status="pending">Pending</button>
                <button class="admin-filter-btn" data-status="won">Won</button>
                <button class="admin-filter-btn" data-status="lost">Lost</button>
            </div>
            <div class="admin-table-wrapper">
                <table class="admin-table" id="admin-bets-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Match</th>
                            <th>Bet Type</th>
                            <th>Stake (Parlay)</th>
                            <th>Odds</th>
                            <th>Potential Payout</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bets)): ?>
                            <tr>
                                <td colspan="9" class="admin-empty-cell">No bets found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bets as $b): ?>
                                <?php $status = strtolower((string) ($b['status'] ?? 'pending')); ?>
                                <tr data-status="<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>">
                                    <td data-label="ID"><?php echo (int) $b['id']; ?></td>
                                    <td data-label="User"><?php echo htmlspecialchars($b['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Match"><?php echo htmlspecialchars($b['match_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Bet Type"><?php echo htmlspecialchars($b['bet_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Stake">$<?php echo number_format((float) $b['stake'], 2); ?></td>
                                    <td data-label="Odds"><?php echo number_format((float) $b['odds'], 2); ?></td>
                                    <td data-label="Potential Payout">$<?php echo number_format((float) $b['potential_payout'], 2); ?></td>
                                    <td data-label="Status">
                                        <span class="admin-pill admin-pill-<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td data-label="Date"><?php echo htmlspecialchars(date('n/j/Y, g:i A', strtotime($b['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const filterButtons = document.querySelectorAll('.admin-filter-btn');
        const betRows = document.querySelectorAll('#admin-bets-table tbody tr[data-status]');

        filterButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const status = btn.getAttribute('data-status');

                filterButtons.forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');

                betRows.forEach((row) => {
                    const rowStatus = row.getAttribute('data-status');
                    const shouldShow = status === 'all' || status === rowStatus;
                    row.style.display = shouldShow ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html>
