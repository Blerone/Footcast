<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../db_connection.php';
    require_once __DIR__ . '/assets/includes/PromotionsRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];
    $promotionsRepository = new PromotionsRepository($db);

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

    $message = null;
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'delete_promotion') {
            $deleteId = (int) ($_POST['promotion_id'] ?? 0);
            if ($deleteId > 0) {
                if ($promotionsRepository->delete($deleteId)) {
                    $message = 'Promotion deleted successfully.';
                } else {
                    $errors[] = 'Unable to delete promotion.';
                }
            } else {
                $errors[] = 'Invalid promotion ID.';
            }
        }
    }

    $promotions = $promotionsRepository->getAll();

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Promotions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/lineups.css">
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-section admin-section-active">
            <div class="admin-section-header">
                <div>
                    <h2>Promotions</h2>
                </div>
                <a class="add-button" href="adds/add_promotions.php">Add Promotion</a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="admin-alert admin-alert-error">
                    <?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php elseif ($message): ?>
                <div class="admin-alert admin-alert-success">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Code</th>
                            <th>Tag</th>
                            <th>Active</th>
                            <th>Order</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($promotions)): ?>
                            <tr>
                                <td colspan="10" class="admin-empty-cell">No promotions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promotions as $promo): ?>
                                <tr>
                                    <td data-label="ID"><?php echo (int) $promo['id']; ?></td>
                                    <td data-label="Title"><?php echo htmlspecialchars($promo['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Code"><?php echo htmlspecialchars($promo['promo_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Tag"><?php echo htmlspecialchars($promo['tag_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Active"><?php echo (int) ($promo['is_active'] ?? 0) === 1 ? 'Yes' : 'No'; ?></td>
                                    <td data-label="Order"><?php echo htmlspecialchars((string) ($promo['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Start"><?php echo $promo['start_date'] ? htmlspecialchars(date('n/j/Y', strtotime($promo['start_date'])), ENT_QUOTES, 'UTF-8') : '—'; ?></td>
                                    <td data-label="End"><?php echo $promo['end_date'] ? htmlspecialchars(date('n/j/Y', strtotime($promo['end_date'])), ENT_QUOTES, 'UTF-8') : '—'; ?></td>
                                    <td data-label="Edit">
                                        <a class="admin-link" href="edit/edit_promotions.php?id=<?php echo (int) $promo['id']; ?>">Edit</a>
                                    </td>
                                    <td data-label="Delete">
                                        <form method="post" action="" onsubmit="return confirm('Delete this promotion?');">
                                            <input type="hidden" name="action" value="delete_promotion">
                                            <input type="hidden" name="promotion_id" value="<?php echo (int) $promo['id']; ?>">
                                            <button class="users-delete-btn" type="submit">Delete</button>
                                        </form>
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
