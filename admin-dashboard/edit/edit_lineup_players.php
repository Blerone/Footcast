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

    $playerId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $message = null;
    $errors = [];
    $selectedMatchId = isset($_GET['match_id']) ? (int) $_GET['match_id'] : 0;

    if ($playerId <= 0) {
        $errors[] = 'Invalid player ID.';
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

    $player = null;
    if (empty($errors)) {
        $stmtPlayer = $db->prepare(
            'SELECT id, lineup_match_id, team_side, player_name, player_number, position_label, pos_x, pos_y, is_starter
            FROM lineup_players
            WHERE id = ?
            LIMIT 1'
        );
        if ($stmtPlayer) {
            $stmtPlayer->bind_param('i', $playerId);
            $stmtPlayer->execute();
            $result = $stmtPlayer->get_result();
            $player = $result ? $result->fetch_assoc() : null;
            $stmtPlayer->close();
        }
        if (!$player) {
            $errors[] = 'Player not found.';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
        $lineupMatchId = (int) ($_POST['lineup_match_id'] ?? 0);
        $teamSide = trim((string) ($_POST['team_side'] ?? 'home'));
        $playerName = trim((string) ($_POST['player_name'] ?? ''));
        $playerNumber = trim((string) ($_POST['player_number'] ?? ''));
        $positionLabel = trim((string) ($_POST['position_label'] ?? ''));
        $posX = trim((string) ($_POST['pos_x'] ?? ''));
        $posY = trim((string) ($_POST['pos_y'] ?? ''));
        $isStarter = isset($_POST['is_starter']) ? 1 : 0;

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
            $playerNumberValue = $playerNumber === '' ? null : (int) $playerNumber;
            $posXValue = $posX === '' ? null : (float) $posX;
            $posYValue = $posY === '' ? null : (float) $posY;

            $stmtSave = $db->prepare(
                'UPDATE lineup_players
                SET lineup_match_id = ?, team_side = ?, player_name = ?, player_number = ?, position_label = ?, pos_x = ?, pos_y = ?, is_starter = ?
                WHERE id = ?'
            );
            if ($stmtSave) {
                $stmtSave->bind_param(
                    'ississdii',
                    $lineupMatchId,
                    $teamSide,
                    $playerName,
                    $playerNumberValue,
                    $positionLabel,
                    $posXValue,
                    $posYValue,
                    $isStarter,
                    $playerId
                );
                $stmtSave->execute();
                $stmtSave->close();
                $message = 'Player updated successfully.';
            } else {
                $errors[] = 'Unable to update player.';
            }
        }
    }

    if ($player && empty($errors)) {
        $stmtPlayer = $db->prepare(
            'SELECT id, lineup_match_id, team_side, player_name, player_number, position_label, pos_x, pos_y, is_starter
            FROM lineup_players
            WHERE id = ?
            LIMIT 1'
        );
        if ($stmtPlayer) {
            $stmtPlayer->bind_param('i', $playerId);
            $stmtPlayer->execute();
            $result = $stmtPlayer->get_result();
            $player = $result ? $result->fetch_assoc() : $player;
            $stmtPlayer->close();
        }
    }

    if ($selectedMatchId === 0 && $player) {
        $selectedMatchId = (int) $player['lineup_match_id'];
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
    <title>Edit Lineup Player</title>
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
                        <h2>Edit Lineup Player</h2>
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

                <?php if ($player): ?>
                    <form class="admin-form" method="post" action="">
                        <div class="admin-form-grid">
                            <label class="admin-form-row">
                                <span>Match</span>
                                <select class="admin-select" name="lineup_match_id" required>
                                    <?php echo renderMatchOptions($lineups, (int) ($player['lineup_match_id'] ?? $selectedMatchId)); ?>
                                </select>
                            </label>
                            <label class="admin-form-row">
                                <span>Team Side</span>
                                <select class="admin-select" name="team_side">
                                    <option value="home" <?php echo ($player['team_side'] ?? 'home') === 'home' ? 'selected' : ''; ?>>Home</option>
                                    <option value="away" <?php echo ($player['team_side'] ?? 'home') === 'away' ? 'selected' : ''; ?>>Away</option>
                                </select>
                            </label>
                            <label class="admin-form-row">
                                <span>Player Name</span>
                                <input class="admin-input" type="text" name="player_name" value="<?php echo htmlspecialchars($player['player_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-form-row">
                                <span>Player Number</span>
                                <input class="admin-input" type="number" name="player_number" value="<?php echo htmlspecialchars((string) ($player['player_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Position Label</span>
                                <input class="admin-input" type="text" name="position_label" value="<?php echo htmlspecialchars($player['position_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Pos X (%)</span>
                                <input class="admin-input" type="number" step="0.1" name="pos_x" value="<?php echo htmlspecialchars((string) ($player['pos_x'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Pos Y (%)</span>
                                <input class="admin-input" type="number" step="0.1" name="pos_y" value="<?php echo htmlspecialchars((string) ($player['pos_y'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-form-row">
                                <span>Starter</span>
                                <input class="admin-checkbox" type="checkbox" name="is_starter" value="1" <?php echo (int) ($player['is_starter'] ?? 0) === 1 ? 'checked' : ''; ?>>
                            </label>
                        </div>
                        <div class="admin-form-actions">
                            <a class="admin-link" href="../lineups.php?match_id=<?php echo (int) $selectedMatchId; ?>&section=players">Back to lineups</a>
                            <button class="admin-btn admin-btn-primary" type="submit">Update Player</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
