<?php
function sendSMS($phone, $message) {
    $apiKey = "YOUR_API_KEY_HERE"; // Replace with your actual Fast2SMS API key

    $url = "https://www.fast2sms.com/dev/bulkV2";
    $data = [
        "authorization" => $apiKey,
        "sender_id" => "TXTIND",
        "message" => $message,
        "language" => "english",
        "route" => "v3",
        "numbers" => $phone
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => [
            "authorization: $apiKey",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ],
    ]);

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $response;
}
?>
