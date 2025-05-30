<?php
include('db.php');  // Include the database connection

// Query to get all messages from the database
$stmt = $pdo->query("SELECT sender, message, timestamp FROM helpdesk_messages ORDER BY timestamp ASC");

// Fetch all messages and prepare the response
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the messages as a JSON response
echo json_encode($messages);
?>
