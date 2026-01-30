<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../../db_connection.php';
    require_once __DIR__ . '/../assets/includes/HomepageRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../login.php');
        exit;
    }

    $db = footcast_db();
    $userId = (int) $_SESSION['user_id'];
    $homepageRepository = new HomepageRepository($db);

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

    $validTypes = ['hero', 'sections', 'step', 'banner', 'league', 'favorite'];
    if (!in_array($type, $validTypes, true) || $recordId <= 0) {
        $errors[] = 'Invalid edit request.';
    }

    if (empty($errors)) {
        switch ($type) {
            case 'hero':
                $record = $homepageRepository->getHeroById($recordId);
                break;
            case 'sections':
                $record = $homepageRepository->getSectionsById($recordId);
                break;
            case 'step':
                $record = $homepageRepository->getStepById($recordId);
                break;
            case 'banner':
                $record = $homepageRepository->getBannerById($recordId);
                break;
            case 'league':
                $record = $homepageRepository->getLeagueById($recordId);
                break;
            case 'favorite':
                $record = $homepageRepository->getFavoriteById($recordId);
                break;
        }

        if (!$record) {
            $errors[] = 'Record not found.';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
        switch ($type) {
            case 'hero':
                $sportsText = trim((string) ($_POST['sports_text'] ?? ''));
                $betText = trim((string) ($_POST['bet_text'] ?? ''));
                if ($sportsText === '' || $betText === '') {
                    $errors[] = 'Sports and Bet text are required.';
                    break;
                }
                if ($homepageRepository->updateHero($recordId, [
                    'sports_text' => $sportsText,
                    'bet_text' => $betText,
                ])) {
                    $message = 'Hero updated successfully.';
                } else {
                    $errors[] = 'Unable to update hero.';
                }
                break;
            case 'sections':
                $trustedBy = trim((string) ($_POST['trusted_by_title'] ?? ''));
                $aboutTitle = trim((string) ($_POST['about_title'] ?? ''));
                $aboutHighlight = trim((string) ($_POST['about_highlight'] ?? ''));
                $aboutBody = trim((string) ($_POST['about_body'] ?? ''));
                $betStepsTitle = trim((string) ($_POST['bet_steps_title'] ?? ''));
                $popularLeaguesTitle = trim((string) ($_POST['popular_leagues_title'] ?? ''));
                $favoritesTitle = trim((string) ($_POST['favorites_title'] ?? ''));
                if ($trustedBy === '' || $aboutTitle === '' || $aboutHighlight === '' || $aboutBody === '') {
                    $errors[] = 'All section fields are required.';
                    break;
                }
                if ($homepageRepository->updateSections($recordId, [
                    'trusted_by_title' => $trustedBy,
                    'about_title' => $aboutTitle,
                    'about_highlight' => $aboutHighlight,
                    'about_body' => $aboutBody,
                    'bet_steps_title' => $betStepsTitle,
                    'popular_leagues_title' => $popularLeaguesTitle,
                    'favorites_title' => $favoritesTitle,
                ])) {
                    $message = 'Sections updated successfully.';
                } else {
                    $errors[] = 'Unable to update sections.';
                }
                break;
            case 'step':
                $stepNumber = (int) ($_POST['step_number'] ?? 0);
                $stepTitle = trim((string) ($_POST['step_title'] ?? ''));
                $sortOrder = (int) ($_POST['sort_order'] ?? 0);
                if ($stepNumber <= 0 || $stepTitle === '') {
                    $errors[] = 'Step number and title are required.';
                    break;
                }
                if ($homepageRepository->updateStep($recordId, [
                    'step_number' => $stepNumber,
                    'step_title' => $stepTitle,
                    'sort_order' => $sortOrder,
                ])) {
                    $message = 'Step updated successfully.';
                } else {
                    $errors[] = 'Unable to update step.';
                }
                break;
            case 'banner':
                $homeTeam = trim((string) ($_POST['home_team'] ?? ''));
                $awayTeam = trim((string) ($_POST['away_team'] ?? ''));
                $daysValue = (int) ($_POST['days_value'] ?? 0);
                $hoursValue = (int) ($_POST['hours_value'] ?? 0);
                $minutesValue = (int) ($_POST['minutes_value'] ?? 0);
                $secondsValue = (int) ($_POST['seconds_value'] ?? 0);
                $daysLabel = trim((string) ($_POST['days_label'] ?? 'Days'));
                $hoursLabel = trim((string) ($_POST['hours_label'] ?? 'Hours'));
                $minutesLabel = trim((string) ($_POST['minutes_label'] ?? 'Minutes'));
                $secondsLabel = trim((string) ($_POST['seconds_label'] ?? 'Seconds'));
                $oddsFirst = trim((string) ($_POST['odds_first'] ?? ''));
                $oddsSecond = trim((string) ($_POST['odds_second'] ?? ''));
                $oddsThird = trim((string) ($_POST['odds_third'] ?? ''));
                if ($homeTeam === '' || $awayTeam === '' || $oddsFirst === '' || $oddsSecond === '' || $oddsThird === '') {
                    $errors[] = 'Banner teams and odds are required.';
                    break;
                }
                if ($homepageRepository->updateBanner($recordId, [
                    'home_team' => $homeTeam,
                    'away_team' => $awayTeam,
                    'days_value' => $daysValue,
                    'hours_value' => $hoursValue,
                    'minutes_value' => $minutesValue,
                    'seconds_value' => $secondsValue,
                    'days_label' => $daysLabel,
                    'hours_label' => $hoursLabel,
                    'minutes_label' => $minutesLabel,
                    'seconds_label' => $secondsLabel,
                    'odds_first' => $oddsFirst,
                    'odds_second' => $oddsSecond,
                    'odds_third' => $oddsThird,
                ])) {
                    $message = 'Banner updated successfully.';
                } else {
                    $errors[] = 'Unable to update banner.';
                }
                break;
            case 'league':
                $leagueName = trim((string) ($_POST['league_name'] ?? ''));
                $statsValue = trim((string) ($_POST['stats_value'] ?? ''));
                $statsLabel = trim((string) ($_POST['stats_label'] ?? ''));
                $topScorerLabel = trim((string) ($_POST['top_scorer_label'] ?? ''));
                $goalsText = trim((string) ($_POST['goals_text'] ?? ''));
                $sortOrder = (int) ($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                if ($leagueName === '' || $statsValue === '') {
                    $errors[] = 'League name and stats are required.';
                    break;
                }
                if ($homepageRepository->updateLeague($recordId, [
                    'league_name' => $leagueName,
                    'stats_value' => $statsValue,
                    'stats_label' => $statsLabel,
                    'top_scorer_label' => $topScorerLabel,
                    'goals_text' => $goalsText,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                ])) {
                    $message = 'League updated successfully.';
                } else {
                    $errors[] = 'Unable to update league.';
                }
                break;
            case 'favorite':
                $itemLabel = trim((string) ($_POST['item_label'] ?? ''));
                $itemName = trim((string) ($_POST['item_name'] ?? ''));
                $sortOrder = (int) ($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                if ($itemLabel === '' || $itemName === '') {
                    $errors[] = 'Label and name are required.';
                    break;
                }
                if ($homepageRepository->updateFavorite($recordId, [
                    'item_label' => $itemLabel,
                    'item_name' => $itemName,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                ])) {
                    $message = 'Favorite updated successfully.';
                } else {
                    $errors[] = 'Unable to update favorite.';
                }
                break;
        }
    }

    if ($record && empty($errors)) {
        switch ($type) {
            case 'hero':
                $record = $homepageRepository->getHeroById($recordId) ?? $record;
                break;
            case 'sections':
                $record = $homepageRepository->getSectionsById($recordId) ?? $record;
                break;
            case 'step':
                $record = $homepageRepository->getStepById($recordId) ?? $record;
                break;
            case 'banner':
                $record = $homepageRepository->getBannerById($recordId) ?? $record;
                break;
            case 'league':
                $record = $homepageRepository->getLeagueById($recordId) ?? $record;
                break;
            case 'favorite':
                $record = $homepageRepository->getFavoriteById($recordId) ?? $record;
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
    <title>Edit Homepage</title>
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
                        <h2>Edit Homepage</h2>
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
                            <?php if ($type === 'hero'): ?>
                                <label class="admin-form-row">
                                    <span>Sports Text</span>
                                    <input class="admin-input" type="text" name="sports_text" value="<?php echo htmlspecialchars($record['sports_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Bet Text</span>
                                    <input class="admin-input" type="text" name="bet_text" value="<?php echo htmlspecialchars($record['bet_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                            <?php elseif ($type === 'sections'): ?>
                                <label class="admin-form-row">
                                    <span>Trusted By Title</span>
                                    <input class="admin-input" type="text" name="trusted_by_title" value="<?php echo htmlspecialchars($record['trusted_by_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>About Title</span>
                                    <input class="admin-input" type="text" name="about_title" value="<?php echo htmlspecialchars($record['about_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>About Highlight</span>
                                    <input class="admin-input" type="text" name="about_highlight" value="<?php echo htmlspecialchars($record['about_highlight'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>About Body</span>
                                    <textarea class="admin-input" name="about_body" rows="4" required><?php echo htmlspecialchars($record['about_body'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </label>
                                <label class="admin-form-row">
                                    <span>Bet Steps Title</span>
                                    <input class="admin-input" type="text" name="bet_steps_title" value="<?php echo htmlspecialchars($record['bet_steps_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Popular Leagues Title</span>
                                    <input class="admin-input" type="text" name="popular_leagues_title" value="<?php echo htmlspecialchars($record['popular_leagues_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Favorites Title</span>
                                    <input class="admin-input" type="text" name="favorites_title" value="<?php echo htmlspecialchars($record['favorites_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                            <?php elseif ($type === 'step'): ?>
                                <label class="admin-form-row">
                                    <span>Step Number</span>
                                    <input class="admin-input" type="number" name="step_number" value="<?php echo htmlspecialchars((string) ($record['step_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Step Title</span>
                                    <input class="admin-input" type="text" name="step_title" value="<?php echo htmlspecialchars($record['step_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Sort Order</span>
                                    <input class="admin-input" type="number" name="sort_order" value="<?php echo htmlspecialchars((string) ($record['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                            <?php elseif ($type === 'banner'): ?>
                                <label class="admin-form-row">
                                    <span>Home Team</span>
                                    <input class="admin-input" type="text" name="home_team" value="<?php echo htmlspecialchars($record['home_team'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Away Team</span>
                                    <input class="admin-input" type="text" name="away_team" value="<?php echo htmlspecialchars($record['away_team'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Days Value</span>
                                    <input class="admin-input" type="number" name="days_value" value="<?php echo htmlspecialchars((string) ($record['days_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Hours Value</span>
                                    <input class="admin-input" type="number" name="hours_value" value="<?php echo htmlspecialchars((string) ($record['hours_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Minutes Value</span>
                                    <input class="admin-input" type="number" name="minutes_value" value="<?php echo htmlspecialchars((string) ($record['minutes_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Seconds Value</span>
                                    <input class="admin-input" type="number" name="seconds_value" value="<?php echo htmlspecialchars((string) ($record['seconds_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Days Label</span>
                                    <input class="admin-input" type="text" name="days_label" value="<?php echo htmlspecialchars($record['days_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Hours Label</span>
                                    <input class="admin-input" type="text" name="hours_label" value="<?php echo htmlspecialchars($record['hours_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Minutes Label</span>
                                    <input class="admin-input" type="text" name="minutes_label" value="<?php echo htmlspecialchars($record['minutes_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Seconds Label</span>
                                    <input class="admin-input" type="text" name="seconds_label" value="<?php echo htmlspecialchars($record['seconds_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Odds 1</span>
                                    <input class="admin-input" type="text" name="odds_first" value="<?php echo htmlspecialchars($record['odds_first'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Odds 2</span>
                                    <input class="admin-input" type="text" name="odds_second" value="<?php echo htmlspecialchars($record['odds_second'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Odds 3</span>
                                    <input class="admin-input" type="text" name="odds_third" value="<?php echo htmlspecialchars($record['odds_third'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                            <?php elseif ($type === 'league'): ?>
                                <label class="admin-form-row">
                                    <span>League Name</span>
                                    <input class="admin-input" type="text" name="league_name" value="<?php echo htmlspecialchars($record['league_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Stats Value</span>
                                    <input class="admin-input" type="text" name="stats_value" value="<?php echo htmlspecialchars($record['stats_value'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Stats Label</span>
                                    <input class="admin-input" type="text" name="stats_label" value="<?php echo htmlspecialchars($record['stats_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Top Scorer Label</span>
                                    <input class="admin-input" type="text" name="top_scorer_label" value="<?php echo htmlspecialchars($record['top_scorer_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Goals Text</span>
                                    <input class="admin-input" type="text" name="goals_text" value="<?php echo htmlspecialchars($record['goals_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Sort Order</span>
                                    <input class="admin-input" type="number" name="sort_order" value="<?php echo htmlspecialchars((string) ($record['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                                <label class="admin-form-row">
                                    <span>Active</span>
                                    <input class="admin-checkbox" type="checkbox" name="is_active" value="1" <?php echo (int) ($record['is_active'] ?? 0) === 1 ? 'checked' : ''; ?>>
                                </label>
                            <?php elseif ($type === 'favorite'): ?>
                                <label class="admin-form-row">
                                    <span>Label</span>
                                    <input class="admin-input" type="text" name="item_label" value="<?php echo htmlspecialchars($record['item_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </label>
                                <label class="admin-form-row">
                                    <span>Name</span>
                                    <input class="admin-input" type="text" name="item_name" value="<?php echo htmlspecialchars($record['item_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
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
                            <a class="admin-link" href="../homepage.php?section=<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">Back to homepage</a>
                            <button class="admin-btn admin-btn-primary" type="submit">Save Changes</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
