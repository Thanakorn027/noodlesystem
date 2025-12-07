<?php 
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เมนูอาหาร</title>
<style>
    body { font-family: Arial; padding: 20px; }
    .item { 
        border:1px solid #ccc; padding:10px; margin:10px; 
        border-radius:10px; width:240px; display:inline-block;
    }
</style>
</head>
<body>

<h1>เมนูอาหาร</h1>

<?php
$sql = "SELECT * FROM menu WHERE status='active'";
$res = $conn->query($sql);

while($m = $res->fetch_assoc()):
?>
<div class="item">
    <h3><?= htmlspecialchars($m['menu_name']) ?></h3>
    <p><?= htmlspecialchars($m['description']) ?></p>
    <p><b>ราคา: <?= htmlspecialchars($m['price']) ?> บาท</b></p>

    <form action="add_to_cart.php" method="POST">
        <input type="hidden" name="menu_id" value="<?= (int)$m['menu_id'] ?>">
        <input type="number" name="qty" value="1" min="1">
        <button type="submit">เพิ่มลงตะกร้า</button>
    </form>
</div>
<?php endwhile; ?>

<br><br>
<a href="review.php">ตรวจสอบรายการสั่ง</a>

</body>
</html>
