<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/admin-dashboard/assets/includes/HomepageRepository.php';

$db = footcast_db();
$homepageRepository = new HomepageRepository($db);

$heroRows = $homepageRepository->getHeroRows();
$sectionsRows = $homepageRepository->getSectionsRows();
$stepsRows = $homepageRepository->getStepsRows();
$bannerRows = $homepageRepository->getBannerRows();
$leaguesRows = $homepageRepository->getLeaguesRows();
$favoritesRows = $homepageRepository->getFavoritesRows();

$db->close();

$hero = $heroRows[0] ?? [
  'sports_text' => 'SPORTS',
  'bet_text' => 'BET',
];

$sections = $sectionsRows[0] ?? [
  'trusted_by_title' => 'Trusted By',
  'about_title' => 'One click from',
  'about_highlight' => 'Winning It All',
  'about_body' => 'FootCast is your go-to spot for football betting done right. Place smart bets, follow live stats, and stay ahead with real-time insights. From major leagues to local matches, FootCast keeps every game exciting where your passion for football meets the thrill of winning.',
  'bet_steps_title' => 'How to place a BET ?',
  'popular_leagues_title' => 'Popular Leagues',
  'favorites_title' => 'Fan’s FAVORITE',
];

$banner = $bannerRows[0] ?? [
  'home_team' => 'Real Madrid',
  'away_team' => 'Barcelona',
  'days_value' => 3,
  'hours_value' => 12,
  'minutes_value' => 47,
  'seconds_value' => 32,
  'days_label' => 'Days',
  'hours_label' => 'Hours',
  'minutes_label' => 'Minutes',
  'seconds_label' => 'Seconds',
  'odds_first' => '1.4X',
  'odds_second' => '2.3X',
  'odds_third' => '3.4X',
];

$stepsDefaults = [
  ['step_number' => 1, 'step_title' => 'Create an Account', 'sort_order' => 1],
  ['step_number' => 2, 'step_title' => 'Find your Team', 'sort_order' => 2],
  ['step_number' => 3, 'step_title' => 'Place your BET', 'sort_order' => 3],
  ['step_number' => 4, 'step_title' => 'You won? Withdraw Now!', 'sort_order' => 4],
];

