<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Order ID is missing.");
}

// Update order status to "paid"
$stmt = $pdo->prepare("UPDATE orders SET status = 'Paid' WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);

// Fetch updated order for display
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eafaf1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .confirmation-box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .confirmation-box h2 {
            color: #28a745;
        }
        .confirmation-box p {
            margin: 10px 0;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <h2>🎉 Payment Successful!</h2>
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Amount Paid:</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>

        <a href="your_orders.php" class="btn">View My Orders</a>
    </div>
</body>
</html>
