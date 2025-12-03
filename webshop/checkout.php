<?php
session_start();
require 'db.php';
require 'csrf.php';

// If cart is empty, redirect back
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Require login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

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

// spara totalen i sessionen tills payment/loading är klart
$_SESSION['pending_total'] = $total;
?>
<!DOCTYPE html>
<html>
<body>

<h1>Checkout</h1>
<a href="cart.php">⬅ Back to cart</a>

<h2>Your Order</h2>

<?php foreach ($items as $item): ?>
<p>
    <?php echo $item['quantity']; ?> × 
    <?php echo htmlspecialchars($item['name']); ?> — 
    <?php echo $item['subtotal']; ?> kr
</p>
<?php endforeach; ?>

<h2>Total: <?php echo $total; ?> kr</h2>

<form action="payment.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <button type="submit">Proceed to payment</button>
</form>

</body>
</html>
