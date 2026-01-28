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

    $deleteMessage = null;
    $deleteError = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
        $deleteId = (int) $_POST['delete_user_id'];
        if ($deleteId === $userId) {
            $deleteError = 'You cannot delete your own account.';
        } elseif ($deleteId > 0) {
            $stmtDelete = $db->prepare('DELETE FROM users WHERE id = ?');
            if ($stmtDelete) {
                $stmtDelete->bind_param('i', $deleteId);
                $stmtDelete->execute();
                $stmtDelete->close();
                $deleteMessage = 'User deleted successfully.';
            } else {
                $deleteError = 'Unable to delete user.';
            }
        } else {
            $deleteError = 'Invalid user ID.';
        }
    }

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
    <title>Admin - Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/users.css">
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-hero">
            <div class="admin-hero-text">
                <h1>All Users</h1>
            </div>
        </section>

        <section class="admin-section admin-section-active">
            <?php if ($deleteMessage): ?>
                <script>
                    alert('<?php echo htmlspecialchars($deleteMessage, ENT_QUOTES, 'UTF-8'); ?>');
                </script>
                <div class="admin-alert admin-alert-success">
                    <?php echo htmlspecialchars($deleteMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php elseif ($deleteError): ?>
                <script>
                    alert('<?php echo htmlspecialchars($deleteError, ENT_QUOTES, 'UTF-8'); ?>');
                </script>
                <div class="admin-alert admin-alert-error">
                    <?php echo htmlspecialchars($deleteError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Total Bets</th>
                            <th>Won</th>
                            <th>Lost</th>
                            <th>Pending</th>
                            <th>Total Wagered</th>
                            <th>Total Winnings</th>
                            <th>Joined</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="13" class="admin-empty-cell">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td data-label="ID"><?php echo (int) $u['id']; ?></td>
                                    <td data-label="Username"><?php echo htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Balance">$<?php echo number_format((float) $u['balance'], 2); ?></td>
                                    <td data-label="Total Bets"><?php echo number_format((int) $u['total_bets']); ?></td>
                                    <td data-label="Won" class="admin-pill admin-pill-won"><?php echo number_format((int) $u['won_bets']); ?></td>
                                    <td data-label="Lost" class="admin-pill admin-pill-lost"><?php echo number_format((int) $u['lost_bets']); ?></td>
                                    <td data-label="Pending" class="admin-pill admin-pill-pending"><?php echo number_format((int) $u['pending_bets']); ?></td>
                                    <td data-label="Total Wagered">$<?php echo number_format((float) $u['total_wagered'], 2); ?></td>
                                    <td data-label="Total Winnings">$<?php echo number_format((float) $u['total_winnings'], 2); ?></td>
                                    <td data-label="Joined"><?php echo htmlspecialchars(date('n/j/Y', strtotime($u['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Edit">
                                        <a class="admin-link" href="edit/edit_users.php?id=<?php echo (int) $u['id']; ?>">Edit</a>
                                    </td>
                                    <td data-label="Delete">
                                        <?php if ((int) $u['id'] === $userId): ?>
                                            <span class="admin-muted">—</span>
                                        <?php else: ?>
                                            <form method="post" action="" onsubmit="return confirm('Delete this user?');">
                                                <input type="hidden" name="delete_user_id" value="<?php echo (int) $u['id']; ?>">
                                                <button class="users-delete-btn" type="submit">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
