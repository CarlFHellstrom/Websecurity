<?php
session_start();
require 'db.php';

if (!isset($_SESSION['last_order_id'])) {
    die("No recent order found.");
}

$order_id = $_SESSION['last_order_id'];

// Fetch the order
$stmt = $mysqli->prepare("
    SELECT total_amount, created_at
    FROM orders
    WHERE id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($total, $created_at);
$stmt->fetch();
$stmt->close();

// Fetch the items
$query = "
    SELECT p.name, oi.quantity, oi.unit_price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html>
    <body>

        <h1>Receipt</h1>

        <p>Order Number: <?php echo $order_id; ?></p>
        <p>Date: <?php echo $created_at; ?></p>
        <hr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <p>
                <?php echo $row['quantity']; ?> × 
                <?php echo htmlspecialchars($row['name']); ?>
                @ <?php echo $row['unit_price']; ?> kr  
            </p>
        <?php endwhile; ?>

        <hr>
        <h2>Total: <?php echo $total; ?> kr</h2>

        <a href="index.php">Back to shop</a>

    </body>
</html>
