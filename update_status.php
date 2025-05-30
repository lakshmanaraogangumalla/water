<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'Accepted' WHERE id = ?");
    if ($stmt->execute([$orderId])) {
        header("Location: admin_home.php");
        exit();
    } else {
        echo "Error updating status.";
    }
} else {
    echo "Invalid request.";
}
?>
