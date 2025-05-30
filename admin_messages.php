<?php
include 'db.php';

$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Contact Messages</title>
   <style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 20px;
    }

    .back-button {
        display: inline-block;
        margin-bottom: 20px;
        background-color: #007BFF;
        color: white;
        padding: 10px 16px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .back-button:hover {
        background-color: #0056b3;
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }

    .message-card {
        background: white;
        border: 1px solid #ddd;
        border-left: 5px solid #007BFF;
        padding: 15px 20px;
        margin: 20px auto;
        width: 80%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    .message-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .message-card strong {
        font-size: 18px;
        color: #007BFF;
    }

    .message-info {
        margin-top: 5px;
        color: #666;
        font-size: 14px;
    }

    .message-content {
        margin-top: 10px;
        font-size: 15px;
        color: #333;
        white-space: pre-line;
    }
</style>

</head>
<body>
    <h2>Contact Messages</h2>
    <?php foreach ($messages as $msg): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <strong><?php echo htmlspecialchars($msg['subject']); ?></strong><br>
            From: <?php echo htmlspecialchars($msg['name']); ?> (<?php echo htmlspecialchars($msg['email']); ?>)<br>
            Date: <?php echo $msg['created_at']; ?><br>
            <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
        </div>
    <?php endforeach; ?>
    <a href="admin_home.php" class="back-button">← Back to Admin Home</a>

</body>
</html>
