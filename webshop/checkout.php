<?php
session_start();
require 'db.php';
require 'csrf.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

if (!isset($_SESSION['username'], $_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'];
$ids = array_map('intval', array_keys($cart));
$product_ids = implode(',', $ids);

$total = 0;
$items = [];

if (!empty($ids)) {
    $query = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = $mysqli->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $id = (int)$row['id'];
            $quantity = $cart[$id] ?? 0;
            if ($quantity <= 0) {
                continue;
            }
            $subtotal = $quantity * $row['price'];
            $total += $subtotal;
            $items[] = [
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}

if (empty($items)) {
    header("Location: cart.php");
    exit;
}

$_SESSION['pending_total'] = $total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

    <header class="header">
        <div class="header-title">Checkout</div>
        <div class="nav">
            <a href="index.php">Shop</a>
            <a href="cart.php">Cart</a>
            <span class="header-user">
                Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </span>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="card">
        <h2>Your order</h2>

        <?php foreach ($items as $item): ?>
            <div class="list-row">
                <span>
                    <?php echo (int)$item['quantity']; ?> ×
                    <?php echo htmlspecialchars($item['name']); ?>
                    (<?php echo $item['price']; ?> kr)
                </span>
                <span><?php echo $item['subtotal']; ?> kr</span>
            </div>
        <?php endforeach; ?>

        <hr style="margin: 12px 0;">
        <div class="list-row">
            <span><strong>Total</strong></span>
            <span><strong><?php echo $total; ?> kr</strong></span>
        </div>

        <div class="page-footer-links" style="margin-top: 16px;">
            <a href="cart.php" class="button-link" style="margin-right: 8px;">Back to cart</a>
            <form action="payment.php" method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <button type="submit">Proceed to payment</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
