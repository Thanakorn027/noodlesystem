<?php
session_start();
include 'db.php';

$cart = $_SESSION['cart'] ?? [];

$total = 0;
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ตรวจสอบรายการอาหาร</title>
<style>
    body { font-family: Arial; padding: 20px; }
    table { width: 60%; border-collapse: collapse; }
    td, th { border:1px solid #ccc; padding: 10px; }
    .btn { padding: 10px 20px; background: green; color:white; text-decoration:none; }
</style>
</head>
<body>

<h1>ตรวจสอบรายการสั่ง</h1>

<?php if (empty($cart)): ?>
    <p>ยังไม่มีรายการอาหาร</p>
    <a href="index.php">กลับไปเลือกเมนู</a>
<?php else: ?>

<table>
    <tr>
        <th>เมนู</th>
        <th>ราคา</th>
        <th>จำนวน</th>
        <th>รวม</th>
    </tr>

<?php
foreach ($cart as $menu_id => $qty):
    $menu_id = (int)$menu_id;
    $qty = (int)$qty;

    $res = $conn->query("SELECT * FROM menu WHERE menu_id=$menu_id");
    if (!$res) {
        echo '<tr><td colspan="4">เกิดข้อผิดพลาดของฐานข้อมูล</td></tr>';
        continue;
    }
    $m = $res->fetch_assoc();
    if (!$m) continue;

    $subtotal = $m['price'] * $qty;
    $total += $subtotal;
?>
<tr>
    <td><?= $m['menu_name'] ?></td>
    <td><?= $m['price'] ?></td>
    <td><?= $qty ?></td>
    <td><?= $subtotal ?></td>
</tr>

<?php endforeach; ?>
</table>

<h2>ยอดรวมทั้งหมด: <?= $total ?> บาท</h2>

<br>
<a class="btn" href="confirm.php">ยืนยันการสั่งอาหาร</a>

<?php endif; ?>

</body>
</html>
