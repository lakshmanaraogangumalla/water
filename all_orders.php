<?php
session_start();
$conn = new mysqli("localhost", "root", "R@mu12072004", "water");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If payment is marked complete
if (isset($_GET['mark_paid'])) {
    $order_id = intval($_GET['mark_paid']);
    $conn->query("UPDATE orders SET payment_status='paid' WHERE id=$order_id");
    header("Location: all_orders.php");
    exit();
}

// Fetch all orders with aggregated items
$sql = "
SELECT 
    o.id AS order_id,
    o.customer_name,
    o.phone,
    o.address,
    o.order_time,
    o.total_price,
    o.order_status,
    o.payment_status,
    GROUP_CONCAT(CONCAT(oi.item_name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS items
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id
ORDER BY o.order_time DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders - SR Water</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
       body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #f3f9fa, #ffffff);
    margin: 0;
    padding: 0;
    animation: fadeBody 1s ease-in-out;
}

@keyframes fadeBody {
    from { opacity: 0; }
    to { opacity: 1; }
}

.container {
    max-width: 95%;
    margin: 40px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    animation: slideIn 1s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    color: #006064;
    margin-bottom: 25px;
    font-size: 32px;
    animation: zoomIn 0.6s ease;
}

@keyframes zoomIn {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

table {
    width: 100%;
    border-collapse: collapse;
    animation: fadeIn 1s ease-in;
    background-color: #fafafa;
    border-radius: 12px;
    overflow: hidden;
}

th, td {
    padding: 14px;
    border: 1px solid #e0e0e0;
    text-align: center;
    transition: background-color 0.3s ease;
}

th {
    background-color: #004d40;
    color: #ffffff;
    font-size: 16px;
    letter-spacing: 0.5px;
}

tr:nth-child(even) {
    background-color: #f1f8e9;
}

tr:hover {
    background-color: #e0f7fa;
    transform: scale(1.01);
    transition: all 0.3s ease-in-out;
    box-shadow: 0 0 10px rgba(0, 150, 136, 0.2);
}

.status {
    font-weight: bold;
    text-transform: uppercase;
    color: #00796b;
}

.button, .payment-btn {
    background-color: #00796b;
    color: white;
    padding: 8px 16px;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: bold;
}

.button:hover, .payment-btn:hover {
    background-color: #004d40;
    transform: scale(1.05);
}

.complete {
    background-color: #4caf50 !important;
}

.paid {
    background-color: #4caf50;
    cursor: default;
    opacity: 0.9;
}

/* Animated Truck Icon */
.truck-icon {
    position: relative;
    animation: drive 4s linear infinite;
}

.truck-icon .fa-truck {
    font-size: 60px;
    color: #00695c;
    animation: bounce 1.2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.truck-icon .wheel {
    position: absolute;
    bottom: 8px;
    width: 20px;
    height: 20px;
    background-color: #424242;
    border-radius: 50%;
    animation: rotateWheel 0.5s linear infinite;
}

.truck-icon .wheel.left {
    left: 12px;
}

.truck-icon .wheel.right {
    right: 12px;
}

@keyframes drive {
    0% { transform: translateX(-100px); }
    100% { transform: translateX(500px); }
}

@keyframes rotateWheel {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}


    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-truck truck-icon">
        <span class="wheel left"></span><span class="wheel right"></span>
    </i> All Orders</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Ordered On</th>
                <th>Items</th>
                <th>Total Amount</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= htmlspecialchars($row['order_id']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['order_time'])) ?></td>
                    <td><?= htmlspecialchars($row['items']) ?></td>
                    <td>₹<?= number_format($row['total_price'], 2) ?></td>
                    <td>
                        <?php if ($row['payment_status'] === 'paid'): ?>
                            <button class="payment-btn paid">Paid</button>
                        <?php else: ?>
                            <a href="?mark_paid=<?= $row['order_id'] ?>" class="payment-btn">Mark as Paid</a>
                        <?php endif; ?>
                    </td>
                    <td>
                    <a href="admin_delivery_status.php" class="button">Track</a>

                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center; font-size: 18px;">No orders found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
