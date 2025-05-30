<?php
session_start();
$conn = new mysqli("localhost", "root", "R@mu12072004", "water");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Update order status to 'delivered'
    $sql = "UPDATE orders SET order_status = 'delivered' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    header("Location: track_orders.php"); // Redirect back to orders page
    exit();
}
?>
