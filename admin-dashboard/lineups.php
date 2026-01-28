<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../db_connection.php';

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
$selectedMatchId = 0;
$activeSection = isset($_GET['section']) ? (string) $_GET['section'] : '';
if ($activeSection === '' && isset($_POST['section'])) {
    $activeSection = (string) $_POST['section'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete_match') {
        $deleteId = (int) ($_POST['id'] ?? 0);
        if ($deleteId > 0) {
            $stmtDelete = $db->prepare('DELETE FROM lineup_matches WHERE id = ?');
            if ($stmtDelete) {
                $stmtDelete->bind_param('i', $deleteId);
                $stmtDelete->execute();
                $stmtDelete->close();
                $message = 'Lineup match deleted successfully.';
            } else {
                $errors[] = 'Unable to delete lineup.';
            }
        } else {
            $errors[] = 'Invalid lineup ID.';
        }
    } elseif ($action === 'delete_player') {
        $playerId = (int) ($_POST['player_id'] ?? 0);
        $lineupMatchId = (int) ($_POST['lineup_match_id'] ?? 0);
        if ($playerId > 0) {
            $stmtDelete = $db->prepare('DELETE FROM lineup_players WHERE id = ?');
            if ($stmtDelete) {
                $stmtDelete->bind_param('i', $playerId);
                $stmtDelete->execute();
                $stmtDelete->close();
                $message = 'Player deleted successfully.';
                $selectedMatchId = $lineupMatchId;
            } else {
                $errors[] = 'Unable to delete player.';
            }
        } else {
            $errors[] = 'Invalid player ID.';
        }
    } elseif ($action === 'delete_sub') {
        $subId = (int) ($_POST['sub_id'] ?? 0);
        $lineupMatchId = (int) ($_POST['lineup_match_id'] ?? 0);
        if ($subId > 0) {
            $stmtDelete = $db->prepare('DELETE FROM lineup_substitutions WHERE id = ?');
            if ($stmtDelete) {
                $stmtDelete->bind_param('i', $subId);
                $stmtDelete->execute();
                $stmtDelete->close();
                $message = 'Substitution deleted successfully.';
                $selectedMatchId = $lineupMatchId;
            } else {
                $errors[] = 'Unable to delete substitution.';
            }
        } else {
            $errors[] = 'Invalid substitution ID.';
        }
    } elseif ($action === 'delete_injury') {
        $injuryId = (int) ($_POST['injury_id'] ?? 0);
        $lineupMatchId = (int) ($_POST['lineup_match_id'] ?? 0);
        if ($injuryId > 0) {
            $stmtDelete = $db->prepare('DELETE FROM lineup_injuries WHERE id = ?');
            if ($stmtDelete) {
                $stmtDelete->bind_param('i', $injuryId);
                $stmtDelete->execute();
                $stmtDelete->close();
                $message = 'Injury deleted successfully.';
                $selectedMatchId = $lineupMatchId;
            } else {
                $errors[] = 'Unable to delete injury.';
            }
        } else {
            $errors[] = 'Invalid injury ID.';
        }
    }
}

