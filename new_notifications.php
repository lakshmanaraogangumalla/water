<?php
include 'db.php';

$since = $_GET['since'] ?? '';
$stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_messages WHERE created_at > ?");
$stmt->execute([$since]);
$newCount = $stmt->fetchColumn();

echo json_encode(['new' => $newCount > 0]);
