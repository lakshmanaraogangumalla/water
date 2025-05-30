<?php
require('fpdf/fpdf.php'); // Ensure this path is correct for your setup

// Fetch the order details from the database
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("Order ID missing.");
}

session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get coupon value (if any)
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

$total = 0;
foreach ($items as $item) {
    $total += $item['item_price'] * $item['quantity'];
}

$final_total = max(0, $total - $discount);

// Create PDF using FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set title using Arial font (built-in)
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(200, 10, "Order #$order_id", 0, 1, 'C');

// Add order details using Arial font (built-in)
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, "Customer Name: " . $order['customer_name']);
$pdf->Ln(6);
$pdf->Cell(100, 10, "Phone: " . $order['phone']);
$pdf->Ln(6);
$pdf->Cell(100, 10, "Address: " . $order['address']);
$pdf->Ln(6);
$pdf->Cell(100, 10, "Order Date: " . $order['order_time']);
$pdf->Ln(12);

// Add table header for order items
$pdf->Cell(80, 10, 'Item', 1, 0, 'C');
$pdf->Cell(30, 10, 'Qty', 1, 0, 'C');
$pdf->Cell(40, 10, 'Price', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 1, 'C');

// Add order items to PDF
foreach ($items as $item) {
    $pdf->Cell(80, 10, $item['item_name'], 1);
    $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(40, 10, '₹' . number_format($item['item_price'], 2), 1, 0, 'C');
    $pdf->Cell(40, 10, '₹' . number_format($item['item_price'] * $item['quantity'], 2), 1, 1, 'C');
}

// Add total and discount
$pdf->Ln(6);
$pdf->Cell(150, 10, "Subtotal", 0, 0, 'R');
$pdf->Cell(40, 10, '₹' . number_format($total, 2), 0, 1, 'C');

if ($discount > 0) {
    $pdf->Cell(150, 10, "Discount (Coupon: $coupon_code)", 0, 0, 'R');
    $pdf->Cell(40, 10, '-₹' . number_format($discount, 2), 0, 1, 'C');
}

$pdf->Cell(150, 10, "Final Amount", 0, 0, 'R');
$pdf->Cell(40, 10, '₹' . number_format($final_total, 2), 0, 1, 'C');

// Output PDF to browser for download
$pdf->Output('D', 'Order_' . $order_id . '.pdf');
?>