$lineups = [];
$stmtList = $db->prepare(
    'SELECT id, home_team, away_team, competition, match_date, status
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

$postMatchId = isset($_POST['lineup_match_id']) ? (int) $_POST['lineup_match_id'] : 0;
$getMatchId = isset($_GET['match_id']) ? (int) $_GET['match_id'] : 0;
if ($selectedMatchId === 0) {
    if ($postMatchId > 0) {
        $selectedMatchId = $postMatchId;
    } elseif ($getMatchId > 0) {
        $selectedMatchId = $getMatchId;
    } elseif (!empty($lineups)) {
        $selectedMatchId = (int) $lineups[0]['id'];
    }
}

$players = [];
$subs = [];
$injuries = [];
$injuriesList = [];
$suspensionsList = [];

if ($selectedMatchId > 0) {
    $stmtPlayers = $db->prepare(
        'SELECT id, team_side, player_name, player_number, position_label, pos_x, pos_y, is_starter
         FROM lineup_players
         WHERE lineup_match_id = ?
         ORDER BY is_starter DESC, team_side ASC, id ASC'
    );
    if ($stmtPlayers) {
        $stmtPlayers->bind_param('i', $selectedMatchId);
        $stmtPlayers->execute();
        $result = $stmtPlayers->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $players[] = $row;
            }
        }
        $stmtPlayers->close();
    }

    $stmtSubs = $db->prepare(
        'SELECT id, team_side, minute, player_out, player_in
         FROM lineup_substitutions
         WHERE lineup_match_id = ?
         ORDER BY minute ASC, id ASC'
    );
    if ($stmtSubs) {
        $stmtSubs->bind_param('i', $selectedMatchId);
        $stmtSubs->execute();
        $result = $stmtSubs->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $subs[] = $row;
            }
        }
        $stmtSubs->close();
    }

    $stmtInjuries = $db->prepare(
        'SELECT id, team_side, player_name, reason, type
         FROM lineup_injuries
         WHERE lineup_match_id = ?
         ORDER BY id ASC'
    );
    if ($stmtInjuries) {
        $stmtInjuries->bind_param('i', $selectedMatchId);
        $stmtInjuries->execute();
        $result = $stmtInjuries->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $injuries[] = $row;
            }
        }
        $stmtInjuries->close();
    }
}

foreach ($injuries as $injury) {
    if (($injury['type'] ?? 'injury') === 'suspension') {
        $suspensionsList[] = $injury;
    } else {
        $injuriesList[] = $injury;
    }
}

