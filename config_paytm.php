<?php
define('PAYTM_ENVIRONMENT', 'PROD'); // Use 'TEST' for testing
define('PAYTM_MERCHANT_KEY', 'YOUR_MERCHANT_KEY');
define('PAYTM_MERCHANT_MID', 'YOUR_MERCHANT_ID');
define('PAYTM_MERCHANT_WEBSITE', 'YOUR_WEBSITE_NAME');

$PAYTM_DOMAIN = PAYTM_ENVIRONMENT == 'PROD' ? 'securegw.paytm.in' : 'securegw-stage.paytm.in';

define('PAYTM_STATUS_QUERY_URL', 'https://' . $PAYTM_DOMAIN . '/order/status');
define('PAYTM_TXN_URL', 'https://' . $PAYTM_DOMAIN . '/theia/processTransaction');
?>
