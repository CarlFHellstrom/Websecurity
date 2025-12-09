<?php
session_start();
require 'db.php';
require 'csrf.php';

$cart = $_SESSION['cart'] ?? [];

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
                'id' => $id,
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page">

    <header class="header">
        <div class="header-title">Your Cart</div>
        <div class="nav">
            <a href="index.php">Back to shop</a>
            <a href="cart.php">Cart</a>
            <?php if (isset($_SESSION['username'])): ?>
                <span class="header-user">
                    Logged in as <strong><?php echo $_SESSION['username']; ?></strong>
                </span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign up</a>
            <?php endif; ?>
        </div>
    </header>

    <?php if (empty($items)): ?>
        <div class="card">
            <p>Your cart is empty.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <h2>Items in your cart</h2>
            <?php foreach ($items as $item): ?>
                <div class="list-row">
                    <span>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        (<?php echo $item['quantity']; ?> × <?php echo $item['price']; ?> kr)
                    </span>
                    <span><?php echo $item['subtotal']; ?> kr</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <form action="remove_one.php" method="post" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit">Remove one</button>
                    </form>
                    <form action="remove_all.php" method="post" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit">Remove all</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <hr style="margin: 12px 0;">
            <div class="list-row">
                <span><strong>Total</strong></span>
                <span><strong><?php echo $total; ?> kr</strong></span>
            </div>

            <div class="page-footer-links" style="margin-top: 16px;">
                <a href="checkout.php" class="button-link">Proceed to checkout</a>
            </div>
        </div>
    <?php endif; ?>

</div>
</body>
</html>
