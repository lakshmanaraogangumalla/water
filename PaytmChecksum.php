<?php
require_once("PaytmChecksum.php");

$paytmParams = array();

$paytmParams["MID"] = "YOUR_MID_HERE";
$paytmParams["WEBSITE"] = "WEBSTAGING"; // For test, use "DEFAULT" for production
$paytmParams["INDUSTRY_TYPE_ID"] = "Retail";
$paytmParams["CHANNEL_ID"] = "WEB";
$paytmParams["ORDER_ID"] = "ORD" . rand(10000, 99999);
$paytmParams["CUST_ID"] = "CUST001";
$paytmParams["TXN_AMOUNT"] = "500.00";
$paytmParams["CALLBACK_URL"] = "http://localhost/paytm/callback.php"; // Change for production

$paytmParams["CHECKSUMHASH"] = PaytmChecksum::generateSignature($paytmParams, "YOUR_MERCHANT_KEY");

?>

<html>
    <body>
        <h1>Redirecting to Paytm...</h1>
        <form method="post" action="https://securegw-stage.paytm.in/order/process" name="paytmForm">
            <?php foreach ($paytmParams as $name => $value): ?>
                <input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
            <?php endforeach; ?>
        </form>
        <script type="text/javascript">
            document.paytmForm.submit();
        </script>
    </body>
</html>
