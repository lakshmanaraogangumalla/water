<?php
session_start();
include 'config_paytm.php';
require_once 'lib/PaytmChecksum.php';

$paytmChecksum = $_POST["CHECKSUMHASH"] ?? "";
$isValidChecksum = PaytmChecksum::verifySignature($_POST, PAYTM_MERCHANT_KEY, $paytmChecksum);

if ($isValidChecksum) {
    if ($_POST["STATUS"] == "TXN_SUCCESS") {
        // Payment successful
        // Notify admin
        $admin_number = "9390198031";
        $message = "Payment successful for Order ID: " . $_POST["ORDERID"] . ", Amount: ₹" . $_POST["TXNAMOUNT"];
        // Implement SMS or email notification to admin here
        // For example, using an SMS API or mail function
        echo "<h2>Payment Successful</h2>";
    } else {
        echo "<h2>Payment Failed</h2>";
    }
} else {
    echo "<h2>Checksum Mismatch</h2>";
}
?>
