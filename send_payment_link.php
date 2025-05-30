<?php
// send_payment_link.php

// Fetch the POST data (phone number and payment link)
$data = json_decode(file_get_contents("php://input"), true);
$phone_number = $data['phone_number'];
$payment_link = $data['payment_link'];

// SMS gateway credentials (Example for Msg91 API)
$apiKey = 'YOUR_API_KEY'; // Replace with your actual API key
$senderId = 'WATERORDER'; // Your sender ID
$route = '4'; // Route for promotional messages

// Construct the message to send
$message = "Your payment link: " . $payment_link;

// Send the SMS using Msg91 API (or another service you're using)
$url = "https://api.msg91.com/api/sendhttp.php?authkey=$apiKey&mobiles=$phone_number&message=$message&sender=$senderId&route=$route&country=91";

// Send the request to the SMS API
$response = file_get_contents($url);

// Check if the response indicates success
if ($response) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
