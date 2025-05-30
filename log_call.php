<?php
require 'db.php';
$data = json_decode(file_get_contents("php://input"), true);
$stmt = $pdo->prepare("INSERT INTO call_logs (user_id, duration, timestamp) VALUES (?, ?, NOW())");
$stmt->execute([$data['userId'], $data['duration']]);
echo json_encode(['status' => 'success']);
?>
