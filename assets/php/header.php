<?php
session_start();
?>
<header class="header">
    <div class="container">
        <div class="header-inner">
            <div class="left-section">
                <h1 class="logo-text">FOOTCAST</h1>
                <nav class="">
                    <button class="nav-link active"><a href="index.php" class="nav-link active">Home</a></button>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="nav-link"><a href="user-dashboard/index.php">Dashboard</a></button>
                    <?php endif; ?>
                    <button class="nav-link"><a href="matches.php">Matches</a></button>
                    <button class="nav-link"><a href="standings.php">Standings</a></button>
                    <button class="nav-link"><a href="sports.php">Sports</a></button>
                    <button class="nav-link"><a href="promotions.php">Promotions</a></button>
                </nav>
            </div>
            <div class="right-section">
                <?php
                    if (isset($_SESSION['user_id'])) {
                        echo '<button class="btn outline-btn" style="margin-left: 10px;">
                            <a href="logout.php" class="login-link">Logout</a>
                        </button>';
                    } else {
                        echo '<button class="btn outline-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="user-icon" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.67 0 8 1.34 8 4v4H4v-4c0-2.66 5.33-4 8-4zm0-2a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                            </svg>
                            <a href="login.php" class="login-link">Login</a>
                        </button>';
                    }
                ?>
                <button class="menu-toggle" id="menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="mobile-nav" id="mobile-nav">
        <button class="nav-link active"><a href="index.php">Home</a></button>
        <?php if (isset($_SESSION['user_id'])): ?>
            <button class="nav-link"><a href="user-dashboard/index.php">Dashboard</a></button>
        <?php endif; ?>
        <button class="nav-link"><a href="matches.php">Matches</a></button>
        <button class="nav-link"><a href="standings.php">Standings</a></button>
        <button class="nav-link"><a href="sports.php">Sports</a></button>
        <button class="nav-link"><a href="promotions.php">Promotions</a></button>
    </div>
</header>


<script>
    const toggleBtn = document.getElementById("menu-toggle");
    const mobileNav = document.getElementById("mobile-nav");
    const header = document.querySelector(".header");

    if (toggleBtn && mobileNav) {
      toggleBtn.addEventListener("click", () => {
        mobileNav.classList.toggle("open");
        toggleBtn.classList.toggle("open");
      });
    }

    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        header.classList.add("scrolled");
      } else {
        header.classList.remove("scrolled");
      }
    });
</script>
