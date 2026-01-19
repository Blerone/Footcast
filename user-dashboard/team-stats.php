<?php
session_start();
require_once __DIR__ . '/../config/football_api.php';

$LEAGUE_OPTIONS = [
    'PL' => 'Premier League',
    'PD' => 'La Liga',
    'BL1' => 'Bundesliga',
    'SA' => 'Serie A',
    'FL1' => 'Ligue 1',
];

$teamId = isset($_GET['team']) ? (int) $_GET['team'] : 0;
$league = $_GET['league'] ?? 'PL';
if (!array_key_exists($league, $LEAGUE_OPTIONS)) {
    $league = 'PL';
}

$team = null;
$teamError = null;
$recentMatches = [];
$seasonStats = null;
$recentStats = [
    'wins' => 0,
    'draws' => 0,
    'losses' => 0,
    'goals_for' => 0,
    'goals_against' => 0,
    'goal_diff' => 0,
];

if ($teamId > 0) {
    $teamResult = makeFootballAPIRequest("/teams/{$teamId}", [], true, 600);
    if ($teamResult['success']) {
        $team = $teamResult['data'] ?? null;
    } else {
        $teamError = $teamResult['error'] ?? 'Unable to load team.';
    }

    $matchesResult = makeFootballAPIRequest("/teams/{$teamId}/matches", [
        'status' => 'FINISHED',
        'limit' => 5,
    ], true, 300);

    if ($matchesResult['success']) {
        $matches = $matchesResult['data']['matches'] ?? [];
        usort($matches, static function ($a, $b) {
            return strtotime($b['utcDate'] ?? '') <=> strtotime($a['utcDate'] ?? '');
        });
        $recentMatches = array_slice($matches, 0, 5);

        foreach ($recentMatches as $match) {
            $homeId = $match['homeTeam']['id'] ?? 0;
            $awayId = $match['awayTeam']['id'] ?? 0;
            $homeGoals = (int) ($match['score']['fullTime']['home'] ?? 0);
            $awayGoals = (int) ($match['score']['fullTime']['away'] ?? 0);

            if ($homeId === $teamId) {
                $recentStats['goals_for'] += $homeGoals;
                $recentStats['goals_against'] += $awayGoals;
            } elseif ($awayId === $teamId) {
                $recentStats['goals_for'] += $awayGoals;
                $recentStats['goals_against'] += $homeGoals;
            }

            if ($homeGoals === $awayGoals) {
                $recentStats['draws'] += 1;
            } else {
                $isWin = ($homeId === $teamId && $homeGoals > $awayGoals) || ($awayId === $teamId && $awayGoals > $homeGoals);
                if ($isWin) {
                    $recentStats['wins'] += 1;
                } else {
                    $recentStats['losses'] += 1;
                }
            }
        }

        $recentStats['goal_diff'] = $recentStats['goals_for'] - $recentStats['goals_against'];
    }

    $standingsResult = makeFootballAPIRequest("/competitions/{$league}/standings", [], true, 600);
    if ($standingsResult['success']) {
        $tables = $standingsResult['data']['standings'] ?? [];
        foreach ($tables as $table) {
            $rows = $table['table'] ?? [];
            foreach ($rows as $row) {
                if (($row['team']['id'] ?? 0) === $teamId) {
                    $seasonStats = [
                        'played' => $row['playedGames'] ?? 0,
                        'won' => $row['won'] ?? 0,
                        'draw' => $row['draw'] ?? 0,
                        'lost' => $row['lost'] ?? 0,
                        'goals_for' => $row['goalsFor'] ?? 0,
                        'goals_against' => $row['goalsAgainst'] ?? 0,
                        'goal_diff' => $row['goalDifference'] ?? 0,
                        'points' => $row['points'] ?? 0,
                        'position' => $row['position'] ?? 0,
                    ];
                    break 2;
                }
            }
        }
    }
} else {
    $teamError = 'Invalid team selected.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Overview - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/team-stats.css">
</head>
<body>
    <?php
        include("./assets/php/nav.php")
    ?>
    <main class="team-page">
        <a class="team-back" href="index.php?league=<?php echo htmlspecialchars($league, ENT_QUOTES, 'UTF-8'); ?>">← Back to Teams</a>

        <?php if ($teamError): ?>
            <div class="team-error"><?php echo htmlspecialchars($teamError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php elseif ($team): ?>
            <section class="team-hero">
                <div class="team-hero-logo">
                    <?php if (!empty($team['crest'])): ?>
                        <img src="<?php echo htmlspecialchars($team['crest'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($team['name'], ENT_QUOTES, 'UTF-8'); ?> logo">
                    <?php endif; ?>
                </div>
                <div class="team-hero-info">
                    <h1><?php echo htmlspecialchars($team['name'] ?? 'Team', ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="team-meta">
                        <div><p class="team-p">Founded: <?php echo htmlspecialchars((string) ($team['founded'] ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></p></div>
                        <div><p class="team-p">Venue: <?php echo htmlspecialchars($team['venue'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p></div>
                        <div><p class="team-p">Area: <?php echo htmlspecialchars($team['area']['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p></div>
                        <br>
                        <?php if (!empty($team['website'])): ?>
                            <div><a class="team-link" href="<?php echo htmlspecialchars($team['website'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Official Website</a></div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="team-section">
                <h2>Recent Statistics (Last 5 Matches)</h2>
                <div class="stat-grid">
                    <div class="stat-tile">
                        <span class="stat-value"><?php echo $recentStats['wins']; ?></span>
                        <span class="stat-label">Wins</span>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-value"><?php echo $recentStats['draws']; ?></span>
                        <span class="stat-label">Draws</span>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-value"><?php echo $recentStats['losses']; ?></span>
                        <span class="stat-label">Losses</span>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-value"><?php echo $recentStats['goals_for']; ?></span>
                        <span class="stat-label">Goals For</span>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-value"><?php echo $recentStats['goals_against']; ?></span>
                        <span class="stat-label">Goals Against</span>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-value"><?php echo $recentStats['goal_diff']; ?></span>
                        <span class="stat-label">Goal Difference</span>
                    </div>
                </div>
            </section>

            <section class="team-section">
                <h2>Season Overview</h2>
                <?php if ($seasonStats): ?>
                    <div class="season-grid">
                        <div class="season-item"><span>Played</span><strong><?php echo $seasonStats['played']; ?></strong></div>
                        <div class="season-item"><span>Wins</span><strong><?php echo $seasonStats['won']; ?></strong></div>
                        <div class="season-item"><span>Draws</span><strong><?php echo $seasonStats['draw']; ?></strong></div>
                        <div class="season-item"><span>Losses</span><strong><?php echo $seasonStats['lost']; ?></strong></div>
                        <div class="season-item"><span>Goals For</span><strong><?php echo $seasonStats['goals_for']; ?></strong></div>
                        <div class="season-item"><span>Goals Against</span><strong><?php echo $seasonStats['goals_against']; ?></strong></div>
                        <div class="season-item"><span>Goal Diff</span><strong><?php echo $seasonStats['goal_diff']; ?></strong></div>
                        <div class="season-item"><span>Points</span><strong><?php echo $seasonStats['points']; ?></strong></div>
                    </div>
                <?php else: ?>
                    <p class="team-muted">Season stats not available.</p>
                <?php endif; ?>
            </section>

            <section class="team-section">
                <h2>Recent Matches</h2>
                <div class="matches-list">
                    <?php if (empty($recentMatches)): ?>
                        <p class="team-muted">No recent matches found.</p>
                    <?php else: ?>
                        <?php foreach ($recentMatches as $match): ?>
                            <?php
                            $home = $match['homeTeam']['name'] ?? 'Home';
                            $away = $match['awayTeam']['name'] ?? 'Away';
                            $homeGoals = $match['score']['fullTime']['home'] ?? 0;
                            $awayGoals = $match['score']['fullTime']['away'] ?? 0;
                            $dateLabel = $match['utcDate'] ? (new DateTimeImmutable($match['utcDate']))->format('M j, Y') : '';
                            ?>
                            <div class="match-row">
                                <div>
                                    <div class="match-teams"><?php echo htmlspecialchars($home, ENT_QUOTES, 'UTF-8'); ?> vs <?php echo htmlspecialchars($away, ENT_QUOTES, 'UTF-8'); ?></div>
                                   <br>
                                    <div class="match-date"><?php echo htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                                <div class="match-score"><?php echo htmlspecialchars((string) $homeGoals, ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars((string) $awayGoals, ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include './assets/php/footer.php'; ?>
</body>
</html>
