<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <style>
       body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
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
            text-align: center;
            min-width: 80px;
        }

        .badge.pending {
            background: #ffc107;
        }

        .badge.accepted {
            background: #28a745;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .btn {
            margin-top: 10px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff3366;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background-color: #ff3366;
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
        <h2>Your Orders</h2>
        <input type="text" id="search" placeholder="Search by address or date...">

        <table id="ordersTable">
            <thead>
                <tr>
                    <th>Address</th>
                    <th>Delivery Date</th>
                    <th>Delivery Time</th>
                    <th>Order Placed</th>
                    <th>Status</th>
                    <th>Action</th> <!-- New column for the action button -->
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['address']) ?></td>
                            <td><?= htmlspecialchars($order['delivery_date']) ?></td>
                            <td><?= htmlspecialchars($order['delivery_time']) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                            <td>
                                <span class="badge <?= $order['status'] == 'accepted' ? 'accepted' : 'pending' ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td><a href="bill.php?order_id=<?= $order['id'] ?>" class="btn">View Bill</a></td> <!-- Link to bill.php -->
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="center-btn">
        <a href="home.php" class="btn">← Back</a>
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
