<?php
declare(strict_types=1);

require_once __DIR__ . '/db_connection.php';

$db = footcast_db();
$lineupId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$match = null;

if ($lineupId > 0) {
    $stmtMatch = $db->prepare(
        'SELECT id, home_team, away_team, competition, match_date, home_logo, away_logo, home_formation, away_formation, home_coach, away_coach
         FROM lineup_matches
         WHERE id = ?
         LIMIT 1'
    );
    if ($stmtMatch) {
        $stmtMatch->bind_param('i', $lineupId);
        $stmtMatch->execute();
        $result = $stmtMatch->get_result();
        $match = $result ? $result->fetch_assoc() : null;
        $stmtMatch->close();
    }
} else {
    $stmtMatch = $db->prepare(
        'SELECT id, home_team, away_team, competition, match_date, home_logo, away_logo, home_formation, away_formation, home_coach, away_coach
         FROM lineup_matches
         ORDER BY match_date DESC
         LIMIT 1'
    );
    if ($stmtMatch) {
        $stmtMatch->execute();
        $result = $stmtMatch->get_result();
        $match = $result ? $result->fetch_assoc() : null;
        $stmtMatch->close();
    }
}

$starters = ['home' => [], 'away' => []];
$bench = ['home' => [], 'away' => []];
$subs = ['home' => [], 'away' => []];
$injuries = ['home' => [], 'away' => []];

if ($match) {
    $matchId = (int) $match['id'];

    $stmtPlayers = $db->prepare(
        'SELECT team_side, player_name, player_number, position_label, pos_x, pos_y, is_starter
         FROM lineup_players
         WHERE lineup_match_id = ?
         ORDER BY is_starter DESC, id ASC'
    );
    if ($stmtPlayers) {
        $stmtPlayers->bind_param('i', $matchId);
        $stmtPlayers->execute();
        $result = $stmtPlayers->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $side = $row['team_side'] === 'away' ? 'away' : 'home';
                if ((int) $row['is_starter'] === 1) {
                    $starters[$side][] = $row;
                } else {
                    $bench[$side][] = $row;
                }
            }
        }
        $stmtPlayers->close();
    }

    $stmtSubs = $db->prepare(
        'SELECT team_side, minute, player_out, player_in
         FROM lineup_substitutions
         WHERE lineup_match_id = ?
         ORDER BY minute ASC, id ASC'
    );
    if ($stmtSubs) {
        $stmtSubs->bind_param('i', $matchId);
        $stmtSubs->execute();
        $result = $stmtSubs->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $side = $row['team_side'] === 'away' ? 'away' : 'home';
                $subs[$side][] = $row;
            }
        }
        $stmtSubs->close();
    }

    $stmtInjuries = $db->prepare(
        'SELECT team_side, player_name, reason, type
         FROM lineup_injuries
         WHERE lineup_match_id = ?
         ORDER BY id ASC'
    );
    if ($stmtInjuries) {
        $stmtInjuries->bind_param('i', $matchId);
        $stmtInjuries->execute();
        $result = $stmtInjuries->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $side = $row['team_side'] === 'away' ? 'away' : 'home';
                $injuries[$side][] = $row;
            }
        }
        $stmtInjuries->close();
    }
}

$db->close();

$matchData = is_array($match) ? $match : [];
$homeTeam = $matchData['home_team'] ?? 'Home';
$awayTeam = $matchData['away_team'] ?? 'Away';
$competition = $matchData['competition'] ?? 'Competition';
$homeLogo = $matchData['home_logo'] ?? '';
$awayLogo = $matchData['away_logo'] ?? '';
$homeFormation = $matchData['home_formation'] ?? null;
$awayFormation = $matchData['away_formation'] ?? null;
$homeCoach = $matchData['home_coach'] ?? 'TBD';
$awayCoach = $matchData['away_coach'] ?? 'TBD';
$matchDate = $matchData['match_date'] ?? null;

function formatMatchTime(?string $raw): string
{
    if (!$raw) {
        return 'TBD';
    }
    try {
        $dt = new DateTimeImmutable($raw);
        return $dt->format('g:i');
    } catch (Throwable $error) {
        return 'TBD';
    }
}

