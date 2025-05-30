<?php
// Make sure you've run 'composer require endroid/qr-code' before using this

require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$qrImageData = null;
$canNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['can_number'])) {
    $canNumber = trim($_POST['can_number']);

    if ($canNumber !== '') {
        // Create QR code
        $qr = QrCode::create($canNumber)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qr);

        $qrImageData = $result->getString(); // binary PNG data
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>QR Code Generator for Water Cans</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        input, button { padding: 10px; font-size: 18px; margin-top: 10px; }
        img { margin-top: 20px; border: 1px solid #ccc; }
        a button { cursor: pointer; }
    </style>
</head>
<body>
    <h2>QR Code Generator for Water Cans</h2>

    <form method="POST" action="">
        <label for="can_number">Enter Can Number:</label><br />
        <input type="text" id="can_number" name="can_number" placeholder="e.g., CAN001" required value="<?= htmlspecialchars($canNumber) ?>" /><br />
        <button type="submit">Generate QR</button>
    </form>

    <?php if ($qrImageData !== null): ?>
        <h3>QR Code for: <?= htmlspecialchars($canNumber) ?></h3>
        <img src="data:image/png;base64,<?= base64_encode($qrImageData) ?>" alt="QR Code" />
        <br /><br />
        <a download="<?= htmlspecialchars($canNumber) ?>.png" href="data:image/png;base64,<?= base64_encode($qrImageData) ?>">
            <button>Download QR</button>
        </a>
    <?php endif; ?>
</body>
</html>
