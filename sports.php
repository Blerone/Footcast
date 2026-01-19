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
        <h2 class="section-title">Popular Sports</h2>

        <div class="popular-sports-row">
          <article class="sport-card">
            <h3 class="sport-name">Football</h3>
            <p class="sport-sub">234 matches</p>
          </article>

          <article class="sport-card">
            <h3 class="sport-name">Formula 1</h3>
            <p class="sport-sub">28 matches</p>
          </article>

          <article class="sport-card">
            <h3 class="sport-name">Basketball</h3>
            <p class="sport-sub">126 matches</p>
          </article>

          <article class="sport-card">
            <h3 class="sport-name">Volleyball</h3>
            <p class="sport-sub">34 matches</p>
          </article>

          <article class="sport-card">
            <h3 class="sport-name">Ice Hockey</h3>
            <p class="sport-sub">24 matches</p>
          </article>
        </div>
      </section>

      <section class="section">
        <h2 class="section-title">Top Leagues</h2>

        <div class="leagues-grid">
          <article class="league-card">
            <div class="league-left">
              <div class="league-title">UEFA Champions League</div>
              <div class="league-country">Europe</div>
            </div>
            <div class="league-right">
              <span class="league-count">16</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">Premier League</div>
              <div class="league-country">England</div>
            </div>
            <div class="league-right">
              <span class="league-count">38</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">La Liga</div>
              <div class="league-country">Spain</div>
            </div>
            <div class="league-right">
              <span class="league-count">34</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">Serie A</div>
              <div class="league-country">Italy</div>
            </div>
            <div class="league-right">
              <span class="league-count">32</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">Bundesliga</div>
              <div class="league-country">Germany</div>
            </div>
            <div class="league-right">
              <span class="league-count">26</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">Ligue 1</div>
              <div class="league-country">France</div>
            </div>
            <div class="league-right">
              <span class="league-count">26</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">MLS</div>
              <div class="league-country">USA</div>
            </div>
            <div class="league-right">
              <span class="league-count">22</span>
              <span class="league-label">matches</span>
            </div>
          </article>

          <article class="league-card">
            <div class="league-left">
              <div class="league-title">Primeira Liga</div>
              <div class="league-country">Brazil</div>
            </div>
            <div class="league-right">
              <span class="league-count">18</span>
              <span class="league-label">matches</span>
            </div>
          </article>
        </div>
      </section>

      <section class="newsletter">
        <div class="newsletter-overlay">
          <h2 class="newsletter-title">SUBSCRIBE TO OUR NEWSLETTER</h2>
          <form class="newsletter-form">
            <input
              type="email"
              class="input-newsletter"
              placeholder="Enter your email address..."
              required
            />
            <button type="submit" class="btn-newsletter">
              Subscribe
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