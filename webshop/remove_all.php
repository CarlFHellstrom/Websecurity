<?php
session_start();

if (!isset($_POST['product_id'])) {
    header("Location: cart.php");
    exit;
}

$product_id = intval($_POST['product_id']);

// Remove item entirely
unset($_SESSION['cart'][$product_id]);

header("Location: cart.php");
exit;
