<?php
include 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item_name'];
    $price = $_POST['price'];
    $stmt = $pdo->prepare("INSERT INTO items (name, price) VALUES (?, ?)");
    $stmt->execute([$item, $price]);
    header("Location: admin_home.php");
}
