<?php
    declare(strict_types=1);

    session_start();
require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../assets/includes/LineupRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

$db = footcast_db();
$userId = (int) $_SESSION['user_id'];
$lineupRepository = new LineupRepository($db);

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
    $selectedMatchId = isset($_GET['match_id']) ? (int) $_GET['match_id'] : 0;

$lineups = $lineupRepository->getMatches();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $lineupMatchId = (int) ($_POST['lineup_match_id'] ?? 0);
        $teamSide = trim((string) ($_POST['team_side'] ?? 'home'));
        $playerName = trim((string) ($_POST['player_name'] ?? ''));
        $reason = trim((string) ($_POST['reason'] ?? ''));

        if ($lineupMatchId <= 0) {
            $errors[] = 'Select a lineup match.';
        }
        if (!in_array($teamSide, ['home', 'away'], true)) {
            $errors[] = 'Invalid team side.';
        }
        if ($playerName === '') {
            $errors[] = 'Player name is required.';
        }

        if (empty($errors)) {
        $payload = [
            'lineup_match_id' => $lineupMatchId,
            'team_side' => $teamSide,
            'player_name' => $playerName,
            'reason' => $reason,
            'type' => 'suspension',
        ];
        if ($lineupRepository->createInjury($payload)) {
            $message = 'Suspension added successfully.';
            $selectedMatchId = $lineupMatchId;
        } else {
            $errors[] = 'Unable to add suspension.';
        }
    }
}

    if ($selectedMatchId === 0 && !empty($lineups)) {
        $selectedMatchId = (int) $lineups[0]['id'];
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
    <title>Add Suspension</title>
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
                        <h2>Add Suspension</h2>
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
                            <span>Match</span>
                            <select class="admin-select" name="lineup_match_id" required>
                                <?php echo renderMatchOptions($lineups, $selectedMatchId); ?>
                            </select>
                        </label>
                        <label class="admin-form-row">
                            <span>Team Side</span>
                            <?php $suspensionSide = $_POST['team_side'] ?? 'home'; ?>
                            <select class="admin-select" name="team_side">
                                <option value="home" <?php echo $suspensionSide === 'home' ? 'selected' : ''; ?>>Home</option>
                                <option value="away" <?php echo $suspensionSide === 'away' ? 'selected' : ''; ?>>Away</option>
                            </select>
                        </label>
                        <label class="admin-form-row">
                            <span>Player Name</span>
                            <input class="admin-input" type="text" name="player_name" value="<?php echo htmlspecialchars($_POST['player_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </label>
                        <label class="admin-form-row">
                            <span>Reason</span>
                            <input class="admin-input" type="text" name="reason" value="<?php echo htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </label>
                    </div>
                    <div class="admin-form-actions">
                    <a class="admin-link" href="../lineups.php?match_id=<?php echo (int) $selectedMatchId; ?>&section=suspensions">Back to lineups</a>
                        <button class="admin-btn admin-btn-primary" type="submit">Add Suspension</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
