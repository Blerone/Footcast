<?php
  session_start();
  require_once __DIR__ . '/config/football_api.php';

  $LEAGUE_OPTIONS = [
    'PL' => ['name' => 'Premier League', 'code' => 'PL'],
    'PD' => ['name' => 'La Liga', 'code' => 'PD'],
    'BL1' => ['name' => 'Bundesliga', 'code' => 'BL1'],
    'SA' => ['name' => 'Serie A', 'code' => 'SA'],
    'FL1' => ['name' => 'Ligue 1', 'code' => 'FL1'],
  ];

  $selectedLeague = $_GET['league'] ?? 'PL';
  if (!isset($LEAGUE_OPTIONS[$selectedLeague])) {
    $selectedLeague = 'PL';
  }

  $standingsData = getLeagueStandings($selectedLeague);
  $standings = [];
  $leagueName = 'Premier League';
  $error = null;

  if ($standingsData && isset($standingsData['success']) && $standingsData['success']) {
    $standings = $standingsData['standings'] ?? [];
    $leagueName = $standingsData['league'] ?? $LEAGUE_OPTIONS[$selectedLeague]['name'];
  } 
  else {
    $error = $standingsData['error'] ?? 'Failed to load standings. Please try again later.';
  }

  function getRankClass($position) {
    if ($position == 1) return 'rank-1';
    if ($position == 2) return 'rank-2';
    if ($position == 3) return 'rank-3';
    if ($position == 4) return 'rank-4';
    if ($position == 5) return 'rank-5';
    if ($position == 6) return 'rank-6';
    if ($position == 7) return 'rank-7';
    if ($position == 17) return 'rank-17';
    if ($position >= 18) return 'rank-bottom';
    return 'rank-mid';
  }

  function formatGoalDifference($gd) {
    if ($gd > 0) {
      return '+' . $gd;
    }
    return (string)$gd;
  }

  function renderFormBadges($form) {
    if (empty($form) || !is_string($form)) {
      return '';
    }
      
    $formArray = str_split($form);
    $badges = '';
      
    foreach ($formArray as $result) {
      $class = 'form-badge ';
      if (strtoupper($result) === 'W') {
        $class .= 'win';    
      } elseif (strtoupper($result) === 'D') {
        $class .= 'draw';
      } elseif (strtoupper($result) === 'L') {
        $class .= 'loss';
      } else {
          continue;
      }
          
      $badges .= '<span class="' . htmlspecialchars($class) . '">' . htmlspecialchars(strtoupper($result)) . '</span>';
    }    
    return $badges;
  }

  function getTeamLogo($crest, $teamName) {
    if (!empty($crest)) {
      return htmlspecialchars($crest);
    }
      
    $teamNameLower = strtolower($teamName);
    $logoMap = [
      'manchester city' => './assets/images/footlogos/colorfullogos/mancity.png',
      'arsenal' => './assets/images/footlogos/arsenal.png',
      'chelsea' => './assets/images/footlogos/colorfullogos/chelscolor.png',
      'newcastle' => './assets/images/footlogos/colorfullogos/newcastle.svg',
    ];
      
    foreach ($logoMap as $key => $path) {
      if (strpos($teamNameLower, $key) !== false) {
        return $path;
      }
    }  
    return null;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>League Standings</title>
  <link rel="stylesheet" href="./assets/css/standings.css" />
  <link rel="stylesheet" href="./assets/css/style.css" />

</head>
<body>
    <?php 
    include("./assets/php/header.php")
    ?>
  <main class="standings-page">
    <div class="standings-header">
      <h1 class="standings-title"><?php echo htmlspecialchars($leagueName); ?> Table</h1>
      
      <div class="league-selector-container">
        <label for="league-select" class="league-selector-label">League:</label>
        <select id="league-select" class="league-select">
          <?php foreach ($LEAGUE_OPTIONS as $code => $league): ?>
            <option value="<?php echo htmlspecialchars($code); ?>" <?php echo $selectedLeague === $code ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($league['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="standings-error">
        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <section class="standings-list">
      <?php if (empty($standings) && !$error): ?>
        <div class="standings-empty">
          No standings data available at this time.
        </div>
      <?php else: ?>
        <?php foreach ($standings as $team): ?>
          <article class="team-row <?php echo getRankClass($team['position']); ?>">
            <div class="team-rank-badge">
              <span><?php echo htmlspecialchars($team['position']); ?></span>
            </div>
            <div class="team-row-content">
              <div class="team-header">
                <div class="team-main">
                  <?php 
                  $logoUrl = getTeamLogo($team['team']['crest'] ?? null, $team['team']['name'] ?? '');
                  if ($logoUrl): 
                  ?>
                    <span class="team-logo">
                      <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($team['team']['name'] ?? ''); ?> logo" onerror="this.style.display='none';">
                    </span>
                  <?php endif; ?>
                  <span class="team-name"><?php echo htmlspecialchars($team['team']['name'] ?? 'Unknown'); ?></span>
                </div>
              </div>
              <div class="team-stats">
                <div class="stats-cols">
                  <div class="stat">
                    <span class="stat-label">P</span>
                    <span class="stat-value"><?php echo htmlspecialchars($team['played'] ?? 0); ?></span>
                  </div>
                  <div class="stat">
                    <span class="stat-label">W</span>
                    <span class="stat-value stat-win"><?php echo htmlspecialchars($team['won'] ?? 0); ?></span>
                  </div>
                  <div class="stat">
                    <span class="stat-label">D</span>
                    <span class="stat-value stat-draw"><?php echo htmlspecialchars($team['drawn'] ?? 0); ?></span>
                  </div>
                  <div class="stat">
                    <span class="stat-label">L</span>
                    <span class="stat-value stat-loss"><?php echo htmlspecialchars($team['lost'] ?? 0); ?></span>
                  </div>
                  <div class="stat">
                    <span class="stat-label">GF</span>
                    <span class="stat-value"><?php echo htmlspecialchars($team['goals_for'] ?? 0); ?></span>
                  </div>
                  <div class="stat">
                    <span class="stat-label">GA</span>
                    <span class="stat-value"><?php echo htmlspecialchars($team['goals_against'] ?? 0); ?></span>
                  </div>
                  <div class="stat">
                    <span class="stat-label">GD</span>
                    <?php 
                    $gd = $team['goal_difference'] ?? 0;
                    $gdClass = $gd >= 0 ? 'stat-gd-positive' : 'stat-gd-negative';
                    ?>
                    <span class="stat-value <?php echo $gdClass; ?>"><?php echo formatGoalDifference($gd); ?></span>
                  </div>
                </div>
                <div class="team-points">
                  <span class="points-label">PTS</span>
                  <span class="points-value"><?php echo htmlspecialchars($team['points'] ?? 0); ?></span>
                </div>
                <div class="team-form">
                  <?php echo renderFormBadges($team['form'] ?? ''); ?>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>

<script>
  const leagueSelect = document.getElementById('league-select');
  if (leagueSelect) {
    leagueSelect.addEventListener('change', function() {
      const selectedLeague = this.value;
      window.location.href = '?league=' + encodeURIComponent(selectedLeague);
    });
  }
</script>