if ($activeSection === '') {
    $activeSection = 'matches';
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
    <title>Admin - Lineups</title>
    <link rel="stylesheet" href="./assets/css/lineups.css">
    <link rel="stylesheet" href="./assets/php/header.css"/>
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-section admin-section-active">
            <?php if (!empty($errors)): ?>
                <div class="admin-alert admin-alert-error">
                    <?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php elseif ($message): ?>
                <div class="admin-alert admin-alert-success">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="admin-filter-bar">
                <label for="lineup-section-select">View</label>
                <select class="admin-select" id="lineup-section-select">
                    <?php
                        $sectionOptions = [
                            'matches' => 'Matches',
                            'players' => 'Lineup Players',
                            'substitutions' => 'Substitution',
                            'injuries' => 'Injuries',
                            'suspensions' => 'Suspension',
                        ];
                    ?>
                    <?php foreach ($sectionOptions as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $activeSection === $value ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="lineup-match-select">Match</label>
                <select class="admin-select" id="lineup-match-select" <?php echo empty($lineups) ? 'disabled' : ''; ?>>
                    <?php echo renderMatchOptions($lineups, $selectedMatchId); ?>
                </select>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'matches' ? ' is-active' : ''; ?>" data-section="matches">
                <div class="admin-section-header">
                    <div></div>
                    <a class="add-button" href="adds/add_lineups.php">Add Lineup</a>
                </div>
                <br>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Match</th>
                                <th>Competition</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lineups)): ?>
                                <tr>
                                    <td colspan="7" class="admin-empty-cell">No lineups found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lineups as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Match"><?php echo htmlspecialchars($row['home_team'] . ' vs ' . $row['away_team'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Competition"><?php echo htmlspecialchars($row['competition'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Date"><?php echo htmlspecialchars(date('n/j/Y g:i A', strtotime($row['match_date'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Status">
                                            <span class="admin-pill"><?php echo htmlspecialchars(ucfirst($row['status']), ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_match.php?id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                        <td data-label="Delete">
                                        <form method="post" action="" onsubmit="return confirm('Delete this lineup match?');">
                                            <input type="hidden" name="action" value="delete_match">
                                            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                            <input type="hidden" name="section" value="matches">
                                            <button class="users-delete-btn" type="submit">Delete</button>
                                        </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <br>
            <div class="lineup-section<?php echo $activeSection === 'players' ? ' is-active' : ''; ?>" data-section="players">
                <div class="admin-section-header">
                    <div>
                        <h2>Lineup Players</h2>
                    </div>
                    <a class="add-button" href="adds/add_lineup_players.php?match_id=<?php echo (int) $selectedMatchId; ?>">Add Player</a>
                </div>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Side</th>
                            <th>Player</th>
                            <th>#</th>
                            <th>Starter</th>
                            <th>Pos X</th>
                            <th>Pos Y</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($players)): ?>
                            <tr>
                                <td colspan="9" class="admin-empty-cell">No players found for this match.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($players as $player): ?>
                                <tr>
                                    <td data-label="ID"><?php echo (int) $player['id']; ?></td>
                                    <td data-label="Side"><?php echo htmlspecialchars(ucfirst($player['team_side']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Player"><?php echo htmlspecialchars($player['player_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="#"><?php echo htmlspecialchars((string) ($player['player_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Starter"><?php echo (int) $player['is_starter'] === 1 ? 'Yes' : 'No'; ?></td>
                                    <td data-label="Pos X"><?php echo htmlspecialchars((string) ($player['pos_x'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Pos Y"><?php echo htmlspecialchars((string) ($player['pos_y'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Edit">
                                        <a class="admin-link" href="edit/edit_lineup_players.php?id=<?php echo (int) $player['id']; ?>&match_id=<?php echo (int) $selectedMatchId; ?>">Edit</a>
                                    </td>
                                    <td data-label="Delete">
                                        <form method="post" action="" onsubmit="return confirm('Delete this player?');">
                                            <input type="hidden" name="action" value="delete_player">
                                            <input type="hidden" name="player_id" value="<?php echo (int) $player['id']; ?>">
                                            <input type="hidden" name="lineup_match_id" value="<?php echo (int) $selectedMatchId; ?>">
                                            <input type="hidden" name="section" value="players">
                                            <button class="users-delete-btn" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'substitutions' ? ' is-active' : ''; ?>" data-section="substitutions">
                <div class="admin-section-header">
                    <div>
                        <h2>Substitutions</h2>
                    </div>
                    <a class="add-button" href="adds/add_substitution.php?match_id=<?php echo (int) $selectedMatchId; ?>">Add Substitution</a>
                </div>

                <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Side</th>
                            <th>Minute</th>
                            <th>Player Out</th>
                            <th>Player In</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subs)): ?>
                            <tr>
                                <td colspan="7" class="admin-empty-cell">No substitutions found for this match.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subs as $sub): ?>
                                <tr>
                                    <td data-label="ID"><?php echo (int) $sub['id']; ?></td>
                                    <td data-label="Side"><?php echo htmlspecialchars(ucfirst($sub['team_side']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Minute"><?php echo htmlspecialchars((string) ($sub['minute'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Player Out"><?php echo htmlspecialchars($sub['player_out'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Player In"><?php echo htmlspecialchars($sub['player_in'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Edit">
                                        <a class="admin-link" href="edit/edit_substitution.php?id=<?php echo (int) $sub['id']; ?>&match_id=<?php echo (int) $selectedMatchId; ?>">Edit</a>
                                    </td>
                                    <td data-label="Delete">
                                        <form method="post" action="" onsubmit="return confirm('Delete this substitution?');">
                                            <input type="hidden" name="action" value="delete_sub">
                                            <input type="hidden" name="sub_id" value="<?php echo (int) $sub['id']; ?>">
                                            <input type="hidden" name="lineup_match_id" value="<?php echo (int) $selectedMatchId; ?>">
                                            <input type="hidden" name="section" value="substitutions">
                                            <button class="users-delete-btn" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'injuries' ? ' is-active' : ''; ?>" data-section="injuries">
                <div class="admin-section-header">
                    <div>
                        <h2>Injuries</h2>
                    </div>
                    <a class="add-button" href="adds/add_injury.php?match_id=<?php echo (int) $selectedMatchId; ?>">Add Injury</a>
                </div>

                <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Side</th>
                            <th>Player</th>
                            <th>Reason</th>
                            <th>Type</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($injuriesList)): ?>
                            <tr>
                                <td colspan="7" class="admin-empty-cell">No injuries found for this match.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($injuriesList as $injury): ?>
                                <tr>
                                    <td data-label="ID"><?php echo (int) $injury['id']; ?></td>
                                    <td data-label="Side"><?php echo htmlspecialchars(ucfirst($injury['team_side']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Player"><?php echo htmlspecialchars($injury['player_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Reason"><?php echo htmlspecialchars($injury['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Type"><?php echo htmlspecialchars(ucfirst($injury['type']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-label="Edit">
                                        <a class="admin-link" href="edit/edit_injury.php?id=<?php echo (int) $injury['id']; ?>&match_id=<?php echo (int) $selectedMatchId; ?>">Edit</a>
                                    </td>
                                    <td data-label="Delete">
                                        <form method="post" action="" onsubmit="return confirm('Delete this injury?');">
                                            <input type="hidden" name="action" value="delete_injury">
                                            <input type="hidden" name="injury_id" value="<?php echo (int) $injury['id']; ?>">
                                            <input type="hidden" name="lineup_match_id" value="<?php echo (int) $selectedMatchId; ?>">
                                            <input type="hidden" name="section" value="injuries">
                                            <button class="users-delete-btn" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'suspensions' ? ' is-active' : ''; ?>" data-section="suspensions">
                <div class="admin-section-header">
                    <div>
                        <h2>Suspensions</h2>
                    </div>
                    <a class="add-button" href="adds/add_suspension.php?match_id=<?php echo (int) $selectedMatchId; ?>">Add Suspension</a>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Side</th>
                                <th>Player</th>
                                <th>Reason</th>
                                <th>Type</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($suspensionsList)): ?>
                                <tr>
                                    <td colspan="7" class="admin-empty-cell">No suspensions found for this match.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($suspensionsList as $injury): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $injury['id']; ?></td>
                                        <td data-label="Side"><?php echo htmlspecialchars(ucfirst($injury['team_side']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Player"><?php echo htmlspecialchars($injury['player_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Reason"><?php echo htmlspecialchars($injury['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Type"><?php echo htmlspecialchars(ucfirst($injury['type']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_suspension.php?id=<?php echo (int) $injury['id']; ?>&match_id=<?php echo (int) $selectedMatchId; ?>">Edit</a>
                                        </td>
                                        <td data-label="Delete">
                                            <form method="post" action="" onsubmit="return confirm('Delete this suspension?');">
                                                <input type="hidden" name="action" value="delete_injury">
                                                <input type="hidden" name="injury_id" value="<?php echo (int) $injury['id']; ?>">
                                                <input type="hidden" name="lineup_match_id" value="<?php echo (int) $selectedMatchId; ?>">
                                                <input type="hidden" name="section" value="suspensions">
                                                <button class="users-delete-btn" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
        const sectionSelect = document.getElementById('lineup-section-select');
        const matchSelect = document.getElementById('lineup-match-select');
        const sections = document.querySelectorAll('.lineup-section');

        const setSection = (value) => {
            sections.forEach((section) => {
                section.classList.toggle('is-active', section.dataset.section === value);
            });
            const url = new URL(window.location.href);
            url.searchParams.set('section', value);
            window.history.replaceState({}, '', url);
        };

        if (sectionSelect) {
            setSection(sectionSelect.value);
            sectionSelect.addEventListener('change', () => {
                setSection(sectionSelect.value);
            });
        }

        if (matchSelect) {
            matchSelect.addEventListener('change', () => {
                const url = new URL(window.location.href);
                url.searchParams.set('match_id', matchSelect.value);
                url.searchParams.set('section', sectionSelect ? sectionSelect.value : 'matches');
                window.location.href = url.toString();
            });
        }
    </script>
</body>
</html>
