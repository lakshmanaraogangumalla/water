<?php
require_once("PaytmChecksum.php");

$received_data = $_POST;
$paytmChecksum = $_POST["CHECKSUMHASH"];
$isVerifySignature = PaytmChecksum::verifySignature($received_data, "YOUR_MERCHANT_KEY", $paytmChecksum);

if ($isVerifySignature) {
    if ($_POST["STATUS"] == "TXN_SUCCESS") {
        echo "<h2>✅ Payment Successful</h2>";
        echo "Order ID: " . $_POST["ORDERID"] . "<br>";
        echo "Transaction ID: " . $_POST["TXNID"] . "<br>";
    } else {
        echo "<h2>❌ Payment Failed</h2>";
        echo $_POST["RESPMSG"];
    }
} else {
    echo "<h2>⚠️ Checksum Mismatch. Payment not valid.</h2>";
}
?>
