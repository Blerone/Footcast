<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Promotions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/promotions.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="header-inner">
        <div class="left-section">
          <h1 class="logo-text">FOOTCAST</h1>
          <nav class="">
            <button class="nav-link active"><a href="index.php" class="nav-link active">Home</a></button>
            <button class="nav-link"><a href="matches.php">Matches</a></button>
            <button class="nav-link"><a href="">Results</a></button>
            <button class="nav-link"><a href="sports.php">Sports</a></button>
            <button class="nav-link"><a href="promotions.php">Promotions</a></button>
          </nav>
        </div>
        <div class="right-section">
          <div class="search-box">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
              viewBox="0 0 24 24">
              <path
                d="M10,18a8,8,0,1,1,5.29-13.71A8,8,0,0,1,10,18Zm9.71,1.29-4.1-4.1A9.94,9.94,0,0,0,20,10a10,10,0,1,0-10,10,9.94,9.94,0,0,0,5.19-1.39l4.1,4.1a1,1,0,0,0,1.42-1.42Z" />
            </svg>
            <input type="text" placeholder="Search matches..." />
          </div>
          <button class="btn outline-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="user-icon" width="16" height="16" fill="currentColor"
              viewBox="0 0 24 24">
              <path d="M12 12c2.67 0 8 1.34 8 4v4H4v-4c0-2.66 5.33-4 8-4zm0-2a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
            </svg>
            <a href="login.php" class="login-link">Login</a>
          </button>
          <button class="menu-toggle" id="menu-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
          </button>
        </div>
      </div>
    </div>

    <div class="mobile-nav" id="mobile-nav">
      <button class="nav-link active">Home</button>
      <button class="nav-link">Matches</button>
      <button class="nav-link">Results</button>
      <button class="nav-link">Stats</button>
      <button class="nav-link">Promotions</button>
    </div>
  </header>


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
  <section class="footer">
    <div class="left-footer">
      <h2>FOOTCAST</h2>
      <p>One click away, from winning it all.</p>

      <div class="social-links">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
        <a href="#"><i class="fab fa-x-twitter"></i></a>
      </div>

      <p class="footer-copy">© 2025 Blerona Thaci &amp; Vesa Susuri | All Rights Reserved</p>
    </div>

    <div class="right-footer">
      <h2>Company</h2>
      <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Our Team</a></li>
        <li><a href="#">Our Work</a></li>
        <li><a href="#">Partners</a></li>
        <li><a href="#">Clients</a></li>
      </ul>
    </div>

    <div class="right-footer">
      <h2>Support</h2>
      <ul>
        <li><a href="#">Contact Us</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Q &amp; A</a></li>
        <li><a href="#">Affiliates</a></li>
      </ul>
    </div>

    <div class="right-footer">
      <h2>Trust</h2>
      <ul>
        <li><a href="#">User Trust</a></li>
        <li><a href="#">Guidelines</a></li>
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms of Use</a></li>
        <li><a href="#">Security</a></li>
      </ul>
    </div>
  </section>
  
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
