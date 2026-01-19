<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../config/football_api.php';

$LEAGUE_OPTIONS = [
    'PL' => 'Premier League',
    'PD' => 'La Liga',
    'BL1' => 'Bundesliga',
    'SA' => 'Serie A',
    'FL1' => 'Ligue 1',
];

$selectedLeague = $_GET['league'] ?? 'PL';
if (!array_key_exists($selectedLeague, $LEAGUE_OPTIONS)) {
    $selectedLeague = 'PL';
}

$teams = [];
$teamsError = null;
$teamsResult = makeFootballAPIRequest("/competitions/{$selectedLeague}/teams", [], true, 600);
if ($teamsResult['success']) {
    $teams = $teamsResult['data']['teams'] ?? [];
    usort($teams, static function ($a, $b) {
        return strcmp($a['name'] ?? '', $b['name'] ?? '');
    });
} else {
    $teamsError = $teamsResult['error'] ?? 'Unable to load teams.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

</head>
<body>
    <?php
        include("./assets/php/nav.php")
    ?>
    
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="dashboard-heading">
                <h1>My Dashboard</h1>
                <p>Welcome back, <span class="username"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>!</p>
            </div>

            <div class="balance-card">
                <div class="balance-info">
                    <h3>Account Balance</h3>
                    <div class="balance-amount" id="balance-amount">$0.00</div>
                </div>
                <div class="balance-actions">
                    <button class="btn-primary" onclick="window.location.href='../matches.php'">Place Bet</button>
                </div>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><span class="material-symbols-outlined">analytics</span></div>
                <div class="stat-info">
                    <h4>Total Bets</h4>
                    <p class="stat-value" id="total-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-symbols-outlined">trophy</span></div>
                <div class="stat-info">
                    <h4>Won</h4>
                    <p class="stat-value" id="won-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-symbols-outlined">close</span></div>
                <div class="stat-info">
                    <h4>Lost</h4>
                    <p class="stat-value" id="lost-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-symbols-outlined">hourglass</span></div>
                <div class="stat-info">
                    <h4>Pending</h4>
                    <p class="stat-value" id="pending-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-symbols-outlined">monitoring</span></div>
                <div class="stat-info">
                    <h4>Win Rate</h4>
                    <p class="stat-value" id="win-rate">0%</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-symbols-outlined">account_balance_wallet</span></div>
                <div class="stat-info">
                    <h4>Total Winnings</h4>
                    <p class="stat-value" id="total-winnings">$0.00</p>
                </div>
            </div>
        </div>

        <section class="dashboard-team-stats">
            <div class="dashboard-section-head">
                <div>
                    <h2>Team Stats</h2>
                    <p>Pick a team to view the last five results and season totals.</p>
                </div>
            </div>

            <div class="stats-controls dashboard-controls">
                <label class="stats-label" for="league-select">Select League</label>
                <select id="league-select" class="stats-select">
                    <?php foreach ($LEAGUE_OPTIONS as $code => $label): ?>
                        <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedLeague === $code ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($teamsError): ?>
                <div class="stats-error"><?php echo htmlspecialchars($teamsError, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <div class="teams-grid" id="teams-grid">
                <?php foreach ($teams as $team): ?>
                    <?php
                    $teamId = $team['id'] ?? 0;
                    $teamName = $team['name'] ?? 'Team';
                    $teamCrest = $team['crest'] ?? '';
                    ?>
                    <a class="team-card" href="team-stats.php?league=<?php echo htmlspecialchars($selectedLeague, ENT_QUOTES, 'UTF-8'); ?>&team=<?php echo htmlspecialchars((string) $teamId, ENT_QUOTES, 'UTF-8'); ?>" data-team-name="<?php echo htmlspecialchars(strtolower($teamName), ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="team-crest">
                            <?php if ($teamCrest): ?>
                                <img src="<?php echo htmlspecialchars($teamCrest, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($teamName, ENT_QUOTES, 'UTF-8'); ?> logo">
                            <?php endif; ?>
                        </div>
                        <div class="team-name"><?php echo htmlspecialchars($teamName, ENT_QUOTES, 'UTF-8'); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>

        </section>
    </div>

    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/nav.js"></script>
    <script src="../assets/js/stats.js"></script>
</body>
</html>
