<?php
    declare(strict_types=1);

    session_start();
    require_once __DIR__ . '/../db_connection.php';
    require_once __DIR__ . '/assets/includes/HomepageRepository.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    $db = footcast_db();
    $homepageRepository = new HomepageRepository($db);
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

    $activeSection = isset($_GET['section']) ? (string) $_GET['section'] : '';
    if ($activeSection === '') {
        $activeSection = 'hero';
    }

    $heroRows = $homepageRepository->getHeroRows();
    $sectionsRows = $homepageRepository->getSectionsRows();
    $stepsRows = $homepageRepository->getStepsRows();
    $bannerRows = $homepageRepository->getBannerRows();
    $leaguesRows = $homepageRepository->getLeaguesRows();
    $favoritesRows = $homepageRepository->getFavoritesRows();

    $db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - </title>
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
                    <h2>Homepage Content</h2>
                </div>
            </div>

            <div class="admin-filter-bar">
                <select class="admin-select" id="homepage-section-select">
                    <?php
                        $sectionOptions = [
                            'hero' => 'Hero',
                            'sections' => 'Headings & About',
                            'steps' => 'Bet Steps',
                            'banner' => 'Banner',
                            'leagues' => 'Leagues',
                            'favorites' => 'Favorites',
                        ];
                    ?>
                    <?php foreach ($sectionOptions as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $activeSection === $value ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'hero' ? ' is-active' : ''; ?>" data-section="hero">
                <div class="admin-section-header">
                    <div>
                        <h2>Hero</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sports</th>
                                <th>Bet</th>
                                <th>Updated</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($heroRows)): ?>
                                <tr>
                                    <td colspan="5" class="admin-empty-cell">No hero rows found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($heroRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Sports"><?php echo htmlspecialchars($row['sports_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Bet"><?php echo htmlspecialchars($row['bet_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Updated"><?php echo htmlspecialchars(date('n/j/Y', strtotime($row['updated_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_homepage.php?type=hero&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'sections' ? ' is-active' : ''; ?>" data-section="sections">
                <div class="admin-section-header">
                    <div>
                        <h2>Headings & About</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Trusted By</th>
                                <th>About Title</th>
                                <th>About Highlight</th>
                                <th>Bet Steps</th>
                                <th>Leagues</th>
                                <th>Favorites</th>
                                <th>Updated</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sectionsRows)): ?>
                                <tr>
                                    <td colspan="9" class="admin-empty-cell">No section rows found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sectionsRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Trusted By"><?php echo htmlspecialchars($row['trusted_by_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="About Title"><?php echo htmlspecialchars($row['about_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="About Highlight"><?php echo htmlspecialchars($row['about_highlight'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Bet Steps"><?php echo htmlspecialchars($row['bet_steps_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Leagues"><?php echo htmlspecialchars($row['popular_leagues_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Favorites"><?php echo htmlspecialchars($row['favorites_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Updated"><?php echo htmlspecialchars(date('n/j/Y', strtotime($row['updated_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_homepage.php?type=sections&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'steps' ? ' is-active' : ''; ?>" data-section="steps">
                <div class="admin-section-header">
                    <div>
                        <h2>Bet Steps</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Step #</th>
                                <th>Title</th>
                                <th>Sort</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stepsRows)): ?>
                                <tr>
                                    <td colspan="5" class="admin-empty-cell">No steps found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stepsRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Step #"><?php echo htmlspecialchars((string) $row['step_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Title"><?php echo htmlspecialchars($row['step_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Sort"><?php echo htmlspecialchars((string) $row['sort_order'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_homepage.php?type=step&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'banner' ? ' is-active' : ''; ?>" data-section="banner">
                <div class="admin-section-header">
                    <div>
                        <h2>Banner</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Home</th>
                                <th>Away</th>
                                <th>Timer</th>
                                <th>Odds</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bannerRows)): ?>
                                <tr>
                                    <td colspan="6" class="admin-empty-cell">No banner rows found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bannerRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Home"><?php echo htmlspecialchars($row['home_team'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Away"><?php echo htmlspecialchars($row['away_team'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Timer">
                                            <?php
                                                echo htmlspecialchars((string) $row['days_value'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($row['days_label'], ENT_QUOTES, 'UTF-8') . ' ';
                                                echo htmlspecialchars((string) $row['hours_value'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($row['hours_label'], ENT_QUOTES, 'UTF-8') . ' ';
                                                echo htmlspecialchars((string) $row['minutes_value'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($row['minutes_label'], ENT_QUOTES, 'UTF-8') . ' ';
                                                echo htmlspecialchars((string) $row['seconds_value'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($row['seconds_label'], ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </td>
                                        <td data-label="Odds">
                                            <?php echo htmlspecialchars($row['odds_first'], ENT_QUOTES, 'UTF-8'); ?> /
                                            <?php echo htmlspecialchars($row['odds_second'], ENT_QUOTES, 'UTF-8'); ?> /
                                            <?php echo htmlspecialchars($row['odds_third'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_homepage.php?type=banner&id=<?php echo (int) $row['id']; ?>">Edit</a>
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
                        <h2>Leagues</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Stats</th>
                                <th>Label</th>
                                <th>Top Scorer</th>
                                <th>Goals</th>
                                <th>Sort</th>
                                <th>Active</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leaguesRows)): ?>
                                <tr>
                                    <td colspan="9" class="admin-empty-cell">No leagues found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leaguesRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Name"><?php echo htmlspecialchars($row['league_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Stats"><?php echo htmlspecialchars($row['stats_value'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Label"><?php echo htmlspecialchars($row['stats_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Top Scorer"><?php echo htmlspecialchars($row['top_scorer_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Goals"><?php echo htmlspecialchars($row['goals_text'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Sort"><?php echo htmlspecialchars((string) $row['sort_order'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Active"><?php echo (int) $row['is_active'] === 1 ? 'Yes' : 'No'; ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_homepage.php?type=league&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lineup-section<?php echo $activeSection === 'favorites' ? ' is-active' : ''; ?>" data-section="favorites">
                <div class="admin-section-header">
                    <div>
                        <h2>Favorites</h2>
                    </div>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Label</th>
                                <th>Name</th>
                                <th>Sort</th>
                                <th>Active</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($favoritesRows)): ?>
                                <tr>
                                    <td colspan="6" class="admin-empty-cell">No favorites found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($favoritesRows as $row): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo (int) $row['id']; ?></td>
                                        <td data-label="Label"><?php echo htmlspecialchars($row['item_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Name"><?php echo htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Sort"><?php echo htmlspecialchars((string) $row['sort_order'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td data-label="Active"><?php echo (int) $row['is_active'] === 1 ? 'Yes' : 'No'; ?></td>
                                        <td data-label="Edit">
                                            <a class="admin-link" href="edit/edit_homepage.php?type=favorite&id=<?php echo (int) $row['id']; ?>">Edit</a>
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
        const sectionSelect = document.getElementById('homepage-section-select');
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
