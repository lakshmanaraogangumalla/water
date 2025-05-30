<?php
include('db.php');
session_start();

// Check if the order_id is provided in the POST request
if (isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    // Update the order status to 'accepted'
    $sql = "UPDATE orders SET status = 'accepted' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId]);

    // Redirect back to the admin home page
    header("Location: admin_home.php");
    exit();
} else {
    echo "Invalid order ID.";
}
?>
