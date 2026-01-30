<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../../db_connection.php';
    require_once __DIR__ . '/../assets/includes/SportsRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../login.php');
        exit;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];
    $sportsRepository = new SportsRepository($db);

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

    $type = isset($_GET['type']) ? (string) $_GET['type'] : '';
    $recordId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $message = null;
    $errors = [];
    $record = null;

    $validTypes = ['sections', 'sport', 'league'];
    if (!in_array($type, $validTypes, true) || $recordId <= 0) {
        $errors[] = 'Invalid edit request.';
    }

    if (empty($errors)) {
        switch ($type) {
            case 'sections':
                $record = $sportsRepository->getSectionById($recordId);
                break;
            case 'sport':
                $record = $sportsRepository->getSportById($recordId);
                break;
            case 'league':
                $record = $sportsRepository->getLeagueById($recordId);
                break;
        }
        if (!$record) {
            $errors[] = 'Record not found.';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
        switch ($type) {
            case 'sections':
                $popularTitle = trim((string) ($_POST['popular_sports_title'] ?? ''));
                $leaguesTitle = trim((string) ($_POST['top_leagues_title'] ?? ''));
                $newsletterTitle = trim((string) ($_POST['newsletter_title'] ?? ''));
                $newsletterPlaceholder = trim((string) ($_POST['newsletter_placeholder'] ?? ''));
                $newsletterButton = trim((string) ($_POST['newsletter_button_text'] ?? ''));
                if ($popularTitle === '' || $leaguesTitle === '') {
                    $errors[] = 'Section titles are required.';
                    break;
                }
                if ($sportsRepository->updateSection($recordId, [
                    'popular_sports_title' => $popularTitle,
                    'top_leagues_title' => $leaguesTitle,
                    'newsletter_title' => $newsletterTitle,
                    'newsletter_placeholder' => $newsletterPlaceholder,
                    'newsletter_button_text' => $newsletterButton,
                ])) {
                    $message = 'Sections updated successfully.';
                } else {
                    $errors[] = 'Unable to update sections.';
                }
                break;
            case 'sport':
                $sportName = trim((string) ($_POST['sport_name'] ?? ''));
                $matchesCount = (int) ($_POST['matches_count'] ?? 0);
                $matchesLabel = trim((string) ($_POST['matches_label'] ?? 'matches'));
                $sortOrder = (int) ($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                if ($sportName === '') {
                    $errors[] = 'Sport name is required.';
                    break;
                }
                if ($sportsRepository->updateSport($recordId, [
                    'sport_name' => $sportName,
                    'matches_count' => $matchesCount,
                    'matches_label' => $matchesLabel,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                ])) {
                    $message = 'Sport updated successfully.';
                } else {
                    $errors[] = 'Unable to update sport.';
                }
                break;
            case 'league':
                $leagueTitle = trim((string) ($_POST['league_title'] ?? ''));
                $leagueCountry = trim((string) ($_POST['league_country'] ?? ''));
                $matchesCount = (int) ($_POST['matches_count'] ?? 0);
                $matchesLabel = trim((string) ($_POST['matches_label'] ?? 'matches'));
                $sortOrder = (int) ($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                if ($leagueTitle === '' || $leagueCountry === '') {
                    $errors[] = 'League title and country are required.';
                    break;
                }
                if ($sportsRepository->updateLeague($recordId, [
                    'league_title' => $leagueTitle,
                    'league_country' => $leagueCountry,
                    'matches_count' => $matchesCount,
                    'matches_label' => $matchesLabel,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                ])) {
                    $message = 'League updated successfully.';
                } else {
                    $errors[] = 'Unable to update league.';
                }
                break;
        }
    }

    if ($record && empty($errors)) {
        switch ($type) {
            case 'sections':
                $record = $sportsRepository->getSectionById($recordId) ?? $record;
                break;
            case 'sport':
                $record = $sportsRepository->getSportById($recordId) ?? $record;
                break;
            case 'league':
                $record = $sportsRepository->getLeagueById($recordId) ?? $record;
                break;
        }
    }

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sports</title>
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
                        <h2>Edit Sports</h2>
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

                <?php if ($record): ?>
                    <form class="admin-form" method="post" action="">
                        <div class="admin-form-grid">
                            <?php if ($type === 'sections'): ?>
                                <label class="admin-form-row">
                                    <span>Popular Sports Title</span>
                                    <input class="admin-input" type="text" name="popular_sports_title" value="<?php echo htmlspecialchars($record['popular_sports_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Top Leagues Title</span>
                                    <input class="admin-input" type="text" name="top_leagues_title" value="<?php echo htmlspecialchars($record['top_leagues_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Newsletter Title</span>
                                    <input class="admin-input" type="text" name="newsletter_title" value="<?php echo htmlspecialchars($record['newsletter_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Newsletter Placeholder</span>
                                    <input class="admin-input" type="text" name="newsletter_placeholder" value="<?php echo htmlspecialchars($record['newsletter_placeholder'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Newsletter Button</span>
                                    <input class="admin-input" type="text" name="newsletter_button_text" value="<?php echo htmlspecialchars($record['newsletter_button_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                            <?php elseif ($type === 'sport'): ?>
                                <label class="admin-form-row">
                                    <span>Sport Name</span>
                                    <input class="admin-input" type="text" name="sport_name" value="<?php echo htmlspecialchars($record['sport_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Matches Count</span>
                                    <input class="admin-input" type="number" name="matches_count" value="<?php echo htmlspecialchars((string) ($record['matches_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Matches Label</span>
                                    <input class="admin-input" type="text" name="matches_label" value="<?php echo htmlspecialchars($record['matches_label'] ?? 'matches', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Sort Order</span>
                                    <input class="admin-input" type="number" name="sort_order" value="<?php echo htmlspecialchars((string) ($record['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Active</span>
                                    <input class="admin-checkbox" type="checkbox" name="is_active" value="1" <?php echo (int) ($record['is_active'] ?? 0) === 1 ? 'checked' : ''; ?>>
                                </label>
                            <?php elseif ($type === 'league'): ?>
                                <label class="admin-form-row">
                                    <span>League Title</span>
                                    <input class="admin-input" type="text" name="league_title" value="<?php echo htmlspecialchars($record['league_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>League Country</span>
                                    <input class="admin-input" type="text" name="league_country" value="<?php echo htmlspecialchars($record['league_country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Matches Count</span>
                                    <input class="admin-input" type="number" name="matches_count" value="<?php echo htmlspecialchars((string) ($record['matches_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Matches Label</span>
                                    <input class="admin-input" type="text" name="matches_label" value="<?php echo htmlspecialchars($record['matches_label'] ?? 'matches', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Sort Order</span>
                                    <input class="admin-input" type="number" name="sort_order" value="<?php echo htmlspecialchars((string) ($record['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Active</span>
                                    <input class="admin-checkbox" type="checkbox" name="is_active" value="1" <?php echo (int) ($record['is_active'] ?? 0) === 1 ? 'checked' : ''; ?>>
                                </label>
                            <?php endif; ?>
                        </div>
                        <div class="admin-form-actions">
                            <a class="admin-link" href="../sports.php?section=<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">Back to sports</a>
                            <button class="admin-btn admin-btn-primary" type="submit">Save Changes</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
