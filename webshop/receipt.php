<?php
session_start();
require 'db.php';

// Must come from checkout process
if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_SESSION['last_order_id'];

// Fetch order
$stmt = $mysqli->prepare("SELECT total_amount, created_at FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    echo "Order not found.";
    exit;
}

$stmt->bind_result($total_amount, $created_at);
$stmt->fetch();
$stmt->close();

// Fetch items
$items_stmt = $mysqli->prepare("
    SELECT p.name, oi.quantity, oi.unit_price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

// Clear last_order_id so refresh doesn’t duplicate
unset($_SESSION['last_order_id']);
?>
<!DOCTYPE html>
<html>
    <body>

        <h1>Receipt</h1>
        <a href="index.php">⬅ Back to shop</a>

        <h2>Order #<?php echo htmlspecialchars($order_id); ?></h2>
        <p><strong>Date:</strong> <?php echo $created_at; ?></p>

        <h3>Items</h3>

        <?php while ($item = $items_result->fetch_assoc()): ?>
            <p>
                <?php echo $item['quantity']; ?> × 
                <?php echo htmlspecialchars($item['name']); ?> —
                <?php echo $item['unit_price'] * $item['quantity']; ?> kr
            </p>
        <?php endwhile; ?>

        <h2>Total: <?php echo $total_amount; ?> kr</h2>

        <p>Thank you for your purchase!</p>

    </body>
</html>
