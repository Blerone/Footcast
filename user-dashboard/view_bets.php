<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bets - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/bets.css">
</head>
<body>
    <?php include("./assets/php/nav.php"); ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="dashboard-heading">
                <h1>My Bets</h1>
                <p>View all bets you have placed and their current status.</p>
            </div>
            <div class="dashboard-actions">
                <button id="settle-bets-btn" class="settle-bets-btn">
                    Settle Bets
                </button>
            </div>
        </div>

        <div class="bets-list" id="bets-list">
            <div class="loading">Loading your bets...</div>
        </div>
    </div>

    <?php include("../assets/php/footer.php"); ?>

    <script src="assets/js/my_bets.js"></script>
    <script src="assets/js/nav.js"></script>
</body>
</html>
