<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    if ($stmt->execute([$status, $orderId])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
