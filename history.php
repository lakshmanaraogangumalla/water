<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_id'])) {
    // Optionally redirect to login or show an error
    
}

$sql = "SELECT orders.id, orders.customer_name, orders.phone, orders.address, orders.delivery_time,
        orders.order_date, order_items.item_name, order_items.quantity, order_items.item_price,
        (order_items.quantity * order_items.item_price) AS total_price, orders.status
        FROM orders
        LEFT JOIN order_items ON orders.id = order_items.order_id
        ORDER BY orders.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Order History</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: slideIn 1s ease;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        #search {
            padding: 10px;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            animation: fadeIn 1s ease;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .badge.accepted {
            background: #28a745;
        }
        .badge.pending {
            background: #ffc107;
        }
        @keyframes slideIn {
            from {transform: translateY(30px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .center-btn {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>All Order History</h2>
    <input type="text" id="search" placeholder="Search by name or address...">
    <table id="ordersTable">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Delivery Time</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Item Price</th>
            <th>Total</th>
            <th>Order Date</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($orders) > 0): foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['phone']) ?></td>
                <td><?= htmlspecialchars($order['address']) ?></td>
                <td><?= htmlspecialchars($order['delivery_time']) ?></td>
                <td><?= htmlspecialchars($order['item_name']) ?></td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td>₹<?= number_format($order['item_price'], 2) ?></td>
                <td>₹<?= number_format($order['total_price'], 2) ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td>
                    <span class="badge <?= $order['status'] == 'accepted' ? 'accepted' : 'pending' ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="11">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="center-btn">
    <a href="admin_home.php" class="btn">← Back</a>
</div>
<script>
    const searchInput = document.getElementById("search");
    searchInput.addEventListener("input", function () {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll("#ordersTable tbody tr");
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>
</body>
</html>
