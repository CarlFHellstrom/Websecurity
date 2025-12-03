<?php
session_start();
require 'db.php';
require 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("CSRF validation failed.");
}

if (!isset($_SESSION['username'], $_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['pending_total']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$total = $_SESSION['pending_total'];

$SHOP_WALLET_ADDRESS = "FPnelHrkS83dOhq4EONnJsGHWAKArahE8bbFBzDrbcBsJGE2Bbo2jfWnT3N24V6muCPrZQMziTUIXHh7BVjvJg==";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blockchain Payment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

    <header class="header">
        <div class="header-title">Blockchain Payment</div>
        <div class="nav">
            <a href="index.php">Shop</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
            <span class="header-user">
                Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </span>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="card">
        <h2>Complete your payment</h2>

        <p style="margin-bottom: 8px;">
            Total to pay: <strong><?php echo $total; ?> kr</strong>
        </p>

        <p style="margin-bottom: 8px;">
            Send <strong><?php echo $total; ?> kr</strong> to the webshop's wallet address:
        </p>

        <p style="margin-bottom: 12px;">
            <code><?php echo htmlspecialchars($SHOP_WALLET_ADDRESS); ?></code>
        </p>

        <p style="margin-bottom: 16px;">
            Use your SimpleCoin wallet program to send the payment and mine a block.
            When you are done, paste the <strong>transaction ID</strong> from the wallet below.
        </p>

        <form action="loading.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <label for="tx_id">Transaction ID from wallet</label>
            <input type="text" id="tx_id" name="tx_id" required>

            <button type="submit">Confirm payment</button>
        </form>

        <div class="page-footer-links">
            <a href="checkout.php">Back to checkout</a>
        </div>
    </div>

</div>
</body>
</html>
