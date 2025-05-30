<?php
session_start();
include 'config_paytm.php';

$order_id = "ORD" . rand(10000, 99999999);
$_SESSION['ORDER_ID'] = $order_id;
$customer_id = "CUST" . rand(10000, 99999999);
$txn_amount = "100.00"; // Example amount

?>

<!DOCTYPE html>
<html>
<head>
    <title>Paytm Payment</title>
</head>
<body>
    <h2>Paytm Payment Gateway Integration</h2>
    <form method="post" action="pgRedirect.php">
        <input type="hidden" name="ORDER_ID" value="<?php echo $order_id; ?>">
        <input type="hidden" name="CUST_ID" value="<?php echo $customer_id; ?>">
        <input type="hidden" name="INDUSTRY_TYPE_ID" value="Retail">
        <input type="hidden" name="CHANNEL_ID" value="WEB">
        <input type="hidden" name="TXN_AMOUNT" value="<?php echo $txn_amount; ?>">
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>
