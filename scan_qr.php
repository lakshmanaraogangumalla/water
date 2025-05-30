<?php
include 'db.php';

// Save new order
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $can_number = $_POST['can_number'];
    $can_count = $_POST['can_count'];
    $user_name = $_POST['user_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("INSERT INTO orders (can_number, quantity, user_name, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$can_number, $can_count, $user_name, $phone, $address]);
}

// Fetch today’s orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE DATE(date_time) = CURDATE() ORDER BY date_time DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>QR Scan & Can Delivery</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        #reader { width: 300px; margin: auto; }
        input, button, textarea {
            padding: 10px;
            margin: 8px 0;
            width: 100%;
            font-size: 16px;
        }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>

<div class="container">
    <h2>📦 Water Can Delivery Entry</h2>

    <div id="reader"></div>

    <form method="POST">
        <label>Can Number</label>
        <input type="text" id="can_number" name="can_number" required readonly>

        <label>Number of Cans</label>
        <input type="number" name="can_count" required min="1">

        <label>User Name</label>
        <input type="text" name="user_name" required>

        <label>Phone Number</label>
        <input type="text" name="phone" required>

        <label>Address</label>
        <textarea name="address" required></textarea>

        <button type="submit">✅ Submit Order</button>
    </form>
</div>

<!-- Order List -->
<div class="container">
    <h3>📋 Today's Orders</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Can No</th>
            <th>Cans</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
        <?php foreach ($orders as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['can_number']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= date("d-m-Y h:i A", strtotime($row['date_time'])) ?></td>
            <td>
                <a href="update_order.php?id=<?= $row['id'] ?>">✏️</a>
                <a href="delete_order.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this order?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
    function onScanSuccess(decodedText) {
        document.getElementById("can_number").value = decodedText;
        html5QrcodeScanner.clear();
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
</script>

</body>
</html>
