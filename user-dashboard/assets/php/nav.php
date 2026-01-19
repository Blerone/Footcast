<header class="header">
    <div class="container">
        <div class="header-inner">
            <div class="left-section">
                <h1 class="logo-text">FOOTCAST</h1>
                <nav>
                <button class="nav-link">
                    <button class="nav-link active"><a href="../index.php">Home</a></button>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="nav-link"><a href="user-dashboard/index.php">Dashboard</a></button>
                    <?php endif; ?>
                    <button class="nav-link"><a href="../matches.php">Matches</a></button>
                    <button class="nav-link"><a href="../standings.php">Standings</a></button>
                    <button class="nav-link"><a href="view_bets.php">My Bets</a></button>
                    
                </nav>
            </div>

            <div class="right-section">
                <?php
                    session_start();
                    if (isset($_SESSION['user_id'])) {
                        echo '
                        <button class="btn outline-btn" style="margin-left: 10px;">
                            <a href="../logout.php" class="login-link">Logout</a>
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
        <button class="nav-link"><a href="../index.php">Home</a></button>
        <button class="nav-link"><a href="../matches.php">Matches</a></button>
        <button class="nav-link"><a href="../standings.php">Standings</a></button>
        <button class="nav-link"><a href="view_bets.php">My Bets</a></button>

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
