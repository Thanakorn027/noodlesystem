<?php
session_start();
include 'db.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "ไม่มีรายการอาหาร";
    exit;
}

$total = 0;

// คำนวณยอดรวมก่อนบันทึก (cast ค่าเพื่อความปลอดภัย)
foreach ($cart as $menu_id => $qty) {
    $menu_id = (int)$menu_id;
    $qty = (int)$qty;

    $res = $conn->query("SELECT price FROM menu WHERE menu_id=$menu_id");
    if (!$res) {
        die('Database error: ' . $conn->error);
    }
    $m = $res->fetch_assoc();
    if (!$m) continue;

    $total += $m['price'] * $qty;
}

// 1. สร้าง order (ตรวจสอบก่อนว่า $total ถูกคำนวณ)
$total = (float)$total;

$insertOrderSql = "INSERT INTO orders (customer_id, total_price, status) VALUES (1, $total, 'done')";
if (!$conn->query($insertOrderSql)) {
    die('Failed to create order: ' . $conn->error);
}

$order_id = $conn->insert_id;

// 2. เพิ่มรายการ order_item
foreach ($cart as $menu_id => $qty) {
    $menu_id = (int)$menu_id;
    $qty = (int)$qty;

    $res = $conn->query("SELECT price FROM menu WHERE menu_id=$menu_id");
    if (!$res) {
        die('Database error: ' . $conn->error);
    }
    $m = $res->fetch_assoc();
    if (!$m) continue;

    $subtotal = $m['price'] * $qty;

    $sql = "INSERT INTO order_item (order_id, menu_id, quantity, subtotal) VALUES ($order_id, $menu_id, $qty, $subtotal)";
    if (!$conn->query($sql)) {
        die('Failed to add order item: ' . $conn->error);
    }
}

// 3. ล้างตะกร้า
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ยอดชำระ</title>
<style>
    body { font-family: Arial; padding: 20px; }
    .box { padding:20px; border:2px solid #4CAF50; width:400px; }
</style>
</head>
<body>

<h1>สั่งซื้อสำเร็จ!</h1>

<div class="box">
    <h2>ยอดที่ต้องชำระ: <?= $total ?> บาท</h2>
    <p>หมายเลขออเดอร์: <?= $order_id ?></p>
</div>

<br>
<a href="index.php">กลับไปหน้าเมนู</a>

</body>
</html>
