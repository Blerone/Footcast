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
    <title>Profile - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php
        include("./assets/php/nav.php")
    ?>

    <div class="dashboard-container profile-container">
        <div class="profile-header">
            <h1>Profile</h1>
            <p>Account snapshot and quick settings.</p>
        </div>

        <div class="profile-tabs" role="tablist">
            <button class="profile-tab-btn active" data-tab="overview" role="tab" aria-selected="true">Overview</button>
            <button class="profile-tab-btn" data-tab="recent" role="tab" aria-selected="false">Recent Bets</button>
            <button class="profile-tab-btn" data-tab="transactions" role="tab" aria-selected="false">Transactions</button>
            <button class="profile-tab-btn" data-tab="settings" role="tab" aria-selected="false">Settings</button>
        </div>

        <section class="profile-tab-panel active" id="overview-panel" role="tabpanel">
            <div class="profile-section-title">Overview</div>
            <div class="profile-stats-grid">
                <div class="profile-stat">
                    <span class="profile-stat-label">Total Bets</span>
                    <span class="profile-stat-value" id="profile-total-bets">0</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-label">Won Bets</span>
                    <span class="profile-stat-value" id="profile-won-bets">0</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-label">Lost Bets</span>
                    <span class="profile-stat-value" id="profile-lost-bets">0</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-label">Win Rate</span>
                    <span class="profile-stat-value" id="profile-win-rate">0%</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-label">Total Wagered</span>
                    <span class="profile-stat-value" id="profile-total-wagered">$0.00</span>
                </div>
                <div class="profile-stat">
                    <span class="profile-stat-label">Total Won</span>
                    <span class="profile-stat-value" id="profile-total-won">$0.00</span>
                </div>
            </div>
        </section>

        <section class="profile-tab-panel" id="recent-panel" role="tabpanel">
            <div class="profile-section-title">Recent Bets</div>
            <div class="profile-table">
                <div class="profile-table-header">
                    <span>Bet</span>
                    <span>Placed</span>
                    <span>Status</span>
                </div>
                <div class="profile-table-body" id="recent-bets-list">
                    <div class="profile-empty">Loading recent bets...</div>
                </div>
            </div>
        </section>

        <section class="profile-tab-panel" id="transactions-panel" role="tabpanel">
            <div class="profile-section-title">Transactions</div>
            <div class="profile-table">
                <div class="profile-table-header">
                    <span>Date</span>
                    <span>Amount</span>
                    <span>Status</span>
                </div>
                <div class="profile-table-body">
                    <div class="profile-empty">No deposits yet.</div>
                </div>
            </div>
        </section>

        <section class="profile-tab-panel" id="settings-panel" role="tabpanel">
            <div class="profile-section-title">Settings</div>
            <form class="profile-form">
                <label class="profile-input">
                    <span>Current Password</span>
                    <input type="password" name="current_password" placeholder="Current password">
                </label>
                <label class="profile-input">
                    <span>New Password</span>
                    <input type="password" name="new_password" placeholder="New password">
                </label>
                <label class="profile-input">
                    <span>Confirm New Password</span>
                    <input type="password" name="confirm_password" placeholder="Confirm new password">
                </label>
                <button type="submit" class="profile-submit">Update Password</button>
            </form>
        </section>
    </div>

    <?php include("../assets/php/footer.php"); ?>

    <script src="assets/js/profile.js"></script>
    <script src="assets/js/nav.js"></script>
</body>
</html>
