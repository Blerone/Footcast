<?php
session_start();
    const LEAGUE_OPTIONS = [
    '2021' => ['name' => 'Premier League', 'code' => 'PL', 'id' => 39],
    '2014' => ['name' => 'La Liga', 'code' => 'PD', 'id' => 140],
    '2002' => ['name' => 'Bundesliga', 'code' => 'BL1', 'id' => 78],
    '2019' => ['name' => 'Serie A', 'code' => 'SA', 'id' => 135],
    '2015' => ['name' => 'Ligue 1', 'code' => 'FL1', 'id' => 61],
    ];

    const STATUS_OPTIONS = [
    'SCHEDULED' => 'upcoming',
    'LIVE' => 'live',
    'FINISHED' => 'finished',
    ];

define('FOOTBALL_API_KEY', '92a4c2f60d3e47fbb17ea21881d6838c');
define('FOOTBALL_API_URL', 'https://api.football-data.org/v4');

function buildOddsFromSeed(string $seed): array
{
    $hash = md5($seed);
    $nums = [];
    for ($i = 0; $i < 20; $i++) {
        $nums[] = hexdec(substr($hash, $i * 2, 2)) % 100;
    }
    $homeWin = round(1.5 + ($nums[0] / 100), 2);
    $draw = round(2.5 + ($nums[1] / 100), 2);
    $awayWin = round(1.8 + ($nums[2] / 100), 2);

    return [
        'home_win' => $homeWin,
        'draw' => $draw,
        'away_win' => $awayWin,
        '1h_home_win' => round($homeWin * 1.3, 2),
        '1h_draw' => round($draw * 1.2, 2),
        '1h_away_win' => round($awayWin * 1.3, 2),
        '2h_home_win' => round($homeWin * 1.2, 2),
        '2h_draw' => round($draw * 1.1, 2),
        '2h_away_win' => round($awayWin * 1.2, 2),
        'corners_over_8.5' => round(1.6 + ($nums[3] / 200), 2),
        'corners_under_8.5' => round(2.2 + ($nums[4] / 200), 2),
        'corners_over_9.5' => round(1.7 + ($nums[5] / 200), 2),
        'corners_under_9.5' => round(2.0 + ($nums[6] / 200), 2),
        'corners_over_10.5' => round(1.8 + ($nums[7] / 200), 2),
        'corners_under_10.5' => round(1.9 + ($nums[8] / 200), 2),
        'corners_1h_over_4.5' => round(1.7 + ($nums[11] / 200), 2),
        'corners_1h_under_4.5' => round(2.0 + ($nums[12] / 200), 2),
        'corners_1h_over_5.5' => round(1.9 + ($nums[13] / 200), 2),
        'corners_1h_under_5.5' => round(1.8 + ($nums[14] / 200), 2),
        'yellow_cards_over_3.5' => round(1.6 + ($nums[15] / 200), 2),
        'yellow_cards_under_3.5' => round(2.2 + ($nums[16] / 200), 2),
        'yellow_cards_over_4.5' => round(1.8 + ($nums[17] / 200), 2),
        'yellow_cards_under_4.5' => round(1.9 + ($nums[18] / 200), 2),
        'yellow_cards_1h_over_1.5' => round(1.8 + ($nums[1] / 200), 2),
        'yellow_cards_1h_under_1.5' => round(1.9 + ($nums[2] / 200), 2),
        'yellow_cards_1h_over_2.5' => round(2.2 + ($nums[3] / 200), 2),
        'yellow_cards_1h_under_2.5' => round(1.65 + ($nums[4] / 200), 2),
        'cards_over_4.5' => round(1.7 + ($nums[5] / 200), 2),
        'cards_under_4.5' => round(2.0 + ($nums[6] / 200), 2),
        'cards_over_5.5' => round(1.9 + ($nums[7] / 200), 2),
        'cards_under_5.5' => round(1.8 + ($nums[8] / 200), 2),
        'shots_on_target_over_4.5' => round(1.6 + ($nums[9] / 200), 2),
        'shots_on_target_under_4.5' => round(2.2 + ($nums[10] / 200), 2),
        'shots_on_target_over_5.5' => round(1.8 + ($nums[11] / 200), 2),
        'shots_on_target_under_5.5' => round(1.9 + ($nums[12] / 200), 2),
        'shots_on_target_1h_over_2.5' => round(1.7 + ($nums[13] / 200), 2),
        'shots_on_target_1h_under_2.5' => round(2.0 + ($nums[14] / 200), 2),
        'offsides_over_2.5' => round(1.7 + ($nums[15] / 200), 2),
        'offsides_under_2.5' => round(2.0 + ($nums[16] / 200), 2),
        'offsides_over_3.5' => round(2.1 + ($nums[17] / 200), 2),
        'offsides_under_3.5' => round(1.75 + ($nums[18] / 200), 2),
        'offsides_1h_over_1.5' => round(1.8 + ($nums[19] / 200), 2),
        'offsides_1h_under_1.5' => round(1.9 + ($nums[0] / 200), 2),
        'fouls_over_20.5' => round(1.7 + ($nums[1] / 200), 2),
        'fouls_under_20.5' => round(2.0 + ($nums[2] / 200), 2),
        'fouls_over_25.5' => round(1.9 + ($nums[3] / 200), 2),
        'fouls_under_25.5' => round(1.8 + ($nums[4] / 200), 2),
        'fouls_1h_over_10.5' => round(1.8 + ($nums[5] / 200), 2),
        'fouls_1h_under_10.5' => round(1.9 + ($nums[6] / 200), 2),
        'posts_crossbars_over_0.5' => round(2.5 + ($nums[7] / 200), 2),
        'posts_crossbars_under_0.5' => round(1.5 + ($nums[8] / 200), 2),
        'posts_crossbars_over_1.5' => round(4.0 + ($nums[9] / 200), 2),
        'posts_crossbars_under_1.5' => round(1.25 + ($nums[10] / 200), 2),
        'posts_crossbars_1h_over_0.5' => round(3.0 + ($nums[11] / 200), 2),
        'posts_crossbars_1h_under_0.5' => round(1.4 + ($nums[12] / 200), 2),
        'throw_ins_over_40.5' => round(1.7 + ($nums[13] / 200), 2),
        'throw_ins_under_40.5' => round(2.0 + ($nums[14] / 200), 2),
        'throw_ins_over_45.5' => round(1.9 + ($nums[15] / 200), 2),
        'throw_ins_under_45.5' => round(1.8 + ($nums[16] / 200), 2),
        'throw_ins_1h_over_20.5' => round(1.8 + ($nums[17] / 200), 2),
        'throw_ins_1h_under_20.5' => round(1.9 + ($nums[18] / 200), 2),
        'shots_towards_goal_over_10.5' => round(1.6 + ($nums[19] / 200), 2),
        'shots_towards_goal_under_10.5' => round(2.2 + ($nums[0] / 200), 2),
        'shots_towards_goal_over_12.5' => round(1.8 + ($nums[1] / 200), 2),
        'shots_towards_goal_under_12.5' => round(1.9 + ($nums[2] / 200), 2),
    ];
}

