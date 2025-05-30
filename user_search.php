<?php
require 'db.php'; // Your PDO connection

header('Content-Type: application/json');

$query = isset($_GET['name']) ? trim($_GET['name']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';

if ($query === '' || ($role !== 'admin' && $role !== 'user')) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, username, phone, email 
        FROM customers 
        WHERE role = ? AND (
            username LIKE ? OR phone LIKE ? OR email LIKE ?
        )
    ");
    $likeQuery = "%$query%";
    $stmt->execute([$role, $likeQuery, $likeQuery, $likeQuery]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
