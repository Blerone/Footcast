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
</head>
<body>
    <?php include("./assets/php/nav.php"); ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="dashboard-heading">
                <h1>My Bets</h1>
                <p>Track pending bets, history, and parlays.</p>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-tab="pending">Pending Bets</button>
            <button class="tab-btn" data-tab="history">Bet History</button>
            <button class="tab-btn" data-tab="parlays">Parlays</button>
        </div>

        <div class="tab-content active" id="pending-tab">
            <div class="bets-list" id="pending-bets-list">
                <div class="loading">Loading pending bets...</div>
            </div>
        </div>

        <div class="tab-content" id="history-tab">
            <div class="bets-list" id="history-bets-list">
                <div class="loading">Loading bet history...</div>
            </div>
        </div>

        <div class="tab-content" id="parlays-tab">
            <div class="bets-list" id="parlays-list">
                <div class="loading">Loading parlays...</div>
            </div>
        </div>
    </div>

    <?php include("../assets/php/footer.php"); ?>

    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/nav.js"></script>
</body>
</html>
