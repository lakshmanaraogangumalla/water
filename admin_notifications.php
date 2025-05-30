<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Fetch latest message time for JavaScript polling
$stmt = $pdo->query("SELECT MAX(created_at) as last_time FROM admin_messages");
$latestTime = $stmt->fetch(PDO::FETCH_ASSOC)['last_time'];

try {
    $stmt = $pdo->query("SELECT admin_messages.*, users.username 
                         FROM admin_messages 
                         JOIN users ON admin_messages.user_id = users.id 
                         ORDER BY admin_messages.created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Notifications</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f8ff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #004aad;
            margin-bottom: 20px;
        }
        .notification {
            border-left: 5px solid #007bff;
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .notification.order { border-color: green; }
        .notification.support { border-color: orange; }
        .notification.warning { border-color: red; }
        .message { font-size: 16px; color: #333; }
        .time { font-size: 13px; color: #777; margin-top: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Notifications</h2>
    <?php if (count($messages) === 0): ?>
        <p>No notifications found.</p>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="notification <?php echo htmlspecialchars($msg['type'] ?? ''); ?>">
                <div class="message">
                    🔔 <?php echo htmlspecialchars($msg['message']); ?>
                    (by <strong><?php echo htmlspecialchars($msg['username']); ?></strong>)
                    [<?php echo strtoupper($msg['type'] ?? 'info'); ?>]
                </div>
                <div class="time">🕒 <?php echo date("d M Y, h:i A", strtotime($msg['created_at'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- 🔊 Audio alert -->
<audio id="notif-sound" src="notification.mp3" preload="auto"></audio>

<script>
let lastTime = "<?php echo $latestTime; ?>";

setInterval(() => {
    fetch('new_notifications.php?since=' + encodeURIComponent(lastTime))
        .then(res => res.json())
        .then(data => {
            if (data.new) {
                document.getElementById('notif-sound').play();
                location.reload(); // Optional: can replace this with dynamic DOM update
            }
        });
}, 10000);
</script>
</body>
</html>
