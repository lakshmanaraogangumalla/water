<?php
session_start();
include 'db.php';

$keyId = 'rzp_test_ZYxjy9xoZJ2kI6'; // Razorpay test key

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("Order ID is missing.");
}

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) die("Order not found.");

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($items as $item) {
    $total += $item['item_price'] * $item['quantity'];
}

// Apply coupon
$discount = 0;
if ($order['coupon_code']) {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ?");
    $stmt->execute([$order['coupon_code']]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($coupon) {
        $discount = floatval($coupon['value']);
    }
}

$final_amount = max(0, $total - $discount) * 100; // in paise
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #a8edea, #fed6e3);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-box {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        h2 {
            color: #2c3e50;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            margin: 15px 0;
        }
        button {
            padding: 12px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #219150;
        }
        .upi-box {
            margin-top: 30px;
            background: #f0f0f0;
            padding: 15px;
            border-radius: 10px;
            font-size: 15px;
            line-height: 1.5;
        }
        .upi-id {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
<div class="payment-box">
    <h2>Proceed to Pay</h2>
    <div class="amount">₹<?= number_format($final_amount / 100, 2) ?></div>
    <button id="rzp-button1">Pay Now</button>

    <div class="upi-box">
        <p><strong>OR Pay via UPI:</strong></p>
        <p>Send payment to the following UPI ID:</p>
        <p class="upi-id">9390198031@ybl</p>
        <p>After payment, share your transaction ID or screenshot with support.</p>
    </div>
</div>

<script>
var options = {
    "key": "<?= $keyId ?>",
    "amount": "<?= $final_amount ?>",
    "currency": "INR",
    "name": "Water Delivery",
    "description": "Order #<?= $order_id ?>",
    "handler": function (response){
        alert("Payment Successful! ID: " + response.razorpay_payment_id);
        window.location.href = "payment_success.php?order_id=<?= $order_id ?>&payment_id=" + response.razorpay_payment_id;
    },
    "prefill": {
        "name": "<?= htmlspecialchars($order['customer_name']) ?>",
        "contact": "<?= htmlspecialchars($order['phone']) ?>"
    },
    "theme": {
        "color": "#27ae60"
    }
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}
</script>
</body>
</html>
