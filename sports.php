<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/admin-dashboard/assets/includes/SportsRepository.php';

$db = footcast_db();
$sportsRepository = new SportsRepository($db);

$sectionsRows = $sportsRepository->getSections();
$sportsRows = $sportsRepository->getSports();
$leaguesRows = $sportsRepository->getLeagues();

$db->close();

$sections = $sectionsRows[0] ?? [
  'popular_sports_title' => 'Popular Sports',
  'top_leagues_title' => 'Top Leagues',
  'newsletter_title' => 'SUBSCRIBE TO OUR NEWSLETTER',
  'newsletter_placeholder' => 'Enter your email address...',
  'newsletter_button_text' => 'Subscribe',
];

$sportsDefaults = [
  ['sport_name' => 'Football', 'matches_count' => 234, 'matches_label' => 'matches', 'sort_order' => 1],
  ['sport_name' => 'Formula 1', 'matches_count' => 28, 'matches_label' => 'matches', 'sort_order' => 2],
  ['sport_name' => 'Basketball', 'matches_count' => 126, 'matches_label' => 'matches', 'sort_order' => 3],
  ['sport_name' => 'Volleyball', 'matches_count' => 34, 'matches_label' => 'matches', 'sort_order' => 4],
  ['sport_name' => 'Ice Hockey', 'matches_count' => 24, 'matches_label' => 'matches', 'sort_order' => 5],
];

$activeSports = array_values(array_filter($sportsRows, fn($row) => (int) ($row['is_active'] ?? 0) === 1));
usort($activeSports, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

$leaguesDefaults = [
  ['league_title' => 'UEFA Champions League', 'league_country' => 'Europe', 'matches_count' => 16, 'matches_label' => 'matches', 'sort_order' => 1],
  ['league_title' => 'Premier League', 'league_country' => 'England', 'matches_count' => 38, 'matches_label' => 'matches', 'sort_order' => 2],
  ['league_title' => 'La Liga', 'league_country' => 'Spain', 'matches_count' => 34, 'matches_label' => 'matches', 'sort_order' => 3],
  ['league_title' => 'Serie A', 'league_country' => 'Italy', 'matches_count' => 32, 'matches_label' => 'matches', 'sort_order' => 4],
  ['league_title' => 'Bundesliga', 'league_country' => 'Germany', 'matches_count' => 26, 'matches_label' => 'matches', 'sort_order' => 5],
  ['league_title' => 'Ligue 1', 'league_country' => 'France', 'matches_count' => 26, 'matches_label' => 'matches', 'sort_order' => 6],
  ['league_title' => 'MLS', 'league_country' => 'USA', 'matches_count' => 22, 'matches_label' => 'matches', 'sort_order' => 7],
  ['league_title' => 'Primeira Liga', 'league_country' => 'Brazil', 'matches_count' => 18, 'matches_label' => 'matches', 'sort_order' => 8],
];

$activeLeagues = array_values(array_filter($leaguesRows, fn($row) => (int) ($row['is_active'] ?? 0) === 1));
usort($activeLeagues, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="./assets/css/sports.css" />
</head>
<body>
  <?php 
    include("./assets/php/header.php")
  ?>
    <main class="main">
      <section class="section">
        <h2 class="section-title"><?php echo htmlspecialchars($sections['popular_sports_title'] ?? 'Popular Sports', ENT_QUOTES, 'UTF-8'); ?></h2>

        <div class="popular-sports-row">
          <?php $sportsList = $activeSports ?: $sportsDefaults; ?>
          <?php foreach ($sportsList as $sport): ?>
            <article class="sport-card">
              <h3 class="sport-name"><?php echo htmlspecialchars($sport['sport_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="sport-sub">
                <?php echo htmlspecialchars((string) ($sport['matches_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>
                <?php echo htmlspecialchars($sport['matches_label'] ?? 'matches', ENT_QUOTES, 'UTF-8'); ?>
              </p>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="section">
        <h2 class="section-title"><?php echo htmlspecialchars($sections['top_leagues_title'] ?? 'Top Leagues', ENT_QUOTES, 'UTF-8'); ?></h2>

        <div class="leagues-grid">
          <?php $leagueList = $activeLeagues ?: $leaguesDefaults; ?>
          <?php foreach ($leagueList as $league): ?>
            <article class="league-card">
              <div class="league-left">
                <div class="league-title"><?php echo htmlspecialchars($league['league_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="league-country"><?php echo htmlspecialchars($league['league_country'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
              </div>
              <div class="league-right">
                <span class="league-count"><?php echo htmlspecialchars((string) ($league['matches_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="league-label"><?php echo htmlspecialchars($league['matches_label'] ?? 'matches', ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="newsletter">
        <div class="newsletter-overlay">
          <h2 class="newsletter-title"><?php echo htmlspecialchars($sections['newsletter_title'] ?? 'SUBSCRIBE TO OUR NEWSLETTER', ENT_QUOTES, 'UTF-8'); ?></h2>
          <form class="newsletter-form">
            <input
              type="email"
              class="input-newsletter"
              placeholder="<?php echo htmlspecialchars($sections['newsletter_placeholder'] ?? 'Enter your email address...', ENT_QUOTES, 'UTF-8'); ?>"
              required
            />
            <button type="submit" class="btn-newsletter">
              <?php echo htmlspecialchars($sections['newsletter_button_text'] ?? 'Subscribe', ENT_QUOTES, 'UTF-8'); ?>
            </button>
          </form>
        </div>
      </section>
    </main>
  </div>

  <?php
    include('./assets/php/footer.php');
  ?>

</body>
</html>
