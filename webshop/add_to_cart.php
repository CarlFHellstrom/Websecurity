<?php
session_start();

if (!isset($_POST['product_id'])) {
    header("Location: index.php");
    exit;
}

$product_id = intval($_POST['product_id']);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] = 1;
} else {
    $_SESSION['cart'][$product_id]++;
}

header("Location: index.php");
exit;