function formatFormation(?string $formation): string
{
    $value = trim((string) $formation);
    return $value === '' ? 'TBD' : $value;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>FootCast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./assets/css/lineups.css" />
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body>
 <?php 
  include("./assets/php/header.php")
 ?>

 <div class="bz-page">
    <section class="match-hero">
      <div class="match-hero-inner">
        <div class="team-block">
          <div class="team-logo">
            <?php if ($homeLogo !== ''): ?>
              <img src="<?php echo htmlspecialchars($homeLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($homeTeam, ENT_QUOTES, 'UTF-8'); ?> logo" />
            <?php endif; ?>
          </div>
          <div class="team-name"><?php echo htmlspecialchars($homeTeam, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>

        <div class="match-time">
          <?php echo htmlspecialchars(formatMatchTime($matchDate), ENT_QUOTES, 'UTF-8'); ?>
          <span><?php echo htmlspecialchars($competition, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="team-block team-right">
          <div class="team-logo team-logo-away">
            <?php if ($awayLogo !== ''): ?>
              <img src="<?php echo htmlspecialchars($awayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($awayTeam, ENT_QUOTES, 'UTF-8'); ?> logo" />
            <?php endif; ?>
          </div>
          <div class="team-name team-name-right"><?php echo htmlspecialchars($awayTeam, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
      </div>
    </section>

    <section class="match-tabs">
      <div class="match-content">
        <div class="tab-panel active" data-tab="lineups">
          <div class="lineup-wrapper">
            <div class="lineup-header-bar">
              <div>
                <strong><?php echo htmlspecialchars($homeTeam, ENT_QUOTES, 'UTF-8'); ?></strong>
                <span> · <?php echo htmlspecialchars(formatFormation($homeFormation), ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <span>Coach: <?php echo htmlspecialchars($homeCoach, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="lineup-header-bar">
              <div>
                <strong><?php echo htmlspecialchars($awayTeam, ENT_QUOTES, 'UTF-8'); ?></strong>
                <span> · <?php echo htmlspecialchars(formatFormation($awayFormation), ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <span>Coach: <?php echo htmlspecialchars($awayCoach, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <?php if (!empty($starters['home']) || !empty($starters['away'])): ?>
              <div class="pitch">
                <?php foreach ($starters['home'] as $player): ?>
                  <?php if ($player['pos_x'] !== null && $player['pos_y'] !== null): ?>
                    <div class="player" style="top: <?php echo htmlspecialchars((string) $player['pos_y'], ENT_QUOTES, 'UTF-8'); ?>%; left: <?php echo htmlspecialchars((string) $player['pos_x'], ENT_QUOTES, 'UTF-8'); ?>%;">
                      <div class="player-circle">
                        <span class="player-number"><?php echo htmlspecialchars((string) ($player['player_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <div class="player-name"><?php echo htmlspecialchars($player['player_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>

                <?php foreach ($starters['away'] as $player): ?>
                  <?php if ($player['pos_x'] !== null && $player['pos_y'] !== null): ?>
                    <div class="player" style="top: <?php echo htmlspecialchars((string) $player['pos_y'], ENT_QUOTES, 'UTF-8'); ?>%; left: <?php echo htmlspecialchars((string) $player['pos_x'], ENT_QUOTES, 'UTF-8'); ?>%;">
                      <div class="player-circle player-circle-away">
                        <span class="player-number"><?php echo htmlspecialchars((string) ($player['player_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <div class="player-name"><?php echo htmlspecialchars($player['player_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="standings-placeholder">Lineup positions not available yet.</div>
            <?php endif; ?>

            <div class="subs-card">
              <h3 class="subs-title">Substitutions</h3>

              <div class="subs-rows">
                <?php
                  $subsHome = $subs['home'];
                  $subsAway = $subs['away'];
                  $subsCount = max(count($subsHome), count($subsAway));
                ?>
                <?php if ($subsCount === 0): ?>
                  <div class="standings-placeholder">No substitutions recorded.</div>
                <?php else: ?>
                  <?php for ($i = 0; $i < $subsCount; $i++): ?>
                    <div class="subs-row">
                      <?php $homeSub = $subsHome[$i] ?? null; ?>
                      <div class="subs-side">
                        <?php if ($homeSub): ?>
                          <div class="subs-minute"><?php echo htmlspecialchars((string) ($homeSub['minute'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>'</div>
                          <div class="subs-body">
                            <span class="subs-icon subs-out">↓</span>
                            <div class="subs-text">
                              <span class="subs-player-out"><?php echo htmlspecialchars($homeSub['player_out'], ENT_QUOTES, 'UTF-8'); ?></span>
                              <span class="subs-player-in"><?php echo htmlspecialchars($homeSub['player_in'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                          </div>
                        <?php else: ?>
                          <div class="subs-minute"></div>
                          <div class="subs-body"></div>
                        <?php endif; ?>
                      </div>
                      <?php $awaySub = $subsAway[$i] ?? null; ?>
                      <div class="subs-side">
                        <?php if ($awaySub): ?>
                          <div class="subs-minute"><?php echo htmlspecialchars((string) ($awaySub['minute'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>'</div>
                          <div class="subs-body">
                            <span class="subs-icon subs-out">↓</span>
                            <div class="subs-text">
                              <span class="subs-player-out"><?php echo htmlspecialchars($awaySub['player_out'], ENT_QUOTES, 'UTF-8'); ?></span>
                              <span class="subs-player-in"><?php echo htmlspecialchars($awaySub['player_in'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                          </div>
                        <?php else: ?>
                          <div class="subs-minute"></div>
                          <div class="subs-body"></div>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endfor; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="bench-card">
              <h3 class="subs-title">Substitute players</h3>

              <div class="bench-rows">
                <?php
                  $benchHome = $bench['home'];
                  $benchAway = $bench['away'];
                  $benchCount = max(count($benchHome), count($benchAway));
                ?>
                <?php if ($benchCount === 0): ?>
                  <div class="standings-placeholder">No bench players listed.</div>
                <?php else: ?>
                  <?php for ($i = 0; $i < $benchCount; $i++): ?>
                    <div class="bench-row">
                      <?php $homeBench = $benchHome[$i] ?? null; ?>
                      <div class="bench-player">
                        <?php if ($homeBench): ?>
                          <span class="bench-number"><?php echo htmlspecialchars((string) ($homeBench['player_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                          <span class="bench-name"><?php echo htmlspecialchars($homeBench['player_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                      </div>
                      <?php $awayBench = $benchAway[$i] ?? null; ?>
                      <div class="bench-player">
                        <?php if ($awayBench): ?>
                          <span class="bench-number"><?php echo htmlspecialchars((string) ($awayBench['player_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                          <span class="bench-name"><?php echo htmlspecialchars($awayBench['player_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endfor; ?>
                <?php endif; ?>
              </div>
            </div>

            <section class="injuries-section">
              <h3 class="injuries-title">Injuries &amp; suspensions</h3>

              <div class="injury-card">
                <div class="injury-rows">
                  <?php
                    $injHome = $injuries['home'];
                    $injAway = $injuries['away'];
                    $injCount = max(count($injHome), count($injAway));
                  ?>
                  <?php if ($injCount === 0): ?>
                    <div class="standings-placeholder">No injuries or suspensions reported.</div>
                  <?php else: ?>
                    <?php for ($i = 0; $i < $injCount; $i++): ?>
                      <div class="injury-row">
                        <?php $homeInj = $injHome[$i] ?? null; ?>
                        <div class="injury-item">
                          <?php if ($homeInj): ?>
                            <?php $homeIsSusp = ($homeInj['type'] ?? 'injury') === 'suspension'; ?>
                            <span class="injury-icon <?php echo $homeIsSusp ? 'injury-icon-susp' : 'injury-icon-injury'; ?>">
                              <?php echo $homeIsSusp ? '⛔' : '✚'; ?>
                            </span>
                            <div>
                              <div class="injury-name"><?php echo htmlspecialchars($homeInj['player_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                              <div class="injury-reason"><?php echo htmlspecialchars($homeInj['reason'] ?? 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php $awayInj = $injAway[$i] ?? null; ?>
                        <div class="injury-item">
                          <?php if ($awayInj): ?>
                            <?php $awayIsSusp = ($awayInj['type'] ?? 'injury') === 'suspension'; ?>
                            <span class="injury-icon <?php echo $awayIsSusp ? 'injury-icon-susp' : 'injury-icon-injury'; ?>">
                              <?php echo $awayIsSusp ? '⛔' : '✚'; ?>
                            </span>
                            <div>
                              <div class="injury-name"><?php echo htmlspecialchars($awayInj['player_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                              <div class="injury-reason"><?php echo htmlspecialchars($awayInj['reason'] ?? 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endfor; ?>
                  <?php endif; ?>
                </div>
              </div>
            </section>
          </div>
        </div>

        <div class="tab-panel" data-tab="standings"></div>
      </div>
    </section>

  </div>
  <?php
    include('./assets/php/footer.php');
  ?>
</body>
</html>
