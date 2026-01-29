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

$promotionId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = null;
$errors = [];

if ($promotionId <= 0) {
    $errors[] = 'Invalid promotion ID.';
}

$promotion = empty($errors) ? $promotionsRepository->getById($promotionId) : null;
if (empty($errors) && !$promotion) {
    $errors[] = 'Promotion not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
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
        if ($promotionsRepository->update($promotionId, $payload)) {
            $message = 'Promotion updated successfully.';
        } else {
            $errors[] = 'Unable to update promotion.';
        }
    }
}

if ($promotion && empty($errors)) {
    $promotion = $promotionsRepository->getById($promotionId) ?? $promotion;
}

$db->close();

function formatDateValue(?string $value): string
{
    if (!$value) {
        return '';
    }
    return date('Y-m-d\\TH:i', strtotime($value));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Promotion</title>
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
                        <h2>Edit Promotion</h2>
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

                <?php if ($promotion): ?>
                    <form class="admin-form" method="post" action="">
                        <div class="admin-form-grid">
                            <label class="admin-form-row">
                                <span>Title</span>
                                <input class="admin-input" type="text" name="title" value="<?php echo htmlspecialchars($promotion['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Promo Code</span>
                                <input class="admin-input" type="text" name="promo_code" value="<?php echo htmlspecialchars($promotion['promo_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Tag Label</span>
                                <input class="admin-input" type="text" name="tag_label" value="<?php echo htmlspecialchars($promotion['tag_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Tag Style</span>
                                <input class="admin-input" type="text" name="tag_style" value="<?php echo htmlspecialchars($promotion['tag_style'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Icon Name</span>
                                <input class="admin-input" type="text" name="icon_name" value="<?php echo htmlspecialchars($promotion['icon_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Card Style</span>
                                <input class="admin-input" type="text" name="card_style" value="<?php echo htmlspecialchars($promotion['card_style'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Description</span>
                                <textarea class="admin-input" name="description" rows="4" required><?php echo htmlspecialchars($promotion['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </label>
                            <label class="admin-form-row">
                                <span>Sort Order</span>
                                <input class="admin-input" type="number" name="sort_order" value="<?php echo htmlspecialchars((string) ($promotion['sort_order'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Start Date</span>
                                <input class="admin-input" type="datetime-local" name="start_date" value="<?php echo htmlspecialchars(formatDateValue($promotion['start_date'] ?? null), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>End Date</span>
                                <input class="admin-input" type="datetime-local" name="end_date" value="<?php echo htmlspecialchars(formatDateValue($promotion['end_date'] ?? null), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Active</span>
                                <input class="admin-checkbox" type="checkbox" name="is_active" value="1" <?php echo (int) ($promotion['is_active'] ?? 0) === 1 ? 'checked' : ''; ?>>
                            </label>
                        </div>
                        <div class="admin-form-actions">
                            <a class="admin-link" href="../promotions.php">Back to promotions</a>
                            <button class="admin-btn admin-btn-primary" type="submit">Update Promotion</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
