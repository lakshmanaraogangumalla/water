<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];
$coupon_code = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : null;

try {
    // Fetch the latest pending order for the user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND order_status = 'pending' ORDER BY id DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("No pending order found.");
    }

    $order_id = $order['id'];
    $total = $order['total_price'];

    // Apply coupon logic if needed
    if ($coupon_code === "DISCOUNT50") {
        $discount = 50;
        $total = max(0, $total - $discount);

        // Update total and coupon in the order
        $updateStmt = $pdo->prepare("UPDATE orders SET total_price = ?, coupon_code = ? WHERE id = ?");
        $updateStmt->execute([$total, $coupon_code, $order_id]);
    }

    // Mark the order as paid
    $payStmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'Accepted', payment_time = NOW() WHERE id = ?");
    $payStmt->execute([$order_id]);

    echo "<h3>Payment Successful!</h3>";
    echo "<p>Total Paid: ₹" . number_format($total, 2) . "</p>";
    echo "<a href='your_orders.php'>View Your Orders</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
