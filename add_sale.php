<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = $_POST['sale_description'];
    $discount = $_POST['discount_percent'];
    
    if ($discount >= 1 && $discount <= 100) {
        $stmt = $pdo->prepare("INSERT INTO sales (description, discount_percent) VALUES (?, ?)");
        $stmt->execute([$desc, $discount]);
        header("Location: view_sales.php");
    } else {
        echo "<script>alert('Discount must be between 1-100%!'); window.history.back();</script>";
    }
}
?>
