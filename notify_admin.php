<?php
$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? '';

if ($order_id) {
    $isPriority = rand(0, 1) === 1 ? "🚨 PRIORITY" : "Standard";
    $entry = "$isPriority Order: #$order_id at " . date('Y-m-d H:i:s') . "\n";
    file_put_contents('admin_notifications.txt', $entry, FILE_APPEND);
    echo "Notification Sent";
} else {
    echo "No Order ID Provided";
}
