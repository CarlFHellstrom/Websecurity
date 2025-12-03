<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html>
<body>

<h1>Your Cart</h1>

<a href="index.php">⬅ Back to shop</a>

<?php
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit;
}

$cart = $_SESSION['cart'];
$product_ids = implode(",", array_keys($cart));

$query = "SELECT * FROM products WHERE id IN ($product_ids)";
$result = $mysqli->query($query);

$total = 0;

while ($row = $result->fetch_assoc()):
    $id = $row['id'];
    $quantity = $cart[$id];
    $subtotal = $quantity * $row['price'];
    $total += $subtotal;
?>
    <div style="margin-bottom: 10px;">
        <strong><?php echo $row['name']; ?></strong>
        (<?php echo $quantity; ?> x <?php echo $row['price']; ?> kr)
        = <?php echo $subtotal; ?> kr
    </div>

    <form action="remove_one.php" method="post" style="display:inline;">
        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
        <button type="submit">- Remove one</button>
    </form>

    <form action="remove_all.php" method="post" style="display:inline;">
        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
        <button type="submit">Remove all</button>
    </form>

<?php endwhile; ?>

<a href="checkout.php">Proceed to checkout</a>

</body>
</html>
