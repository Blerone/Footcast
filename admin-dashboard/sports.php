<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../db_connection.php';
    require_once __DIR__ . '/assets/includes/SportsRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
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
        header('Location: ../index.php');
        exit;
    }

    $activeSection = isset($_GET['section']) ? (string) $_GET['section'] : '';
    if ($activeSection === '') {
        $activeSection = 'sections';
    }

    $sectionsRows = $sportsRepository->getSections();
    $sportsRows = $sportsRepository->getSports();
    $leaguesRows = $sportsRepository->getLeagues();

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/lineups.css">
</head>
<body>
    <?php include("./assets/php/header.php"); ?>

    <main class="admin-main">
        <section class="admin-section admin-section-active">
            <div class="admin-section-header">
                <div>
                    <h2>Sports Page</h2>
                </div>
            </div>

            <div class="admin-filter-bar">
                <select class="admin-select" id="sports-section-select">
                    <?php
                        $sectionOptions = [
                            'sections' => 'Sections',
                            'sports' => 'Popular Sports',
                            'leagues' => 'Top Leagues',
                        ];
                    ?>
                    <?php foreach ($sectionOptions as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $activeSection === $value ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <br>

            <div class="lineup-section<?php echo $activeSection === 'sections' ? ' is-active' : ''; ?>" data-section="sections">
                <div class="admin-section-header">
                    <div>
                        <h2>Sections</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Popular Sports</th>
                                <th>Top Leagues</th>
                                <th>Newsletter Title</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sectionsRows)): ?>
                                <tr>
                                    <td colspan="5" class="admin-empty-cell">No section rows found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sectionsRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Popular Sports"><?php echo htmlspecialchars($row['popular_sports_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Top Leagues"><?php echo htmlspecialchars($row['top_leagues_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Newsletter"><?php echo htmlspecialchars($row['newsletter_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_sports.php?type=sections&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'sports' ? ' is-active' : ''; ?>" data-section="sports">
                <div class="admin-section-header">
                    <div>
                        <h2>Popular Sports</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sport</th>
                                <th>Count</th>
                                <th>Label</th>
                                <th>Sort</th>
                                <th>Active</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sportsRows)): ?>
                                <tr>
                                    <td colspan="7" class="admin-empty-cell">No sports found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sportsRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Sport"><?php echo htmlspecialchars($row['sport_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Count"><?php echo htmlspecialchars((string) $row['matches_count'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Label"><?php echo htmlspecialchars($row['matches_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Sort"><?php echo htmlspecialchars((string) $row['sort_order'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Active"><?php echo (int) $row['is_active'] === 1 ? 'Yes' : 'No'; ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_sports.php?type=sport&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'leagues' ? ' is-active' : ''; ?>" data-section="leagues">
                <div class="admin-section-header">
                    <div>
                        <h2>Top Leagues</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>League</th>
                                <th>Country</th>
                                <th>Count</th>
                                <th>Label</th>
                                <th>Sort</th>
                                <th>Active</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leaguesRows)): ?>
                                <tr>
                                    <td colspan="8" class="admin-empty-cell">No leagues found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leaguesRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="League"><?php echo htmlspecialchars($row['league_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Country"><?php echo htmlspecialchars($row['league_country'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Count"><?php echo htmlspecialchars((string) $row['matches_count'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Label"><?php echo htmlspecialchars($row['matches_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Sort"><?php echo htmlspecialchars((string) $row['sort_order'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Active"><?php echo (int) $row['is_active'] === 1 ? 'Yes' : 'No'; ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_sports.php?type=league&id=<?php echo (int) $row['id']; ?>">Edit</a>
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
        const sectionSelect = document.getElementById('sports-section-select');
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
    </script>
</body>
</html>
