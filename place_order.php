<?php
session_start();
include 'db.php'; // DB connection file

date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: home.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = trim($_POST['customer'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $delivery_date = $_POST['delivery_date'] ?? '';
    $delivery_time = $_POST['delivery_time'] ?? '';
    $terms_agreed = isset($_POST['terms']);

    if (empty($customer) || empty($address) || empty($phone) || empty($delivery_date) || empty($delivery_time)) {
        $message = "<p style='color:red;'>⚠️ All fields are required.</p>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "<p style='color:red;'>📞 Invalid phone number. Must be 10 digits.</p>";
    } elseif (!$terms_agreed) {
        $message = "<p style='color:red;'>⚠️ You must agree to the terms and conditions.</p>";
    } else {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $order_date = date('Y-m-d H:i:s');

        try {
            // Insert into orders table
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, address, phone, status, order_date, delivery_date, delivery_time, customer_name)
                                   VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?)");
            $stmt->execute([$user_id, $total, $address, $phone, $order_date, $delivery_date, $delivery_time, $customer]);

            $order_id = $pdo->lastInsertId();

            // Insert order items
            foreach ($_SESSION['cart'] as $product) {
                $subtotal = $product['price'] * $product['quantity'];
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_name, quantity, item_price, total_price)
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$order_id, $product['item'], $product['quantity'], $product['price'], $subtotal]);
            }

            // Send message to admin
            $admin_msg = "New Order from $username (Order ID: $order_id). Total: ₹$total";
            $stmt = $pdo->prepare("INSERT INTO admin_messages (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $admin_msg]);

            // Clear cart
            unset($_SESSION['cart']);

            header("Location: order_success.php?order_id=$order_id");
            exit();
        } catch (PDOException $e) {
            $message = "<p style='color:red;'>❌ Order failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Your Order - Water Bottles</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e6f0ff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            color: #004aad;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            background: #004aad;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s ease;
        }

        button:hover {
            background: #003080;
        }

        .message {
            text-align: center;
            margin-top: 15px;
        }

        .terms {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }

        .terms input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Place Your Water Bottle Order</h2>

    <div class="message"><?php echo $message; ?></div>

    <form method="POST" action="">
        <div class="form-group">
            <label for="customer">Customer Name</label>
            <input type="text" id="customer" name="customer" required>
        </div>

        <div class="form-group">
            <label for="address">Delivery Address</label>
            <textarea id="address" name="address" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required maxlength="10">
        </div>

        <div class="form-group">
            <label for="delivery_date">Delivery Date</label>
            <input type="date" id="delivery_date" name="delivery_date" required>
        </div>

        <div class="form-group">
            <label for="delivery_time">Delivery Time</label>
            <input type="time" id="delivery_time" name="delivery_time" required>
        </div>

        <div class="terms">
            <label>
                <input type="checkbox" name="terms" required> I agree to the <a href="Original.php" target="_blank">Terms & Conditions</a>, including the no-refund policy for damaged cans.
            </label>
        </div>

        <button type="submit">Submit Order</button>
    </form>
</div>

</body>
</html>
