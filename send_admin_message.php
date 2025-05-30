<?php
include('db.php');  // Include the database connection

// Check if the request is a POST and contains message data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    $message = trim($inputData['message']);

    if (!empty($message)) {
        // Prepare and execute the query to insert the admin message into the database
        $stmt = $pdo->prepare("INSERT INTO helpdesk_messages (sender, message, timestamp) VALUES ('admin', ?, NOW())");
        $stmt->execute([$message]);

        // Return success response
        echo json_encode(["success" => true]);
    } else {
        // Return failure response if no message was provided
        echo json_encode(["success" => false, "error" => "Message cannot be empty."]);
    }
}
?>