$steps = $stepsRows ?: $stepsDefaults;
usort($steps, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

$leaguesDefaults = [
  ['league_name' => 'Premier League', 'stats_value' => '204', 'stats_label' => 'active players', 'top_scorer_label' => 'Top Goal Scorer', 'goals_text' => '60 G/A'],
  ['league_name' => 'Seria A', 'stats_value' => '194', 'stats_label' => 'active players', 'top_scorer_label' => 'Top Goal Scorer', 'goals_text' => '30 G/A'],
  ['league_name' => 'Budensliga', 'stats_value' => '194', 'stats_label' => 'active players', 'top_scorer_label' => 'Top Goal Scorer', 'goals_text' => '30 G/A'],
  ['league_name' => 'La Liga', 'stats_value' => '194', 'stats_label' => 'active players', 'top_scorer_label' => 'Top Goal Scorer', 'goals_text' => '30 G/A'],
];

$activeLeagues = array_values(array_filter($leaguesRows, fn($row) => (int) ($row['is_active'] ?? 0) === 1));
usort($activeLeagues, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

$favoritesDefaults = [
  ['item_label' => 'Player', 'item_name' => 'Haaland'],
  ['item_label' => 'Coach', 'item_name' => 'Maresca'],
  ['item_label' => 'Club', 'item_name' => 'Chelsea'],
  ['item_label' => 'Club', 'item_name' => 'Real Madrid'],
  ['item_label' => 'Club', 'item_name' => 'Bayern Munich'],
  ['item_label' => 'Player', 'item_name' => 'Mbappe'],
  ['item_label' => 'Player', 'item_name' => 'Estêvão'],
  ['item_label' => 'Club', 'item_name' => 'Inter Milan'],
  ['item_label' => 'Coach', 'item_name' => 'Xabi Alonso'],
];

$activeFavorites = array_values(array_filter($favoritesRows, fn($row) => (int) ($row['is_active'] ?? 0) === 1));
usort($activeFavorites, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

function highlightText(string $text, string $keyword): string{
  if ($keyword === '' || strpos($text, $keyword) === false) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  }
  $parts = explode($keyword, $text);
  $escaped = array_map(fn($part) => htmlspecialchars($part, ENT_QUOTES, 'UTF-8'), $parts);
  return implode('<span>' . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . '</span>', $escaped);
}

function pickText(array $row, string $key, string $fallback): string{
  $value = $row[$key] ?? '';
  if (!is_string($value) || trim($value) === '') {
    return $fallback;
  }
  return $value;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FootCast</title>
  <link rel="stylesheet" href="./assets/css/style.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_upward" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_downward" />
</head>

<body>
 <?php 
  include("./assets/php/header.php")
 ?>
  <div class="main-content">
    <div class="content-wrapper">
      <div class="purple-section">
        <div class="hero-text">
          <h2 class="sports-text"><?php echo htmlspecialchars(pickText($hero, 'sports_text', 'SPORTS'), ENT_QUOTES, 'UTF-8'); ?></h2>
          <h2 class="bet-text"><?php echo htmlspecialchars(pickText($hero, 'bet_text', 'BET'), ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>
        <div class="player-right">
          <img src="./assets/images/players/enzo.png" alt="Enzo">
        </div>
      </div>
    </div>
  </div>

  <br><br><br>
  <div class="trusted-by">
    <h2 class="trusted-by-text"><?php echo htmlspecialchars(pickText($sections, 'trusted_by_title', 'Trusted By'), ENT_QUOTES, 'UTF-8'); ?></h2>
    <div class="underline"></div>
  </div>
  <br><br><br><br>
  <div class="slider">
    <div class="slide-track">
      <div class="slide">
        <img src="./assets/images/footlogos/chelses.png" alt="Logo 1" style="width: 100px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/barca.png" alt="Logo 2" style="width: 160px; height: 120px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/real.png" alt="Logo 3" style="width: 140px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/psg.png" alt="Logo 4" style="width: 120px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/arsenal.png" alt="Logo 5" style="width: 130px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/chelses.png" alt="Logo 1" style="width: 100px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/barca.png" alt="Logo 2" style="width: 160px; height: 120px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/real.png" alt="Logo 3" style="width: 140px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/psg.png" alt="Logo 4" style="width: 120px; height: 100px;">
      </div>
      <div class="slide">
        <img src="./assets/images/footlogos/arsenal.png" alt="Logo 5" style="width: 130px; height: 100px;">
      </div>
    </div>
  </div>


  <section class="about-us">
    <div class="left-text">
      <h1>
        <?php echo htmlspecialchars(pickText($sections, 'about_title', 'One click from'), ENT_QUOTES, 'UTF-8'); ?>
        <span><?php echo htmlspecialchars(pickText($sections, 'about_highlight', 'Winning It All'), ENT_QUOTES, 'UTF-8'); ?></span>
      </h1>
      <p><?php echo htmlspecialchars(pickText($sections, 'about_body', 'FootCast is your go-to spot for football betting done right. Place smart bets, follow live stats, and stay ahead with real-time insights. From major leagues to local matches, FootCast keeps every game exciting where your passion for football meets the thrill of winning.'), ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div class="right-image">
      <img src="./assets/images/banners/phone.png" alt="">
    </div>
  </section>

  <section class="bet-steps">
    <h1><?php echo highlightText(pickText($sections, 'bet_steps_title', 'How to place a BET ?'), 'BET'); ?></h1>
    <div class="underline2"></div>
    <div class="bet-content">
      <?php foreach ($steps as $step): ?>
        <div class="bet-step">
          <div class="inside-step">
            <?php echo htmlspecialchars((string) ($step['step_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
          </div>
          <h2><?php echo htmlspecialchars($step['step_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="santiago-banner">
    <div class="santiago-banner-img">
      <img src="./assets/images/footlogos/whitelogos/real1.png" alt="Real Madrid Logo" class="real-madrid-logo">
      <img src="./assets/images/footlogos/whitelogos/barca1.png" alt="Barcelona Logo" class="barcelona-logo">
    </div>

    <div class="time-left">
      <div class="inside-time">
        <h2>3</h2>
        <p><?php echo htmlspecialchars($banner['days_label'] ?? 'Days', ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <div class="inside-time">
        <h2><?php echo htmlspecialchars((string) ($banner['hours_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars($banner['hours_label'] ?? 'Hours', ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <div class="inside-time">
        <h2><?php echo htmlspecialchars((string) ($banner['minutes_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars($banner['minutes_label'] ?? 'Minutes', ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <div class="inside-time">
        <h2><?php echo htmlspecialchars((string) ($banner['seconds_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars($banner['seconds_label'] ?? 'Seconds', ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

    </div>

    <div class="santiago-banner-text">
        <h2><?php echo htmlspecialchars(pickText($banner, 'home_team', 'Real Madrid'), ENT_QUOTES, 'UTF-8'); ?></h2>
      <h2><?php echo htmlspecialchars(pickText($banner, 'away_team', 'Barcelona'), ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>

    <div class="santiago-banner-buttons">
      <button><?php echo htmlspecialchars($banner['odds_first'] ?? '1.4X', ENT_QUOTES, 'UTF-8'); ?></button>
      <button><?php echo htmlspecialchars($banner['odds_second'] ?? '2.3X', ENT_QUOTES, 'UTF-8'); ?></button>
      <button><?php echo htmlspecialchars($banner['odds_third'] ?? '3.4X', ENT_QUOTES, 'UTF-8'); ?></button>
    </div>
  </section>


  <div class="leagues-heading">
    <h1><?php echo htmlspecialchars(pickText($sections, 'popular_leagues_title', 'Popular Leagues'), ENT_QUOTES, 'UTF-8'); ?></h1>
    <div class="underline3"></div>
  </div>
  <section class="leagues">
    <div class="leagues-inner premier">
      <div class="league-box">
        <div class="img-circle">
          <img src="./assets/images/footlogos/leagues/premierleague.png" alt="Premier League"
            style="width:50px; height: auto;">
        </div>
        <div class="league-stats">
          <h4>204 <span class="material-symbols-outlined"> arrow_downward</span></h4>
          <p>active players</p>
        </div>
      </div>

      <?php $league = $activeLeagues[0] ?? $leaguesDefaults[0]; ?>
      <h2><?php echo htmlspecialchars($league['league_name'] ?? 'Premier League', ENT_QUOTES, 'UTF-8'); ?></h2>

      <div class="inside-leagues">
        <div class="leagues-leftside">
          <p class="label"><?php echo htmlspecialchars($league['top_scorer_label'] ?? 'Top Goal Scorer', ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="goals"><?php echo htmlspecialchars($league['goals_text'] ?? '60 G/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="leagues-rightside">
          <img src="./assets/images/players/haaland.png" alt="Erling Haaland">
        </div>
      </div>
    </div>

    <div class="leagues-inner seriea">
      <div class="league-box">
        <div class="img-circle">
          <img src="./assets/images/footlogos/leagues/seria.png" alt="Serie A" style="width: 50px; height: auto;">
        </div>
        <div class="league-stats">
          <h4>194 <span class="material-symbols-outlined"> arrow_downward</span></h4>
          <p>active players</p>
        </div>
      </div>

      <?php $league = $activeLeagues[1] ?? $leaguesDefaults[1]; ?>
      <h2><?php echo htmlspecialchars($league['league_name'] ?? 'Seria A', ENT_QUOTES, 'UTF-8'); ?></h2>

      <div class="inside-leagues">
        <div class="leagues-leftside">
          <p class="label"><?php echo htmlspecialchars($league['top_scorer_label'] ?? 'Top Goal Scorer', ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="goals"><?php echo htmlspecialchars($league['goals_text'] ?? '30 G/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="leagues-rightside">
          <img src="./assets/images/players/calhanoglu.png" alt="">
        </div>
      </div>
    </div>

    <div class="leagues-inner bundesliga">
      <div class="league-box">
        <div class="img-circle">
          <img src="./assets/images/footlogos/leagues/bundesliga.png" alt="Serie A" style="width: 60px; height: auto;">
        </div>
        <div class="league-stats">
          <h4>194 <span class="material-symbols-outlined">arrow_downward</span></h4>
          <p>active players</p>
        </div>
      </div>

      <?php $league = $activeLeagues[2] ?? $leaguesDefaults[2]; ?>
      <h2><?php echo htmlspecialchars($league['league_name'] ?? 'Budensliga', ENT_QUOTES, 'UTF-8'); ?></h2>

      <div class="inside-leagues">
        <div class="leagues-leftside">
          <p class="label"><?php echo htmlspecialchars($league['top_scorer_label'] ?? 'Top Goal Scorer', ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="goals"><?php echo htmlspecialchars($league['goals_text'] ?? '30 G/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="leagues-rightside">
          <img src="./assets/images/players/kane.png" alt="">
        </div>
      </div>
    </div>


    <div class="leagues-inner laliga">
      <div class="league-box">
        <div class="img-circle">
          <img src="./assets/images/footlogos/leagues/laliga1.png" alt="Serie A">
        </div>
        <div class="league-stats">
          <h4>194 <span class="material-symbols-outlined">arrow_downward</span></h4>
          <p>active players</p>
        </div>
      </div>

      <?php $league = $activeLeagues[3] ?? $leaguesDefaults[3]; ?>
      <h2><?php echo htmlspecialchars($league['league_name'] ?? 'La Liga', ENT_QUOTES, 'UTF-8'); ?></h2>

      <div class="inside-leagues">
        <div class="leagues-leftside">
          <p class="label"><?php echo htmlspecialchars($league['top_scorer_label'] ?? 'Top Goal Scorer', ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="goals"><?php echo htmlspecialchars($league['goals_text'] ?? '30 G/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="leagues-rightside">
          <img src="./assets/images/players/mbappe1.png" alt="">
        </div>
      </div>
    </div>
  </section>


  <div class="favorites-heading">
    <h1><?php echo highlightText(pickText($sections, 'favorites_title', 'Fan’s FAVORITE'), 'FAVORITE'); ?></h1>
    <div class="underline3"></div>
  </div>

  <section class="favorites">
    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/players/haaland.png" alt="">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[0] ?? $favoritesDefaults[0]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Player', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Haaland', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>

    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/players/maresca.png" alt="">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[1] ?? $favoritesDefaults[1]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Coach', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Maresca', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>

    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/footlogos/colorfullogos/chelscolor.png" alt="" style="width: 60px; height: 60px;">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[2] ?? $favoritesDefaults[2]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Club', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Chelsea', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>


    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/footlogos/colorfullogos/realcolor.png" alt="" style="width: 70px; height: 70px;">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[3] ?? $favoritesDefaults[3]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Club', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Real Madrid', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>
    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/footlogos/colorfullogos/bayerncolor.png" alt="" style="width: 60px; height: 60px;">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[4] ?? $favoritesDefaults[4]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Club', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Bayern Munich', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>

    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/players/mbappe1.png" alt="">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[5] ?? $favoritesDefaults[5]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Player', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Mbappe', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>

    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/players/estevao.png" alt="">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[6] ?? $favoritesDefaults[6]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Player', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Estêvão', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>

    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/footlogos/colorfullogos/intercolor.png" alt="" style="width: 60px; height: 60px;">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[7] ?? $favoritesDefaults[7]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Club', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Inter Milan', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>

    <div class="inside-favorites">
      <div class="inside-favorites-img">
        <img src="./assets/images/players/alonso.png" alt="">
      </div>
      <div class="inside-favorites-text">
        <?php $favorite = $activeFavorites[8] ?? $favoritesDefaults[8]; ?>
        <p><?php echo htmlspecialchars($favorite['item_label'] ?? 'Coach', ENT_QUOTES, 'UTF-8'); ?></p>
        <h2><?php echo htmlspecialchars($favorite['item_name'] ?? 'Xabi Alonso', ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
    </div>
  </section>

  <?php
    include('./assets/php/footer.php');
  ?>
</body>
</html>
