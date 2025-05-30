<?php
session_start();
include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM orders ORDER BY order_date DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All User Bills</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #fce4ec);
            padding: 30px;
            margin: 0;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .order-box {
            background: white;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .order-box:hover {
            transform: scale(1.01);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #90caf9;
            color: #fff;
            text-align: left;
        }

        td {
            background: #f9f9f9;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            color: #2e7d32;
        }

        .btn {
            padding: 10px 18px;
            background: #2196f3;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
        }

        .btn:hover {
            background: #1976d2;
        }

        .status-btn {
            margin-top: 10px;
            background: #e57373;
        }

        .paid {
            background: #66bb6a !important;
        }

        .info {
            margin: 5px 0;
        }

        .back {
            text-align: center;
            margin-top: 30px;
        }

        .back .btn {
            background: #6a1b9a;
        }

        .back .btn:hover {
            background: #4a148c;
        }
    </style>
</head>
<body>

<h2>All User Bills (With Date, Time & Payment)</h2>

<?php if (count($orders) === 0): ?>
    <p style="text-align:center;">No orders found.</p>
<?php endif; ?>

<?php foreach ($orders as $order): ?>
    <div class="order-box" id="order-<?= $order['id'] ?>">
        <h3>Order ID: <?= $order['id'] ?></h3>
        <div class="info"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
        <div class="info"><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></div>
        <div class="info"><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></div>
        <div class="info"><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></div>
        <div class="info"><strong>Date & Time:</strong> <?= date("d-m-Y h:i A", strtotime($order['order_date'])) ?></div>

        <?php
            $stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt_items->execute([$order['id']]);
            $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
            $total = 0;
        ?>

        <table>
            <tr>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($items as $item): 
                $subtotal = $item['quantity'] * $item['item_price'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₹<?= number_format($item['item_price'], 2) ?></td>
                <td>₹<?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p class="total">Total: ₹<?= number_format($total, 2) ?></p>

        <!-- Payment Button -->
        <button class="btn status-btn" onclick="togglePayment(this)">Not Complete</button>
    </div>
<?php endforeach; ?>

<div class="back">
    <a href="admin_home.php" class="btn">⬅ Back to Admin Home</a>
</div>

<script>
    function togglePayment(button) {
        if (button.classList.contains('paid')) {
            button.classList.remove('paid');
            button.innerText = 'Not Complete';
        } else {
            button.classList.add('paid');
            button.innerText = 'Complete';
        }
    }
</script>

</body>
</html>
