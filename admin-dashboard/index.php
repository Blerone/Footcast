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


    $totalUsers = 0;
    $newUsers7d = 0;

    if ($res = $db->query('SELECT COUNT(*) AS total FROM users')) {
        if ($row = $res->fetch_assoc()) {
            $totalUsers = (int) $row['total'];
        }
    }
    if ($res = $db->query("SELECT COUNT(*) AS recent FROM users WHERE created_at >= (NOW() - INTERVAL 7 DAY)")) {
        if ($row = $res->fetch_assoc()) {
            $newUsers7d = (int) $row['recent'];
        }
    }

    $totalMatches = 0;
    if ($res = $db->query('SELECT COUNT(*) AS total FROM matches')) {
        if ($row = $res->fetch_assoc()) {
            $totalMatches = (int) $row['total'];
        }
    }

    $singleStats = [
        'total'   => 0,
        'pending' => 0,
        'won'     => 0,
        'lost'    => 0,
        'wagered' => 0.0,
        'payouts' => 0.0,
    ];
    if ($res = $db->query("
        SELECT
            COUNT(*) AS total,
            SUM(status = 'pending') AS pending,
            SUM(status = 'won') AS won,
            SUM(status = 'lost') AS lost,
            COALESCE(SUM(amount), 0) AS wagered,
            COALESCE(SUM(CASE WHEN status = 'won' THEN potential_payout ELSE 0 END), 0) AS payouts
        FROM bets
    ")) {
        if ($row = $res->fetch_assoc()) {
            $singleStats = [
                'total'   => (int) ($row['total'] ?? 0),
                'pending' => (int) ($row['pending'] ?? 0),
                'won'     => (int) ($row['won'] ?? 0),
                'lost'    => (int) ($row['lost'] ?? 0),
                'wagered' => (float) ($row['wagered'] ?? 0),
                'payouts' => (float) ($row['payouts'] ?? 0),
            ];
        }
    }

    $parlayStats = [
        'total'   => 0,
        'pending' => 0,
        'won'     => 0,
        'lost'    => 0,
        'wagered' => 0.0,
        'payouts' => 0.0,
    ];
    if ($res = $db->query("
        SELECT
            COUNT(*) AS total,
            SUM(status = 'pending') AS pending,
            SUM(status = 'won') AS won,
            SUM(status = 'lost') AS lost,
            COALESCE(SUM(stake), 0) AS wagered,
            COALESCE(SUM(CASE WHEN status = 'won' THEN potential_payout ELSE 0 END), 0) AS payouts
        FROM parlays
    ")) {
        if ($row = $res->fetch_assoc()) {
            $parlayStats = [
                'total'   => (int) ($row['total'] ?? 0),
                'pending' => (int) ($row['pending'] ?? 0),
                'won'     => (int) ($row['won'] ?? 0),
                'lost'    => (int) ($row['lost'] ?? 0),
                'wagered' => (float) ($row['wagered'] ?? 0),
                'payouts' => (float) ($row['payouts'] ?? 0),
            ];
        }
    }

    $totalBets    = $singleStats['total'] + $parlayStats['total'];
    $totalPending = $singleStats['pending'] + $parlayStats['pending'];
    $totalWagered = $singleStats['wagered'] + $parlayStats['wagered'];
    $totalPayouts = $singleStats['payouts'] + $parlayStats['payouts'];

    $users = [];
    $usersSql = "
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

    if ($res = $db->query($usersSql)) {
        while ($row = $res->fetch_assoc()) {
            $row['total_bets']     = (int) $row['single_bets'] + (int) $row['parlay_bets'];
            $row['won_bets']       = (int) $row['single_won'] + (int) $row['parlay_won'];
            $row['lost_bets']      = (int) $row['single_lost'] + (int) $row['parlay_lost'];
            $row['pending_bets']   = (int) $row['single_pending'] + (int) $row['parlay_pending'];
            $row['total_wagered']  = (float) $row['single_wagered'] + (float) $row['parlay_wagered'];
            $row['total_winnings'] = (float) $row['single_winnings'] + (float) $row['parlay_winnings'];
            $users[] = $row;
        }
    }

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../user-dashboard/assets/css/dashboard.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">
        <br><br>
        <section class="admin-hero">
            <div class="admin-hero-text">
                <h1>Welcome Back  
                    <span class="admin-username"><?php echo htmlspecialchars($currentUser['username'], ENT_QUOTES, 'UTF-8'); ?></span>          
                </h1>
            </div>
        </section>

        <section class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Users</div>
                <div class="admin-stat-value"><?php echo number_format($totalUsers); ?></div>
                <div class="admin-stat-sub">New (7d): <?php echo number_format($newUsers7d); ?></div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Bets</div>
                <div class="admin-stat-value"><?php echo number_format($totalBets); ?></div>
                <div class="admin-stat-sub">Pending: <?php echo number_format($totalPending); ?></div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Wagered</div>
                <div class="admin-stat-value">$<?php echo number_format($totalWagered, 2); ?></div>
                <div class="admin-stat-sub">Payouts: $<?php echo number_format($totalPayouts, 2); ?></div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Matches</div>
                <div class="admin-stat-value"><?php echo number_format($totalMatches); ?></div>
                <div class="admin-stat-sub">All tracked fixtures</div>
            </div>
        </section>
    </main>

    <script></script>
</body>
</html>
