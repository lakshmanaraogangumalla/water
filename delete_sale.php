<?php
include 'db.php';

// Check if ID is provided and valid
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];

    // Delete the sale
    $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect back to the page after deletion
    header("Location: admin_home.php");
    exit();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
