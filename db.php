<?php
$host = 'db';
$user = 'root';
$pass = 'root';
$dbname = '4685002_project';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

mysqli_set_charset($conn, "utf8");
?>
