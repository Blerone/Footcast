<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../../db_connection.php';
    require_once __DIR__ . '/../assets/includes/PromotionsRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../login.php');
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
        header('Location: ../../index.php');
        exit;
    }

    $message = null;
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $promoCode = trim((string) ($_POST['promo_code'] ?? ''));
        $tagLabel = trim((string) ($_POST['tag_label'] ?? ''));
        $tagStyle = trim((string) ($_POST['tag_style'] ?? ''));
        $iconName = trim((string) ($_POST['icon_name'] ?? ''));
        $cardStyle = trim((string) ($_POST['card_style'] ?? ''));
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrderRaw = trim((string) ($_POST['sort_order'] ?? '0'));
        $startDateRaw = trim((string) ($_POST['start_date'] ?? ''));
        $endDateRaw = trim((string) ($_POST['end_date'] ?? ''));

        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        $sortOrder = $sortOrderRaw === '' ? 0 : (int) $sortOrderRaw;
        $startDate = $startDateRaw === '' ? null : date('Y-m-d H:i:s', strtotime($startDateRaw));
        $endDate = $endDateRaw === '' ? null : date('Y-m-d H:i:s', strtotime($endDateRaw));

        if (empty($errors)) {
            $payload = [
                'title' => $title,
                'description' => $description,
                'promo_code' => $promoCode,
                'tag_label' => $tagLabel,
                'tag_style' => $tagStyle,
                'icon_name' => $iconName,
                'card_style' => $cardStyle,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
            if ($promotionsRepository->create($payload)) {
                $message = 'Promotion added successfully.';
            } else {
                $errors[] = 'Unable to add promotion.';
            }
        }
    }

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Promotion</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/lineups.css">
</head>
<body>
    <?php include("../assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-section admin-section-active">
            <div class="admin-form-center">
                <div class="admin-section-header">
                    <div>
                        <h2>Add Promotion</h2>
                    </div>
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

                <form class="admin-form" method="post" action="">
                    <div class="admin-form-grid">
                        <label class="admin-form-row">
                            <span>Title</span>
                            <input class="admin-input" type="text" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Promo Code</span>
                            <input class="admin-input" type="text" name="promo_code" value="<?php echo htmlspecialchars($_POST['promo_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Tag Label</span>
                            <input class="admin-input" type="text" name="tag_label" value="<?php echo htmlspecialchars($_POST['tag_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Tag Style</span>
                            <input class="admin-input" type="text" name="tag_style" value="<?php echo htmlspecialchars($_POST['tag_style'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Icon Name</span>
                            <input class="admin-input" type="text" name="icon_name" value="<?php echo htmlspecialchars($_POST['icon_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Card Style</span>
                            <input class="admin-input" type="text" name="card_style" value="<?php echo htmlspecialchars($_POST['card_style'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Description</span>
                            <textarea class="admin-input" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </label>
                        <label class="admin-form-row">
                            <span>Sort Order</span>
                            <input class="admin-input" type="number" name="sort_order" value="<?php echo htmlspecialchars((string) ($_POST['sort_order'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Start Date</span>
                            <input class="admin-input" type="datetime-local" name="start_date" value="<?php echo htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>End Date</span>
                            <input class="admin-input" type="datetime-local" name="end_date" value="<?php echo htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Active</span>
                            <input class="admin-checkbox" type="checkbox" name="is_active" value="1" <?php echo isset($_POST['is_active']) ? 'checked' : 'checked'; ?>>
                        </label>
                    </div>
                    <div class="admin-form-actions">
                        <a class="admin-link" href="../promotions.php">Back to promotions</a>
                        <button class="admin-btn admin-btn-primary" type="submit">Add Promotion</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
