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

    $matchId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $message = null;
    $errors = [];

    if ($matchId <= 0) {
        $errors[] = 'Invalid lineup match ID.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
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
                'UPDATE lineup_matches
                SET home_team = ?, away_team = ?, competition = ?, match_date = ?, home_logo = ?, away_logo = ?,
                    home_formation = ?, away_formation = ?, home_coach = ?, away_coach = ?, status = ?
                WHERE id = ?'
            );
            if ($stmtSave) {
                $stmtSave->bind_param(
                    'sssssssssssi',
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
                    $status,
                    $matchId
                );
                $stmtSave->execute();
                $stmtSave->close();
                $message = 'Lineup updated successfully.';
            } else {
                $errors[] = 'Unable to update lineup.';
            }
        }
    }

    $match = null;
    if (empty($errors)) {
        $stmtMatch = $db->prepare(
            'SELECT id, home_team, away_team, competition, match_date, home_logo, away_logo, home_formation, away_formation, home_coach, away_coach, status
            FROM lineup_matches
            WHERE id = ?
            LIMIT 1'
        );
        if ($stmtMatch) {
            $stmtMatch->bind_param('i', $matchId);
            $stmtMatch->execute();
            $result = $stmtMatch->get_result();
            $match = $result ? $result->fetch_assoc() : null;
            $stmtMatch->close();
        }
        if (!$match) {
            $errors[] = 'Lineup match not found.';
        }
    }

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lineup</title>
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
                        <h2>Edit Lineup</h2>
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

                <?php if ($match): ?>
                    <form class="admin-form" method="post" action="">
                        <div class="admin-form-grid">
                            <label class="admin-form-row">
                                <span>Home Team</span>
                                <input class="admin-input" type="text" name="home_team" value="<?php echo htmlspecialchars($match['home_team'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Away Team</span>
                                <input class="admin-input" type="text" name="away_team" value="<?php echo htmlspecialchars($match['away_team'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Competition</span>
                                <input class="admin-input" type="text" name="competition" value="<?php echo htmlspecialchars($match['competition'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Match Date</span>
                                <input class="admin-input" type="datetime-local" name="match_date" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($match['match_date'])), ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Home Logo URL</span>
                                <input class="admin-input" type="text" name="home_logo" value="<?php echo htmlspecialchars($match['home_logo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Away Logo URL</span>
                                <input class="admin-input" type="text" name="away_logo" value="<?php echo htmlspecialchars($match['away_logo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Home Formation</span>
                                <input class="admin-input" type="text" name="home_formation" value="<?php echo htmlspecialchars($match['home_formation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Away Formation</span>
                                <input class="admin-input" type="text" name="away_formation" value="<?php echo htmlspecialchars($match['away_formation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Home Coach</span>
                                <input class="admin-input" type="text" name="home_coach" value="<?php echo htmlspecialchars($match['home_coach'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Away Coach</span>
                                <input class="admin-input" type="text" name="away_coach" value="<?php echo htmlspecialchars($match['away_coach'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Status</span>
                                <select class="admin-select" name="status">
                                    <option value="scheduled" <?php echo $match['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="live" <?php echo $match['status'] === 'live' ? 'selected' : ''; ?>>Live</option>
                                    <option value="finished" <?php echo $match['status'] === 'finished' ? 'selected' : ''; ?>>Finished</option>
                                </select>
                            </label>
                        </div>
                        <div class="admin-form-actions">
                            <a class="admin-link" href="../lineups.php">Back to lineups</a>
                            <button class="admin-btn admin-btn-primary" type="submit">Update Lineup</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
