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

$subId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = null;
$errors = [];
$selectedMatchId = isset($_GET['match_id']) ? (int) $_GET['match_id'] : 0;

if ($subId <= 0) {
    $errors[] = 'Invalid substitution ID.';
}

$lineups = [];
$stmtList = $db->prepare(
    'SELECT id, home_team, away_team
     FROM lineup_matches
     ORDER BY match_date DESC'
);
if ($stmtList) {
    $stmtList->execute();
    $result = $stmtList->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lineups[] = $row;
        }
    }
    $stmtList->close();
}

$sub = null;
if (empty($errors)) {
    $stmtSub = $db->prepare(
        'SELECT id, lineup_match_id, team_side, minute, player_out, player_in
         FROM lineup_substitutions
         WHERE id = ?
         LIMIT 1'
    );
    if ($stmtSub) {
        $stmtSub->bind_param('i', $subId);
        $stmtSub->execute();
        $result = $stmtSub->get_result();
        $sub = $result ? $result->fetch_assoc() : null;
        $stmtSub->close();
    }
    if (!$sub) {
        $errors[] = 'Substitution not found.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $lineupMatchId = (int) ($_POST['lineup_match_id'] ?? 0);
    $teamSide = trim((string) ($_POST['team_side'] ?? 'home'));
    $minute = trim((string) ($_POST['minute'] ?? ''));
    $playerOut = trim((string) ($_POST['player_out'] ?? ''));
    $playerIn = trim((string) ($_POST['player_in'] ?? ''));

    if ($lineupMatchId <= 0) {
        $errors[] = 'Select a lineup match.';
    }
    if (!in_array($teamSide, ['home', 'away'], true)) {
        $errors[] = 'Invalid team side.';
    }
    if ($playerOut === '' || $playerIn === '') {
        $errors[] = 'Substitution players are required.';
    }

    if (empty($errors)) {
        $minuteValue = $minute === '' ? null : (int) $minute;
        $stmtSave = $db->prepare(
            'UPDATE lineup_substitutions
             SET lineup_match_id = ?, team_side = ?, minute = ?, player_out = ?, player_in = ?
             WHERE id = ?'
        );
        if ($stmtSave) {
            $stmtSave->bind_param('isissi', $lineupMatchId, $teamSide, $minuteValue, $playerOut, $playerIn, $subId);
            $stmtSave->execute();
            $stmtSave->close();
            $message = 'Substitution updated successfully.';
        } else {
            $errors[] = 'Unable to update substitution.';
        }
    }
}

if ($sub && empty($errors)) {
    $stmtSub = $db->prepare(
        'SELECT id, lineup_match_id, team_side, minute, player_out, player_in
         FROM lineup_substitutions
         WHERE id = ?
         LIMIT 1'
    );
    if ($stmtSub) {
        $stmtSub->bind_param('i', $subId);
        $stmtSub->execute();
        $result = $stmtSub->get_result();
        $sub = $result ? $result->fetch_assoc() : $sub;
        $stmtSub->close();
    }
}

if ($selectedMatchId === 0 && $sub) {
    $selectedMatchId = (int) $sub['lineup_match_id'];
}

$db->close();

function renderMatchOptions(array $lineups, int $selectedId): string
{
    if (empty($lineups)) {
        return '<option value="">No matches available</option>';
    }
    $options = '';
    foreach ($lineups as $row) {
        $id = (int) $row['id'];
        $label = $row['home_team'] . ' vs ' . $row['away_team'];
        $selected = $id === $selectedId ? ' selected' : '';
        $options .= '<option value="' . htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') . '"' . $selected . '>'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . '</option>';
    }
    return $options;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Substitution</title>
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
                        <h2>Edit Substitution</h2>
                        <p>Update substitution details.</p>
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

                <?php if ($sub): ?>
                    <form class="admin-form" method="post" action="">
                        <div class="admin-form-grid">
                            <label class="admin-form-row">
                                <span>Match</span>
                                <select class="admin-select" name="lineup_match_id" required>
                                    <?php echo renderMatchOptions($lineups, (int) ($sub['lineup_match_id'] ?? $selectedMatchId)); ?>
                                </select>
                            </label>
                            <label class="admin-form-row">
                                <span>Team Side</span>
                                <select class="admin-select" name="team_side">
                                    <option value="home" <?php echo ($sub['team_side'] ?? 'home') === 'home' ? 'selected' : ''; ?>>Home</option>
                                    <option value="away" <?php echo ($sub['team_side'] ?? 'home') === 'away' ? 'selected' : ''; ?>>Away</option>
                                </select>
                            </label>
                            <label class="admin-form-row">
                                <span>Minute</span>
                                <input class="admin-input" type="number" name="minute" value="<?php echo htmlspecialchars((string) ($sub['minute'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Player Out</span>
                                <input class="admin-input" type="text" name="player_out" value="<?php echo htmlspecialchars($sub['player_out'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Player In</span>
                                <input class="admin-input" type="text" name="player_in" value="<?php echo htmlspecialchars($sub['player_in'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                        </div>
                        <div class="admin-form-actions">
                            <a class="admin-link" href="../lineups.php?match_id=<?php echo (int) $selectedMatchId; ?>&section=substitutions">Back to lineups</a>
                            <button class="admin-btn admin-btn-primary" type="submit">Update Substitution</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
