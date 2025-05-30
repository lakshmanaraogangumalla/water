<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM customers WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Profile</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.profile-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 320px;
}
.profile-image-container {
    position: relative;
    display: inline-block;
    margin-bottom: 15px;
}
.glow-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 51, 102, 0.4), transparent 70%);
    animation: pulseGlow 2s infinite;
    z-index: 0;
}
@keyframes pulseGlow {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.7; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(1); opacity: 0.7; }
}
.profile-card img {
    z-index: 1;
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ff3366;
}
.profile-note {
    margin: 15px 0;
    font-size: 14px;
    color: #555;
}
.btn, .btn1 {
    margin-top: 10px;
    display: inline-block;
    padding: 10px 20px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.3s ease;
}
.btn1 {
    background-color: #4CAF50;
}
.btn1:hover {
    background-color: #45a049;
    transform: scale(1.05);
}
.back-btn {
    background: #4b4b4b;
    margin-left: 10px;
    position: relative;
    overflow: hidden;
}
.back-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    transition: left 0.4s ease;
}
.back-btn:hover::before {
    left: 100%;
}
.back-btn:hover {
    background: #2c2c2c;
}
</style>
</head>
<body>

<div class="profile-card">
    <h2><?= htmlspecialchars($user['username']) ?></h2>
    <p class="profile-note"><?= htmlspecialchars($user['email']) ?><br><?= htmlspecialchars($user['phone']) ?></p>
    <a href="edit_profile.php" class="btn1">Edit Profile</a>
    <a href="home.php" class="btn back-btn">← Back</a>
</div>

<script>
// JS for future features like toggling more profile options
console.log("Profile page loaded");
</script>

</body>
</html>
