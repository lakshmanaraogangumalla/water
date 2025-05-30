<?php
// bulk_update.php

session_start();
require 'db.php'; // your PDO connection file

// Only proceed if form is submitted with selected order IDs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_ids']) && is_array($_POST['order_ids'])) {
    
    // Sanitize and validate input
    $order_ids = array_map('intval', $_POST['order_ids']);
    $id_placeholders = implode(',', array_fill(0, count($order_ids), '?'));

    try {
        // Prepare the SQL update query with placeholders
        $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id IN ($id_placeholders)");
        $stmt->execute($order_ids);

        $_SESSION['success'] = count($order_ids) . " orders updated to 'completed'.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating orders: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "No orders selected.";
}

// Redirect back to admin dashboard
header("Location: admin_home.php");
exit();
?>
