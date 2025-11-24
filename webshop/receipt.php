<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("No order found.");
}

require 'db.php';

// Calculate totals
$cart = $_SESSION['cart'];
$product_ids = implode(",", array_keys($cart));

$query = "SELECT * FROM products WHERE id IN ($product_ids)";
$result = $mysqli->query($query);

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $quantity = $cart[$id];
    $subtotal = $quantity * $row['price'];
    $total += $subtotal;

    $items[] = [
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
}

// Clear cart after purchase
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<body>
<h1>Receipt</h1>

<?php foreach ($items as $item): ?>
    <p>
        <?php echo $item['quantity']; ?> × 
        <?php echo htmlspecialchars($item['name']); ?> — 
        <?php echo $item['subtotal']; ?> kr
    </p>
<?php endforeach; ?>

<h2>Total paid: <?php echo $total; ?> kr</h2>

<p>Thank you for your purchase!</p>

<a href="index.php">Back to shop</a>

</body>
</html>
