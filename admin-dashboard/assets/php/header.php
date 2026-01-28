<header class="header">
    <div class="container">
        <div class="header-inner">
            <div class="left-section">
                <h1 class="logo-text">FootCast</h1>
                <nav>
                <button class="nav-link">
                    <button class="nav-link active"><a href="./index.php">Dashboard</a></button>
                    <button class="nav-link active"><a href="../index.php">Home Page</a></button>
                    <button class="nav-link active"><a href="./users.php">Sports</a></button>
                    <button class="nav-link active"><a href="./users.php">Promotions</a></button>
                    <button class="nav-link active"><a href="./users.php">Users</a></button>
                    <button class="nav-link active"><a href="./bets.php">Bets</a></button>
                    <button class="nav-link active"><a href="./lineups.php">Lineups</a></button>
                </nav>
            </div>

            <div class="right-section">
                <?php
                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        session_start();
                    }
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
        <a class="nav-link" href="../index.php">Home</a>
        <a class="nav-link" href="./index.php">Overview</a>
        <a class="nav-link" href="./users.php">Users</a>
        <a class="nav-link" href="./bets.php">Bets</a>
        <a class="nav-link" href="../matches.php">Matches</a>
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
</body>
</html>