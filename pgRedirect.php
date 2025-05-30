<?php
function notifyAdmin($orderId, $amount) {
    $adminNumber = "9390198031"; // Admin phone number
    $adminEmail = "admin@example.com"; // Optional: replace with actual admin email
    $subject = "New Successful Payment - Order ID: $orderId";
    $message = "Payment was successful.\n\nOrder ID: $orderId\nAmount: ₹$amount\n\nPlease take the next steps.";
    $headers = "From: no-reply@yourdomain.com";

    // Send email to admin
    if (mail($adminEmail, $subject, $message, $headers)) {
        error_log("Admin notified via email for Order $orderId");
    } else {
        error_log("Failed to notify admin via email.");
    }

    // OPTIONAL: Use an SMS API here if available
    /*
    $apiKey = "your_sms_api_key";
    $smsMessage = urlencode("Water Order - ₹$amount received for Order ID: $orderId.");
    $url = "https://api.yoursmsgateway.com/send?apikey=$apiKey&to=$adminNumber&message=$smsMessage";

    $response = file_get_contents($url);
    error_log("SMS sent to admin: $response");
    */
}
?>
