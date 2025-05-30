<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders from the database
$sql = "SELECT order_date, amount FROM orders WHERE user_id=:user_id ORDER BY order_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['orders' => $orders]);
?>
