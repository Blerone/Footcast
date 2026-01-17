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
    <title>User Dashboard - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
</head>
<body>
    <?php include("../assets/php/header.php"); ?>
    
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>My Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</p>
        </div>

        <div class="balance-card">
            <div class="balance-info">
                <h3>Account Balance</h3>
                <div class="balance-amount" id="balance-amount">$0.00</div>
            </div>
            <div class="balance-actions">
                <button class="btn-primary" onclick="window.location.href='../matches.php'">Place Bet</button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <h4>Total Bets</h4>
                    <p class="stat-value" id="total-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <h4>Won</h4>
                    <p class="stat-value" id="won-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">❌</div>
                <div class="stat-info">
                    <h4>Lost</h4>
                    <p class="stat-value" id="lost-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <h4>Pending</h4>
                    <p class="stat-value" id="pending-bets">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-info">
                    <h4>Win Rate</h4>
                    <p class="stat-value" id="win-rate">0%</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h4>Total Winnings</h4>
                    <p class="stat-value" id="total-winnings">$0.00</p>
                </div>
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
</body>
</html>
