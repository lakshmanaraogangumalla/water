<?php
require_once 'vendor/autoload.php'; // Ensure you have installed the QR code library via Composer

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$upi_id = "yourmerchant@upi";
$payee_name = "WaterOrder";
$amount = "100.00"; // Example amount

$upi_link = "upi://pay?pa={$upi_id}&pn={$payee_name}&am={$amount}&cu=INR";

$qrCode = QrCode::create($upi_link);
$writer = new PngWriter();
$result = $writer->write($qrCode);

// Output the QR code image
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
?>
