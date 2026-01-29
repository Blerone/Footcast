<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../db_connection.php';
    require_once __DIR__ . '/assets/includes/UserRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];
    $userRepository = new UserRepository($db);

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
            if ($userRepository->deleteUser($deleteId)) {
                $deleteMessage = 'User deleted successfully.';
            } else {
                $deleteError = 'Unable to delete user.';
            }
        } else {
            $deleteError = 'Invalid user ID.';
        }
    }

    $users = $userRepository->getUsersWithStats();
    if (empty($users)) {
        $users = $userRepository->getUsersBasic();
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