function fetchFootballMatches(string $fromDate, string $toDate, string $status, string $competitionId, ?string &$error): array
{
    $error = null;
    $query = "status={$status}&dateFrom={$fromDate}&dateTo={$toDate}&competitions={$competitionId}";
    $url = FOOTBALL_API_URL . "/matches?{$query}";
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-Auth-Token: ' . FOOTBALL_API_KEY,
            'Accept: application/json',
        ],
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 6,
    ]);
    $response = curl_exec($curl);
    if ($response === false && curl_errno($curl) === 60) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($curl);
    }
    if ($response === false) {
        $error = 'Request failed: ' . curl_error($curl);
        curl_close($curl);
        return [];
    }
    $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);
    if ($statusCode >= 400) {
        $error = "API error ({$statusCode}).";
        return [];
    }

    $data = json_decode($response, true);
    if (!is_array($data) || !isset($data['matches']) || !is_array($data['matches'])) {
        $error = 'Invalid match data received.';
        return [];
    }

    return $data['matches'];
}

$matchesError = null;
$matches = [];
$selectedLeagueRaw = $_GET['league'] ?? '2021';
$selectedLeague = array_key_exists($selectedLeagueRaw, LEAGUE_OPTIONS) ? $selectedLeagueRaw : '2021';
$selectedLeagueCode = LEAGUE_OPTIONS[$selectedLeague]['code'] ?? $selectedLeague;
$selectedStatus = $_GET['status'] ?? 'SCHEDULED';
$selectedStatus = array_key_exists($selectedStatus, STATUS_OPTIONS) ? $selectedStatus : 'SCHEDULED';

