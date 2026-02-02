<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/assets/includes/ContactRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$db = footcast_db();
$userId = (int) $_SESSION['user_id'];

$stmtUser = $db->prepare('SELECT id, username, role FROM users WHERE id = ? LIMIT 1');
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

$contactRepository = new ContactRepository($db);
$messages = $contactRepository->getAll();
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Messages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/users.css">
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-hero">
            <div class="admin-hero-text">
                <h1>Contact Messages</h1>
                <p>Everyone who filled out the contact form</p>
            </div>
        </section>

        <section class="admin-section admin-section-active">
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="7" class="admin-empty-cell">No contact messages yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td data-label="ID"><?php echo (int) $msg['id']; ?></td>
                                    <td data-label="Name"><?php echo htmlspecialchars($msg['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($msg['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Subject"><?php echo htmlspecialchars($msg['subject'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Message"><?php echo htmlspecialchars($msg['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Status"><?php echo htmlspecialchars($msg['status'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Date"><?php echo htmlspecialchars(date('n/j/Y g:i A', strtotime($msg['created_at'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
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
