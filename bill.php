<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("Order ID missing.");
}

// Apply coupon if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['coupon_code'])) {
    $coupon_input = trim($_POST['coupon_code']);
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ?");
    $stmt->execute([$coupon_input]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if coupon is valid, active and not already used by the user
    if ($coupon && isset($coupon['is_active']) && $coupon['is_active'] && !couponUsed($_SESSION['user_id'], $coupon_input)) {
        // Apply coupon and update sales table
        $stmt = $pdo->prepare("UPDATE orders SET coupon_code = ?, discount = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$coupon_input, $coupon['value'], $order_id, $_SESSION['user_id']]);

        // Record this coupon application into sales table for future reference
        $stmt = $pdo->prepare("INSERT INTO sales (order_id, coupon_code, discount) VALUES (?, ?, ?)");
        $stmt->execute([$order_id, $coupon_input, $coupon['value']]);

        header("Location: bill.php?order_id=$order_id");
        exit;
    } else {
        $coupon_error = "Invalid or already used coupon code.";
    }
}

// Function to check if the coupon was used by the user
function couponUsed($user_id, $coupon_code) {
    global $pdo;
    // Check if the coupon is used in any past or current orders by this user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND coupon_code = ?");
    $stmt->execute([$user_id, $coupon_code]);
    return $stmt->rowCount() > 0;
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Get order items
$total = 0;
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    $total += $item['item_price'] * $item['quantity'];
}

// Get coupon value
$coupon_code = $order['coupon_code'];
$discount = 0;

if ($coupon_code) {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ?");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($coupon && isset($coupon['is_active']) && $coupon['is_active']) {
        $discount = floatval($coupon['value']);
    }
}

$final_total = max(0, $total - $discount);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay Bill</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px; }
        .container, .container1 { max-width: 700px; background: #fff; padding: 25px; margin: auto; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .container1 { display: none; }
        h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        .btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border: none; border-radius: 5px; display: inline-block; cursor: pointer; margin-top: 20px; }
        .btn:hover { background: #218838; }
        .btn-back { background: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; }
        .btn-back:hover { background: #0056b3; }
        .coupon-form { margin-top: 20px; text-align: center; }
        .coupon-form input[type="text"] { padding: 8px; width: 200px; margin-right: 10px; }
        .error { color: red; text-align: center; }
        #qr-code { text-align: center; margin-top: 20px; }
        #upi-instruction { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Order #<?= $order_id ?></h2>
    <h3>User Details</h3>
    <p><strong>Name:</strong> <?= $order['customer_name'] ?></p>
    <p><strong>Phone:</strong> <?= $order['phone'] ?></p>
    <p><strong>Address:</strong> <?= $order['address'] ?></p>
    <p><strong>Status:</strong> <?= $order['status'] ?></p>
    
    <h3>Items Ordered</h3>
    <table>
        <tr>
            <th>Item</th><th>Qty</th><th>Price</th><th>Total</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₹<?= number_format($item['item_price'], 2) ?></td>
                <td>₹<?= number_format($item['item_price'] * $item['quantity'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <h3>Total: ₹<?= number_format($total, 2) ?></h3>
    
    <?php if ($discount > 0): ?>
        <p><strong>Coupon (<?= htmlspecialchars($coupon_code) ?>) Applied:</strong> -₹<?= number_format($discount, 2) ?></p>
        <h3>Final Amount: ₹<?= number_format($final_total, 2) ?></h3>
    <?php else: ?>
        <div class="coupon-form">
            <form method="POST">
                <input type="text" name="coupon_code" placeholder="Enter Coupon Code" required>
                <input class="btn" type="submit" value="Apply Coupon">
            </form>
            <?php if (!empty($coupon_error)): ?>
                <p class="error"><?= $coupon_error ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <button class="btn" onclick="document.querySelector('.container1').style.display='block'">Proceed to Payment</button>
</div>

<div class="container1">
    <a class="btn" href="razorpay_payment.php?order_id=<?= $order_id ?>">Pay via Razorpay</a>
    <button class="btn" onclick="showUPIInstructions()">Pay via UPI</button>
    <button class="btn" onclick="generateQRCode()">Scan & Pay</button>
    <a class="btn" href="download_pdf.php?order_id=<?= $order_id ?>">Download PDF Bill</a>
    <button class="btn" onclick="window.print()">Print Bill</button>

    <div id="upi-instruction"></div>
    <div id="qr-code"></div>
</div>

<div style="text-align:center; margin-top: 30px;">
    <a href="your_orders.php" class="btn-back">⬅ Back to Orders</a>
</div>

<script>
function showUPIInstructions() {
    document.getElementById('upi-instruction').innerHTML = `
        <p><strong>Manual UPI Payment</strong></p>
        <p>Pay to: <b>9390198031@ybl</b></p>
        <p>Amount: ₹<?= number_format($final_total, 2) ?></p>
        <p>Use PhonePe, GPay, or Paytm and enter above details manually.</p>
    `;
    alert("Pay via your UPI app to 9390198031@ybl");
}

let qrGenerated = false;
let countdownSeconds = 300; // 5 minutes
let timer;

function startCountdown() {
    const qrContainer = document.querySelector('.container1');
    const timerDiv = document.createElement('div');
    timerDiv.id = 'countdown-timer';
    timerDiv.style.textAlign = "center";
    timerDiv.style.marginTop = "10px";
    timerDiv.style.fontWeight = "bold";
    qrContainer.insertBefore(timerDiv, qrContainer.firstChild);

    timer = setInterval(() => {
        const minutes = Math.floor(countdownSeconds / 60);
        const seconds = countdownSeconds % 60;
        timerDiv.textContent = `QR Code Expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
        if (--countdownSeconds < 0) {
            clearInterval(timer);
            document.getElementById('qr-code').innerHTML = "<p style='color:red;'>QR code expired. Please refresh the page to regenerate.</p>";
            document.getElementById('upi-instruction').innerHTML = "";
        }
    }, 1000);
}

function generateQRCode() {
    if (qrGenerated) return;
    qrGenerated = true;

    const qrDiv = document.getElementById('qr-code');
    qrDiv.innerHTML = "";
    const upiLink = "upi://pay?pa=9390198031@ybl&pn=WaterOrder💧&am=<?= $final_total ?>&cu=INR";

    QRCode.toCanvas(document.createElement('canvas'), upiLink, (error, canvas) => {
        if (error) console.error(error);
        else qrDiv.appendChild(canvas);
    });

    startCountdown();
}
</script>
</body>
</html>
