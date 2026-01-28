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
    <title>Deposit - FootCast</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php
        include("./assets/php/nav.php")
    ?>

    <div class="dashboard-container deposit-container">
        <div class="profile-header">
            <h1>Deposit</h1>
            <p>Top up your balance in a few taps.</p>
        </div>

        <div class="deposit-section">
            <div class="profile-section-title">Select Amount</div>
            <div class="deposit-amounts">
                <button type="button">$25</button>
                <button type="button">$50</button>
                <button type="button">$100</button>
                <button type="button">$250</button>
                <button type="button">$500</button>
            </div>
        </div>

        <div class="deposit-section">
            <label class="profile-input">
                <span>Custom Amount</span>
                <input type="number" min="10" placeholder="Minimum $10">
            </label>
        </div>

        <div class="deposit-section">
            <div class="profile-section-title">Payment Method</div>
            <div class="deposit-method">
                <span>PayPal</span>
                <span class="deposit-tag">Demo</span>
            </div>
        </div>

        <button class="deposit-submit" type="button">Complete Deposit (Demo)</button>
    </div>

    <?php include("../assets/php/footer.php"); ?>

    <script src="assets/js/nav.js"></script>
</body>
</html>
