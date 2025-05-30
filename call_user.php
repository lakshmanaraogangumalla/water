<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['caller_id'])) {
    $caller_id = $_POST['caller_id'];

    // Get customer info
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$caller_id]);
    $customer = $stmt->fetch();

    if ($customer) {
        $caller_name = $customer['name'];
        $call_start = date('Y-m-d H:i:s');
        $call_end = date('Y-m-d H:i:s', strtotime('+2 minutes')); // Simulated end

        $stmt = $pdo->prepare("INSERT INTO call_history (caller_id, caller_name, call_start, call_end) VALUES (?, ?, ?, ?)");
        $stmt->execute([$caller_id, $caller_name, $call_start, $call_end]);

        echo "Call with " . htmlspecialchars($caller_name) . " logged successfully.";
    } else {
        echo "Customer not found.";
    }
}
?>
