<?php
session_start();

// Validate input and cast to integers to avoid unexpected types
$menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

if ($menu_id <= 0 || $qty <= 0) {
    // Invalid input — redirect back to menu
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'][$menu_id])) {
    $_SESSION['cart'][$menu_id] = 0;
}

$_SESSION['cart'][$menu_id] += $qty;

header("Location: review.php");
exit;
