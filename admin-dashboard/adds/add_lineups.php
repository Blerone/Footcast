<?php
    declare(strict_types=1);

    session_start();
require_once __DIR__ . '/../../db_connection.php';

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

    $message = null;
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $homeTeam = trim((string) ($_POST['home_team'] ?? ''));
        $awayTeam = trim((string) ($_POST['away_team'] ?? ''));
        $competition = trim((string) ($_POST['competition'] ?? ''));
        $matchDateRaw = trim((string) ($_POST['match_date'] ?? ''));
        $homeLogo = trim((string) ($_POST['home_logo'] ?? ''));
        $awayLogo = trim((string) ($_POST['away_logo'] ?? ''));
        $homeFormation = trim((string) ($_POST['home_formation'] ?? ''));
        $awayFormation = trim((string) ($_POST['away_formation'] ?? ''));
        $homeCoach = trim((string) ($_POST['home_coach'] ?? ''));
        $awayCoach = trim((string) ($_POST['away_coach'] ?? ''));
        $status = trim((string) ($_POST['status'] ?? 'scheduled'));

        if ($homeTeam === '') {
            $errors[] = 'Home team is required.';
        }
        if ($awayTeam === '') {
            $errors[] = 'Away team is required.';
        }
        if ($competition === '') {
            $errors[] = 'Competition is required.';
        }
        if ($matchDateRaw === '') {
            $errors[] = 'Match date/time is required.';
        }
        $allowedStatuses = ['scheduled', 'live', 'finished'];
        if (!in_array($status, $allowedStatuses, true)) {
            $errors[] = 'Invalid status.';
        }

        $matchDate = $matchDateRaw === '' ? '' : date('Y-m-d H:i:s', strtotime($matchDateRaw));

        if (empty($errors)) {
            $stmtSave = $db->prepare(
                'INSERT INTO lineup_matches
                (home_team, away_team, competition, match_date, home_logo, away_logo, home_formation, away_formation, home_coach, away_coach, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            if ($stmtSave) {
                $stmtSave->bind_param(
                    'sssssssssss',
                    $homeTeam,
                    $awayTeam,
                    $competition,
                    $matchDate,
                    $homeLogo,
                    $awayLogo,
                    $homeFormation,
                    $awayFormation,
                    $homeCoach,
                    $awayCoach,
                    $status
                );
                $stmtSave->execute();
                $stmtSave->close();
                $message = 'Lineup added successfully.';
            } else {
                $errors[] = 'Unable to add lineup.';
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
    <title>Add Matches</title>
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
                        <h2>Add Matches</h2>
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
                            <span>Home Team</span>
                            <input class="admin-input" type="text" name="home_team" value="<?php echo htmlspecialchars($_POST['home_team'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Away Team</span>
                            <input class="admin-input" type="text" name="away_team" value="<?php echo htmlspecialchars($_POST['away_team'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Competition</span>
                            <input class="admin-input" type="text" name="competition" value="<?php echo htmlspecialchars($_POST['competition'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Match Date</span>
                            <input class="admin-input" type="datetime-local" name="match_date" value="<?php echo htmlspecialchars($_POST['match_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Home Logo URL</span>
                            <input class="admin-input" type="text" name="home_logo" value="<?php echo htmlspecialchars($_POST['home_logo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Away Logo URL</span>
                            <input class="admin-input" type="text" name="away_logo" value="<?php echo htmlspecialchars($_POST['away_logo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Home Formation</span>
                            <input class="admin-input" type="text" name="home_formation" value="<?php echo htmlspecialchars($_POST['home_formation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Away Formation</span>
                            <input class="admin-input" type="text" name="away_formation" value="<?php echo htmlspecialchars($_POST['away_formation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Home Coach</span>
                            <input class="admin-input" type="text" name="home_coach" value="<?php echo htmlspecialchars($_POST['home_coach'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Away Coach</span>
                            <input class="admin-input" type="text" name="away_coach" value="<?php echo htmlspecialchars($_POST['away_coach'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                        <label class="admin-form-row">
                            <span>Status</span>
                            <?php $statusValue = $_POST['status'] ?? 'scheduled'; ?>
                            <select class="admin-select" name="status">
                                <option value="scheduled" <?php echo $statusValue === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="live" <?php echo $statusValue === 'live' ? 'selected' : ''; ?>>Live</option>
                                <option value="finished" <?php echo $statusValue === 'finished' ? 'selected' : ''; ?>>Finished</option>
                            </select>
                        </label>
                    </div>
                    <div class="admin-form-actions">
                        <a class="admin-link" href="../lineups.php">Back to lineups</a>
                        <button class="admin-btn admin-btn-primary" type="submit">Add Matches</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