$nowUtc = new DateTimeImmutable('now', new DateTimeZone('UTC'));
if ($selectedStatus === 'FINISHED') {
    $fromDate = $nowUtc->modify('-7 days')->format('Y-m-d');
    $toDate = $nowUtc->format('Y-m-d');
    $emptyMessage = 'No finished matches in the last 7 days.';
} elseif ($selectedStatus === 'LIVE') {
    $fromDate = $nowUtc->format('Y-m-d');
    $toDate = $nowUtc->modify('+1 day')->format('Y-m-d');
    $emptyMessage = 'No live matches right now.';
} else {
    $fromDate = $nowUtc->format('Y-m-d');
    $toDate = $nowUtc->modify('+7 days')->format('Y-m-d');
    $emptyMessage = 'No upcoming matches in the next 7 days.';
}

$apiStatusMap = [
    'SCHEDULED' => 'SCHEDULED,TIMED',
    'LIVE' => 'LIVE,IN_PLAY,PAUSED',
    'FINISHED' => 'FINISHED',
];
$apiStatus = $apiStatusMap[$selectedStatus] ?? $selectedStatus;
$apiMatches = fetchFootballMatches($fromDate, $toDate, $apiStatus, $selectedLeagueCode, $matchesError);

if (!$matchesError) {
    foreach ($apiMatches as $match) {
        $matchId = $match['id'] ?? null;
        $home = $match['homeTeam'] ?? [];
        $away = $match['awayTeam'] ?? [];
        $matchDate = $match['utcDate'] ?? '';
        $matches[] = [
            'id' => $matchId,
            'api_fixture_id' => $matchId,
            'home_team' => $home['name'] ?? 'Home',
            'away_team' => $away['name'] ?? 'Away',
            'home_team_crest' => $home['crest'] ?? '',
            'away_team_crest' => $away['crest'] ?? '',
            'match_date' => $matchDate,
            'home_score' => $match['score']['fullTime']['home'] ?? null,
            'away_score' => $match['score']['fullTime']['away'] ?? null,
            'status' => $selectedStatus,
            'odds' => buildOddsFromSeed((string) $matchId),
        ];
    }
}

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches</title>
    <link rel="stylesheet" href="./assets/css/matches.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body data-logged-in="<?php echo $isLoggedIn ? '1' : '0'; ?>">
    <?php 
        include("./assets/php/header.php")
    ?>
    <section class="matches-container">
        <div class="matches-controls">
            <a class="matches-button <?php echo $selectedStatus === 'SCHEDULED' ? 'active' : ''; ?>" href="?league=<?php echo htmlspecialchars($selectedLeague, ENT_QUOTES, 'UTF-8'); ?>&status=SCHEDULED">Matches</a>
            <a class="live-button <?php echo $selectedStatus === 'LIVE' ? 'active' : ''; ?>" href="?league=<?php echo htmlspecialchars($selectedLeague, ENT_QUOTES, 'UTF-8'); ?>&status=LIVE">Live</a>
            <a class="matches-button <?php echo $selectedStatus === 'FINISHED' ? 'active' : ''; ?>" href="?league=<?php echo htmlspecialchars($selectedLeague, ENT_QUOTES, 'UTF-8'); ?>&status=FINISHED">Finished</a>
            <form method="get" class="league-filter">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($selectedStatus, ENT_QUOTES, 'UTF-8'); ?>">
                <select id="league-select" name="league" onchange="this.form.submit()">
                <?php foreach (LEAGUE_OPTIONS as $leagueId => $leagueData): ?>
                    <?php 
                    $leagueIdValue = (string) $leagueId;
                    $leagueName = is_array($leagueData) ? $leagueData['name'] : $leagueData;
                    ?>
                    <option value="<?php echo htmlspecialchars($leagueIdValue, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedLeague === $leagueIdValue ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($leagueName, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        </div>
        <div class="matches-grid">
            <?php if (!empty($matchesError)): ?>
                <div class="matches-error-container" style="grid-column: 1 / -1; padding: 20px; background: #fee; border: 1px solid #fcc; border-radius: 8px; margin: 20px 0;">
                    <p class="matches-error" style="color: #c00; font-weight: bold; margin: 0 0 10px 0;"><?php echo htmlspecialchars($matchesError, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if (strpos($matchesError, 'internet connection') !== false || strpos($matchesError, 'API key') !== false || strpos($matchesError, 'connect') !== false): ?>
                        <p style="color: #666; font-size: 0.9em; margin: 0;">
                            <strong>Debug Info:</strong><br>
                            - Check your API key in <code>php/config/football_api.php</code><br>
                            - Ensure you have internet connectivity<br>
                            - Test the API: <a href="test_matches_api.php" target="_blank">test_matches_api.php</a><br>
                            - API URL attempted: <?php echo htmlspecialchars($apiUrl ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php elseif (empty($matches)): ?>
                <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #666;">
                <p class="matches-empty"><?php echo htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if (isset($_GET['debug'])): ?>
                        <p style="font-size: 0.8em; margin-top: 10px; color: #999;">
                            Debug: League: <?php echo htmlspecialchars($selectedLeague, ENT_QUOTES, 'UTF-8'); ?>, 
                            Status: <?php echo htmlspecialchars($selectedStatus, ENT_QUOTES, 'UTF-8'); ?>, 
                            API Status: <?php echo htmlspecialchars($apiStatus, ENT_QUOTES, 'UTF-8'); ?>, 
                            League Code: <?php echo htmlspecialchars($selectedLeagueCode, ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($matches, 0, 9) as $match): ?>
                    <?php
                    // Extract match data from new API format
                    $matchId = $match['id'] ?? $match['api_fixture_id'] ?? '';
                    $homeName = $match['home_team'] ?? 'Home';
                    $awayName = $match['away_team'] ?? 'Away';
                    $homeLogo = $match['home_team_crest'] ?? '';
                    $awayLogo = $match['away_team_crest'] ?? '';
                    $leagueName = LEAGUE_OPTIONS[$selectedLeague]['name'] ?? 'League';
                    $matchDate = $match['match_date'] ?? '';
                    $displayDate = 'TBD';
                    if ($matchDate) {
                        try {
                            $displayDate = (new DateTimeImmutable($matchDate))->format('M j, g:i A');
                        } catch (Exception $e) {
                            $displayDate = $matchDate;
                        }
                    }
                    $scoreHome = $match['home_score'] ?? null;
                    $scoreAway = $match['away_score'] ?? null;
                    $scoreHomeText = $scoreHome === null ? '-' : (string) $scoreHome;
                    $scoreAwayText = $scoreAway === null ? '-' : (string) $scoreAway;
                    
                    // Get odds from API response
                    $odds = $match['odds'] ?? [];
                    $homeWinOdds = $odds['home_win'] ?? '2.10';
                    $drawOdds = $odds['draw'] ?? '3.10';
                    $awayWinOdds = $odds['away_win'] ?? '3.80';
                    ?>
                    <div class="match-box" 
                         data-match-id="<?php echo htmlspecialchars((string) $matchId, ENT_QUOTES, 'UTF-8'); ?>" 
                         data-api-fixture-id="<?php echo htmlspecialchars((string) $matchId, ENT_QUOTES, 'UTF-8'); ?>"
                         data-league="<?php echo htmlspecialchars($leagueName, ENT_QUOTES, 'UTF-8'); ?>" 
                         data-home="<?php echo htmlspecialchars($homeName, ENT_QUOTES, 'UTF-8'); ?>" 
                         data-away="<?php echo htmlspecialchars($awayName, ENT_QUOTES, 'UTF-8'); ?>" 
                         data-home-logo="<?php echo htmlspecialchars($homeLogo, ENT_QUOTES, 'UTF-8'); ?>"
                         data-away-logo="<?php echo htmlspecialchars($awayLogo, ENT_QUOTES, 'UTF-8'); ?>"
                         data-start="<?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?>"
                         data-match-date="<?php echo htmlspecialchars($matchDate, ENT_QUOTES, 'UTF-8'); ?>"
                         data-odds='<?php echo json_encode($odds); ?>'>
                        <div class="match-header">
                            <span class="league-name"><?php echo htmlspecialchars($leagueName, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <div class="match-score">
                            <div class="team">
                                <div class="team-logo">
                                    <?php if ($homeLogo): ?>
                                        <img src="<?php echo htmlspecialchars($homeLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($homeName, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php endif; ?>
                                </div>
                                <span class="team-name"><?php echo htmlspecialchars($homeName, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="score">
                                <span class="score-value"><?php echo htmlspecialchars($scoreHomeText, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="score-separator">-</span>
                                <span class="score-value"><?php echo htmlspecialchars($scoreAwayText, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="team">
                                <div class="team-logo">
                                    <?php if ($awayLogo): ?>
                                        <img src="<?php echo htmlspecialchars($awayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($awayName, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php endif; ?>
                                </div>
                                <span class="team-name"><?php echo htmlspecialchars($awayName, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>

                        <div class="match-info">
                            <button class="more-info-btn">More Info</button>
                            <div class="match-details">
                                <span class="date-icon material-symbols-outlined">calendar_month</span>
                                <span class="match-date"><?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>

                        <div class="match-bottom">
                            <div class="bets-container">
                                <div class="bets-buttons">
                                    <p>1</p>
                                    <button class="draw-btn bet-pick" 
                                            data-bet-type="home_win"
                                            data-market="Match Result" 
                                            data-outcome="<?php echo htmlspecialchars($homeName, ENT_QUOTES, 'UTF-8'); ?>" 
                                            data-odds="<?php echo htmlspecialchars((string) $homeWinOdds, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars((string) $homeWinOdds, ENT_QUOTES, 'UTF-8'); ?>
                                    </button>
                                </div>
                                <div class="bets-buttons">
                                    <p>X</p>
                                    <button class="draw-btn bet-pick" 
                                            data-bet-type="draw"
                                            data-market="Match Result" 
                                            data-outcome="Draw" 
                                            data-odds="<?php echo htmlspecialchars((string) $drawOdds, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars((string) $drawOdds, ENT_QUOTES, 'UTF-8'); ?>
                                    </button>
                                </div>
                                <div class="bets-buttons">
                                    <p>2</p>
                                    <button class="draw-btn bet-pick" 
                                            data-bet-type="away_win"
                                            data-market="Match Result" 
                                            data-outcome="<?php echo htmlspecialchars($awayName, ENT_QUOTES, 'UTF-8'); ?>" 
                                            data-odds="<?php echo htmlspecialchars((string) $awayWinOdds, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars((string) $awayWinOdds, ENT_QUOTES, 'UTF-8'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <div id="betSlipModal" class="modal">
        <div class="modal-content bet-slip-content" role="dialog" aria-modal="true" aria-labelledby="betSlipTitle">
            <div class="modal-header">
                <h2 id="betSlipTitle">Bet Slip</h2>
                <button class="modal-close" type="button" data-modal-close>&times;</button>
            </div>
            <div class="modal-body bet-slip-body">
                <div class="bet-slip-section">
                    <button class="section-toggle" type="button" data-target="betSlipSelectionsSection" aria-expanded="true">
                        <span>Selections</span>
                        <span class="toggle-icon">▾</span>
                    </button>
                    <div id="betSlipSelectionsSection" class="section-content">
                        <div id="betSlipSelections" class="bet-slip-list"></div>
                        <p class="bet-slip-empty" id="betSlipEmpty">No selections yet. Tap odds to add picks.</p>
                    </div>
                </div>
                <div class="bet-slip-section">
                    <button class="section-toggle" type="button" data-target="betSlipMarketsSection" aria-expanded="true">
                        <span>Markets</span>
                        <span class="toggle-icon">▾</span>
                    </button>
                    <div id="betSlipMarketsSection" class="section-content">
                        <div class="bet-match-info">
                            <div class="bet-teams">
                                <div class="bet-team">
                                    <img id="modal-home-logo" src="" alt="" class="bet-team-logo">
                                    <span id="modal-home-name"></span>
                                </div>
                                <span class="bet-vs">VS</span>
                                <div class="bet-team">
                                    <img id="modal-away-logo" src="" alt="" class="bet-team-logo">
                                    <span id="modal-away-name"></span>
                                </div>
                            </div>
                        </div>
                        <div class="betting-categories">
                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Match Result</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="home_win" data-odds="0">
                                            <span class="option-label" id="modal-home-name-display"></span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="draw" data-odds="0">
                                            <span class="option-label">Draw</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="away_win" data-odds="0">
                                            <span class="option-label" id="modal-away-name-display"></span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">1st Half Result</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="1h_home_win" data-odds="0">
                                            <span class="option-label" id="modal-home-name-display-1h"></span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="1h_draw" data-odds="0">
                                            <span class="option-label">Draw</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="1h_away_win" data-odds="0">
                                            <span class="option-label" id="modal-away-name-display-1h"></span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">2nd Half Result</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="2h_home_win" data-odds="0">
                                            <span class="option-label" id="modal-home-name-display-2h"></span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="2h_draw" data-odds="0">
                                            <span class="option-label">Draw</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="2h_away_win" data-odds="0">
                                            <span class="option-label" id="modal-away-name-display-2h"></span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Corners</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options-grid">
                                        <div class="bet-section">
                                            <div class="bet-section-label">FULL MATCH</div>
                                            <div class="bet-options">
                                                <button class="bet-option-btn" data-bet-type="corners_over_8.5" data-odds="0">
                                                    <span class="option-label">Over 8.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_under_8.5" data-odds="0">
                                                    <span class="option-label">Under 8.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_over_9.5" data-odds="0">
                                                    <span class="option-label">Over 9.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_under_9.5" data-odds="0">
                                                    <span class="option-label">Under 9.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_over_10.5" data-odds="0">
                                                    <span class="option-label">Over 10.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_under_10.5" data-odds="0">
                                                    <span class="option-label">Under 10.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bet-section">
                                            <div class="bet-section-label">1ST HALF</div>
                                            <div class="bet-options">
                                                <button class="bet-option-btn" data-bet-type="corners_1h_over_4.5" data-odds="0">
                                                    <span class="option-label">Over 4.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_1h_under_4.5" data-odds="0">
                                                    <span class="option-label">Under 4.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_1h_over_5.5" data-odds="0">
                                                    <span class="option-label">Over 5.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="corners_1h_under_5.5" data-odds="0">
                                                    <span class="option-label">Under 5.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Yellow Cards</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options-grid">
                                        <div class="bet-section">
                                            <div class="bet-section-label">FULL MATCH</div>
                                            <div class="bet-options">
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_over_3.5" data-odds="0">
                                                    <span class="option-label">Over 3.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_under_3.5" data-odds="0">
                                                    <span class="option-label">Under 3.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_over_4.5" data-odds="0">
                                                    <span class="option-label">Over 4.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_under_4.5" data-odds="0">
                                                    <span class="option-label">Under 4.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="bet-section">
                                            <div class="bet-section-label">1ST HALF</div>
                                            <div class="bet-options">
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_1h_over_1.5" data-odds="0">
                                                    <span class="option-label">Over 1.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_1h_under_1.5" data-odds="0">
                                                    <span class="option-label">Under 1.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_1h_over_2.5" data-odds="0">
                                                    <span class="option-label">Over 2.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                                <button class="bet-option-btn" data-bet-type="yellow_cards_1h_under_2.5" data-odds="0">
                                                    <span class="option-label">Under 2.5</span>
                                                    <span class="option-odds">—</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Cards (Total)</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="cards_over_4.5" data-odds="0">
                                            <span class="option-label">Over 4.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="cards_under_4.5" data-odds="0">
                                            <span class="option-label">Under 4.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="cards_over_5.5" data-odds="0">
                                            <span class="option-label">Over 5.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="cards_under_5.5" data-odds="0">
                                            <span class="option-label">Under 5.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Shots on Target</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="shots_on_target_over_4.5" data-odds="0">
                                            <span class="option-label">Over 4.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="shots_on_target_under_4.5" data-odds="0">
                                            <span class="option-label">Under 4.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="shots_on_target_over_5.5" data-odds="0">
                                            <span class="option-label">Over 5.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="shots_on_target_under_5.5" data-odds="0">
                                            <span class="option-label">Under 5.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Offsides</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="offsides_over_2.5" data-odds="0">
                                            <span class="option-label">Over 2.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="offsides_under_2.5" data-odds="0">
                                            <span class="option-label">Under 2.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="offsides_over_3.5" data-odds="0">
                                            <span class="option-label">Over 3.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="offsides_under_3.5" data-odds="0">
                                            <span class="option-label">Under 3.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Fouls</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="fouls_over_20.5" data-odds="0">
                                            <span class="option-label">Over 20.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="fouls_under_20.5" data-odds="0">
                                            <span class="option-label">Under 20.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="fouls_over_25.5" data-odds="0">
                                            <span class="option-label">Over 25.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="fouls_under_25.5" data-odds="0">
                                            <span class="option-label">Under 25.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Posts and Crossbar</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="posts_crossbars_over_0.5" data-odds="0">
                                            <span class="option-label">Over 0.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="posts_crossbars_under_0.5" data-odds="0">
                                            <span class="option-label">Under 0.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="posts_crossbars_over_1.5" data-odds="0">
                                            <span class="option-label">Over 1.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="posts_crossbars_under_1.5" data-odds="0">
                                            <span class="option-label">Under 1.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Throw-ins</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="throw_ins_over_40.5" data-odds="0">
                                            <span class="option-label">Over 40.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="throw_ins_under_40.5" data-odds="0">
                                            <span class="option-label">Under 40.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="throw_ins_over_45.5" data-odds="0">
                                            <span class="option-label">Over 45.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="throw_ins_under_45.5" data-odds="0">
                                            <span class="option-label">Under 45.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bet-category">
                                <div class="bet-category-header">
                                    <span class="category-name">Shots towards Goal</span>
                                </div>
                                <div class="bet-category-content">
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-bet-type="shots_towards_goal_over_10.5" data-odds="0">
                                            <span class="option-label">Over 10.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="shots_towards_goal_under_10.5" data-odds="0">
                                            <span class="option-label">Under 10.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="shots_towards_goal_over_12.5" data-odds="0">
                                            <span class="option-label">Over 12.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                        <button class="bet-option-btn" data-bet-type="shots_towards_goal_under_12.5" data-odds="0">
                                            <span class="option-label">Under 12.5</span>
                                            <span class="option-odds">—</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bet-slip-section">
                    <button class="section-toggle" type="button" data-target="betSlipStakeSection" aria-expanded="true">
                        <span>Stake &amp; Payout</span>
                        <span class="toggle-icon">▾</span>
                    </button>
                    <div id="betSlipStakeSection" class="section-content">
                        <div class="stake-row">
                            <label for="betStake">Stake ($)</label>
                            <input type="number" id="betStake" name="betStake" min="1" max="10000" step="0.01" placeholder="Enter stake">
                            <span class="input-hint">Min 1, Max 10000</span>
                        </div>
                        <div class="odds-row">
                            <div class="odds-item">
                                <span>Combined Odds</span>
                                <strong id="combinedOdds">—</strong>
                            </div>
                            <div class="odds-item">
                                <span>Estimated Payout</span>
                                <strong id="estimatedPayout">—</strong>
                            </div>
                        </div>
                        <p class="bet-slip-error" id="betSlipError" role="alert"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bet-slip-footer">
                <button class="modal-btn outline-btn" type="button" id="clearBetSlip">Clear</button>
                <button class="modal-btn confirm-btn" type="button" id="placeBetBtn" disabled>Place Bet</button>
            </div>
        </div>
    </div>
    <div id="betSlipToast" class="bet-slip-toast" role="status" aria-live="polite"></div>
    <?php
        include('./assets/php/footer.php');
    ?>
    <script src="./assets/js/matches.js?v=2"></script>
</body>
</html>
