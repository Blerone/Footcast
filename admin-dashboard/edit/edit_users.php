<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../../db_connection.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../login.php');
        exit;
    }

    $db = footcast_db();
    $adminId = (int) $_SESSION['user_id'];

    $stmtAdmin = $db->prepare('SELECT id, username, role FROM users WHERE id = ? LIMIT 1');
    if (!$stmtAdmin) {
        http_response_code(500);
        echo 'Failed to load admin.';
        exit;
    }
    $stmtAdmin->bind_param('i', $adminId);
    $stmtAdmin->execute();
    $adminResult = $stmtAdmin->get_result();
    $adminUser = $adminResult ? $adminResult->fetch_assoc() : null;
    $stmtAdmin->close();

    if (!$adminUser || ($adminUser['role'] ?? 'user') !== 'admin') {
        header('Location: ../../index.php');
        exit;
    }

    $editUserId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $editUserId = isset($_POST['id']) ? (int) $_POST['id'] : $editUserId;
    }

    $statusMessage = null;
    $statusType = null;
    $errors = [];

    if ($editUserId <= 0) {
        $errors[] = 'Missing user ID.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $balance = trim((string) ($_POST['balance'] ?? ''));
        $role = trim((string) ($_POST['role'] ?? 'user'));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '') {
            $errors[] = 'Username is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if ($balance === '' || !is_numeric($balance)) {
            $errors[] = 'Balance must be a number.';
        }
        if (!in_array($role, ['user', 'admin'], true)) {
            $errors[] = 'Invalid role selected.';
        }

        if (empty($errors)) {
            $db->begin_transaction();
            try {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmtUpdate = $db->prepare('UPDATE users SET username = ?, email = ?, balance = ?, role = ?, password = ? WHERE id = ?');
                    if (!$stmtUpdate) {
                        throw new RuntimeException('Failed to prepare user update.');
                    }
                    $balanceValue = (float) $balance;
                    $stmtUpdate->bind_param('ssdssi', $username, $email, $balanceValue, $role, $hash, $editUserId);
                } else {
                    $stmtUpdate = $db->prepare('UPDATE users SET username = ?, email = ?, balance = ?, role = ? WHERE id = ?');
                    if (!$stmtUpdate) {
                        throw new RuntimeException('Failed to prepare user update.');
                    }
                    $balanceValue = (float) $balance;
                    $stmtUpdate->bind_param('ssdsi', $username, $email, $balanceValue, $role, $editUserId);
                }

                $stmtUpdate->execute();
                $stmtUpdate->close();
                $db->commit();
                $statusMessage = 'User updated successfully.';
                $statusType = 'success';
            } catch (Throwable $error) {
                $db->rollback();
                $errors[] = 'Unable to update user.';
            }
        }
    }

    $userRow = null;
    if (empty($errors) || $editUserId > 0) {
        $stmtUser = $db->prepare('SELECT id, username, email, balance, role, created_at FROM users WHERE id = ? LIMIT 1');
        if ($stmtUser) {
            $stmtUser->bind_param('i', $editUserId);
            $stmtUser->execute();
            $userResult = $stmtUser->get_result();
            $userRow = $userResult ? $userResult->fetch_assoc() : null;
            $stmtUser->close();
        }
        if (!$userRow && empty($errors)) {
            $errors[] = 'User not found.';
        }
    }

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../user-dashboard/assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include("../assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-hero">
            <div class="admin-hero-text">
                <h1>Edit User</h1>
                <p>Update account details and role.</p>
            </div>
        </section>

        <section class="admin-section admin-section-active">
            <?php if (!empty($errors)): ?>
                <div class="admin-alert admin-alert-error">
                    <?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php elseif ($statusMessage): ?>
                <div class="admin-alert admin-alert-success">
                    <?php echo htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($userRow): ?>
                <form class="admin-form" method="post" action="">
                    <input type="hidden" name="id" value="<?php echo (int) $userRow['id']; ?>">
                    <div class="admin-form-grid">
                        <label class="admin-form-row">
                            <span>Username</span>
                            <input class="admin-input" type="text" name="username" value="<?php echo htmlspecialchars($userRow['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Email</span>
                            <input class="admin-input" type="email" name="email" value="<?php echo htmlspecialchars($userRow['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Balance</span>
                            <input class="admin-input" type="number" step="0.01" name="balance" value="<?php echo htmlspecialchars((string) $userRow['balance'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Role</span>
                            <select class="admin-select" name="role">
                                <option value="user" <?php echo ($userRow['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo ($userRow['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </label>
                        <label class="admin-form-row">
                            <span>New Password</span>
                            <input class="admin-input" type="password" name="password" placeholder="Leave blank to keep current">
                        </label>
                    </div>
                    <div class="admin-form-actions">
                        <a class="admin-link" href="../users.php">Back to users</a>
                        <button class="admin-btn admin-btn-primary" type="submit">Save Changes</button>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
