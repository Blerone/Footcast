<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Promotions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./assets/css/promotions.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
</head>
<body>
  <?php 
    include("./assets/php/header.php")
  ?>
  <main>
    <h3 class="page-title">Active Promotions</h3>
    <section class="promotions-grid">
      <div class="promo-card card-purple">
        <div class="promo-card-inner">
          <div class="promo-header">
            <div class="promo-icon purple"><span class="material-symbols-outlined">
              featured_seasonal_and_gifts
              </span></div>
            <div class="promo-top-right">
              <span class="promo-main-tag tag-green">New Users</span>
            </div>
          </div>

          <div class="promo-body">
            <div>
              <h3 class="promo-title">Welcome Bonus</h3>
              <p class="promo-text">
                Get 100% match on your first deposit up to $500.
              </p>
            </div>

            <div>
              <p class="promo-label">Promo Code</p>
              <div class="promo-code-box">
                <span class="promo-code">WELCOME100</span>
              </div>
            </div>

            <div class="promo-footer">
              <button class="btn-claim" data-code="WELCOME100">Claim Offer</button>
            </div>
          </div>
        </div>
      </div>

      <div class="promo-card card-blue">
        <div class="promo-card-inner">
          <div class="promo-header">
            <div class="promo-icon blue"><span class="material-symbols-outlined">calendar_today</span></div>
            <div class="promo-top-right">
              <span class="small-pill pill-weekly">Weekly</span>
            </div>
          </div>

          <div class="promo-body">
            <div>
              <h3 class="promo-title">Free Bet Friday</h3>
              <p class="promo-text">
                Place 5 bets during the week, get a free $20 bet on Friday.
              </p>
            </div>

            <div>
              <p class="promo-label">Promo Code</p>
              <div class="promo-code-box">
                <span class="promo-code">FRIDAY20</span>
              </div>
            </div>

            <div class="promo-footer">
              <button class="btn-claim" data-code="FRIDAY20">Claim Offer</button>
            </div>
          </div>
        </div>
      </div>

      <div class="promo-card card-orange">
        <div class="promo-card-inner">
          <div class="promo-header">
            <div class="promo-icon orange"><span class="material-symbols-outlined"> call_made</span></div>
            <div class="promo-top-right">
              <span class="small-pill pill-popular">Popular</span>
            </div>
          </div>

          <div class="promo-body">
            <div>
              <h3 class="promo-title">Accumulator Boost</h3>
              <p class="promo-text">
                Up to 50% profit boost on accumulators with 5+ selections.
              </p>
            </div>

            <div>
              <p class="promo-label">Promo Code</p>
              <div class="promo-code-box">
                <span class="promo-code">ACCA50</span>
              </div>
            </div>

            <div class="promo-footer">
              <button class="btn-claim" data-code="ACCA50">Claim Offer</button>
            </div>
          </div>
        </div>
      </div>

      <div class="promo-card card-pink">
        <div class="promo-card-inner">
          <div class="promo-header">
            <div class="promo-icon pink"><span class="material-symbols-outlined">contacts_product</span></div>
            <div class="promo-top-right">
              <span class="promo-main-tag tag-red">Limited</span>
            </div>
          </div>

          <div class="promo-body">
            <div>
              <h3 class="promo-title">Refer a Friend</h3>
              <p class="promo-text">
                Invite your friends and get a $25 free bet for each successful signup.
              </p>
            </div>

            <div>
              <p class="promo-label">Promo Code</p>
              <div class="promo-code-box">
                <span class="promo-code">FRIEND25</span>
              </div>
            </div>

            <div class="promo-footer">
              <button class="btn-claim" data-code="FRIEND25">Claim Offer</button>
            </div>
          </div>
        </div>
      </div>

      <div class="promo-card card-yellow">
        <div class="promo-card-inner">
          <div class="promo-header">
            <div class="promo-icon yellow"><span class="material-symbols-outlined">bolt </span></div>
            <div class="promo-top-right">
              <span class="small-pill pill-daily">Daily</span>
            </div>
          </div>

          <div class="promo-body">
            <div>
              <h3 class="promo-title">Daily Odds Boost</h3>
              <p class="promo-text">
                Get boosted odds on selected matches every day.
              </p>
            </div>

            <div>
              <p class="promo-label">Promo Code</p>
              <div class="promo-code-box">
                <span class="promo-code">BOOST10</span>
              </div>
            </div>

            <div class="promo-footer">
              <button class="btn-claim" data-code="BOOST10">Claim Offer</button>
            </div>
          </div>
        </div>
      </div>

      <div class="promo-card card-red">
        <div class="promo-card-inner">
          <div class="promo-header">
            <div class="promo-icon red"><span class="material-symbols-outlined">
              percent
              </span></div>
            <div class="promo-top-right">
              <span class="small-pill pill-weekly-red">Weekly</span>
            </div>
          </div>

          <div class="promo-body">
            <div>
              <h3 class="promo-title">Cashback Weekend</h3>
              <p class="promo-text">
                Get 10% cashback on your net losses every weekend.
              </p>
            </div>

            <div>
              <p class="promo-label">Promo Code</p>
              <div class="promo-code-box">
                <span class="promo-code">CASHBACK10</span>
              </div>
            </div>

            <div class="promo-footer">
              <button class="btn-claim" data-code="CASHBACK10">Claim Offer</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    
  </main>
    <?php
        include('./assets/php/footer.php');
    ?>
  <script>
    const toggleBtn = document.getElementById('menu-toggle');
    const mobileNav = document.getElementById('mobile-nav');
    const header = document.querySelector('.header');

    toggleBtn.addEventListener('click', () => {
      mobileNav.classList.toggle('open');
      toggleBtn.classList.toggle('open');
    });

    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  </script>
</body>
</html>
