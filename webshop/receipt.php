<?php
session_start();
require 'db.php';

if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_SESSION['last_order_id'];

$stmt = $mysqli->prepare("SELECT total_amount, created_at, tx_id FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    echo "Order not found.";
    exit;
}

$stmt->bind_result($total_amount, $created_at, $tx_id);
$stmt->fetch();
$stmt->close();

$items_stmt = $mysqli->prepare("
    SELECT p.name, oi.quantity, oi.unit_price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

unset($_SESSION['last_order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page">

    <header class="header">
        <div class="header-title">Receipt</div>
        <div class="nav">
            <a href="index.php">Shop</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="card">

        <h2>Order #<?php echo htmlspecialchars($order_id); ?></h2>
        <p><strong>Date:</strong> <?php echo $created_at; ?></p>

        <?php if (!empty($tx_id)): ?>
            <p><strong>Blockchain Transaction ID:</strong><br>
            <code><?php echo htmlspecialchars($tx_id); ?></code></p>
        <?php endif; ?>

        <h3>Items</h3>

        <?php while ($item = $items_result->fetch_assoc()): ?>
            <p>
                <?php echo $item['quantity']; ?> × 
                <?php echo htmlspecialchars($item['name']); ?> —
                <?php echo $item['unit_price'] * $item['quantity']; ?> kr
            </p>
        <?php endwhile; ?>

        <h2>Total: <?php echo $total_amount; ?> kr</h2>

        <p style="margin-top: 20px;">Thank you for your purchase!</p>

        <div class="page-footer-links">
            <a href="index.php">Back to shop</a>
        </div>
    </div>

</div>

</body>
</html>
