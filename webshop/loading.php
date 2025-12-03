<?php
session_start();
require 'db.php';
require 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed.";
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("CSRF validation failed.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
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
        'product_id' => $id,
        'quantity'   => $quantity,
        'unit_price' => $row['price']
    ];
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
$stmt->bind_param("id", $user_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

$stmt = $mysqli->prepare("
    INSERT INTO order_items (order_id, product_id, quantity, unit_price)
    VALUES (?, ?, ?, ?)
");

foreach ($items as $item) {
    $stmt->bind_param(
        "iiid",
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['unit_price']
    );
    $stmt->execute();
}

$stmt->close();

unset($_SESSION['cart']);

$_SESSION['last_order_id'] = $order_id;

sleep(2);

header("Location: receipt.php");
exit;
