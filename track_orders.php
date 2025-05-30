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
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.id, o.order_status, o.order_time, o.total_amount, c.name AS customer_name, c.phone, c.address
        FROM orders o
        LEFT JOIN customers c ON o.user_id = c.id
        WHERE o.user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders - SR Water</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{font-family:'Segoe UI',sans-serif;background:#f0f0f0;margin:0;padding:0}
        .container{max-width:800px;margin:40px auto;padding:20px;background:white;box-shadow:0 0 10px rgba(0,0,0,0.1);border-radius:12px}
        h2{text-align:center;margin-bottom:20px}
        .order-card{padding:15px;margin:10px 0;border-left:6px solid #28a745;background:#e9f5ee;border-radius:8px;animation:fadeIn 0.8s ease-in-out}
        .order-card.pending{border-color:#ffc107;background:#fff8e1}
        .order-card.delivered{border-color:#28a745;background:#e9f5ee}
        .order-card.cancelled{border-color:#dc3545;background:#fcebea}
        .status{font-weight:bold;text-transform:uppercase}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .button{background-color:#28a745;color:white;padding:10px 20px;border:none;cursor:pointer;border-radius:5px}
        .button:hover{background-color:#218838}
        table{width:100%;margin-top:20px;border-collapse:collapse}
        th,td{padding:12px;text-align:center;border:1px solid #ddd}
        th{background-color:#f2f2f2}
        .update-btn{padding:6px 12px;background-color:#0277bd;color:white;border:none;border-radius:4px;cursor:pointer}
        .update-btn:hover{background-color:#01579b}
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-truck"></i> Your Orders</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="order-card <?= strtolower($row['order_status']) ?>">
                <p><strong>Order ID:</strong> #<?= $row['id'] ?></p>
                <p><strong>Status:</strong> <span class="status"><?= ucfirst($row['order_status']) ?></span></p>
                <p><strong>Ordered On:</strong> <?= date("d M Y, h:i A", strtotime($row['order_time'])) ?></p>
                <p><strong>Customer Name:</strong> <?= $row['customer_name'] ?></p>
                <p><strong>Phone Number:</strong> <?= $row['phone'] ?></p>
                <p><strong>Delivery Address:</strong> <?= $row['address'] ?></p>
                <p><strong>Total Amount:</strong> ₹<?= number_format($row['total_amount'], 2) ?></p>
                <?php
                $items_sql = "SELECT item_name, price FROM order_items WHERE order_id = ?";
                $items_stmt = $conn->prepare($items_sql);
                if ($items_stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }
                $items_stmt->bind_param("i", $row['id']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                ?>
                <ul>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <li><?= $item['item_name'] ?> - ₹<?= number_format($item['price'], 2) ?></li>
                    <?php endwhile; ?>
                </ul>
                <?php if ($row['order_status'] != 'delivered'): ?>
                    <form action="update_status.php" method="POST">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="button">Mark as Delivered</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No orders found.</p>
    <?php endif; ?>
</div>
</body>
</html>
<?php
$conn->close();
?>
