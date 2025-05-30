<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $value = intval($_POST['value']);

    if (empty($code)) {
        echo "<script>alert('Coupon code is required.'); window.history.back();</script>";
        exit();
    }

    if ($value < 1 || $value > 100) {
        echo "<script>alert('Coupon value must be between 1 and 100.'); window.history.back();</script>";
        exit();
    }

    // Check for duplicate code
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM coupons WHERE code = ?");
    $checkStmt->execute([$code]);
    if ($checkStmt->fetchColumn() > 0) {
        echo "<script>alert('Coupon code already exists.'); window.history.back();</script>";
        exit();
    }

    // Insert new coupon
    $stmt = $pdo->prepare("INSERT INTO coupons (code, value) VALUES (?, ?)");
    $stmt->execute([$code, $value]);

    header("Location: admin_home.php");
    exit();
}
?>
