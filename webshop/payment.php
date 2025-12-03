<?php
session_start();
require 'db.php';
require 'csrf.php';

// Måste komma från checkout via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

// CSRF-koll
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("CSRF validation failed.");
}

// Måste vara inloggad
if (!isset($_SESSION['username'], $_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Måste ha pending_total + cart
if (!isset($_SESSION['pending_total']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$total = $_SESSION['pending_total'];

// Sätt denna till adressen du fick i shop.txt från SimpleCoin-walleten
$SHOP_WALLET_ADDRESS = "FPnelHrkS83dOhq4EONnJsGHWAKArahE8bbFBzDrbcBsJGE2Bbo2jfWnT3N24V6muCPrZQMziTUIXHh7BVjvJg==";
?>
<!DOCTYPE html>
<html>
<body>

<h1>Blockchain Payment</h1>
<a href="checkout.php">⬅ Back to checkout</a>

<p>Total to pay: <strong><?php echo $total; ?> kr</strong></p>

<p>
    Send <strong><?php echo $total; ?> kr</strong> to the webshop's wallet address:<br>
    <code><?php echo htmlspecialchars($SHOP_WALLET_ADDRESS); ?></code>
</p>

<p>
    Use your SimpleCoin wallet program to send the payment and mine a block.
    When you are done, paste the <strong>transaction ID</strong> from the wallet below.
</p>

<form action="loading.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

    <label>Transaction ID from wallet:</label><br>
    <input type="text" name="tx_id" required style="width: 300px;"><br><br>

    <button type="submit">Confirm Payment</button>
</form>

</body>
</html>
