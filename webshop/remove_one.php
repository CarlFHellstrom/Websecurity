<?php
session_start();

if (!isset($_POST['product_id'])) {
    header("Location: cart.php");
    exit;
}

$product_id = intval($_POST['product_id']);

if (isset($_SESSION['cart'][$product_id])) {

    $_SESSION['cart'][$product_id]--;

    if ($_SESSION['cart'][$product_id] <= 0) {
        unset($_SESSION['cart'][$product_id]);
    }
}

header("Location: cart.php");
exit;
