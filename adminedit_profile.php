<?php
session_start();
include('db.php');

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin data from the database
$sql = "SELECT * FROM customers WHERE id = :admin_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':admin_id', $admin_id);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "Admin not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $updateSql = "UPDATE customers SET username = :username, email = :email, phone = :phone WHERE id = :admin_id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->bindParam(':username', $username);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':phone', $phone);
    $updateStmt->bindParam(':admin_id', $admin_id);

    if ($updateStmt->execute()) {
        header('Location: adminprofile.php');
        exit();
    } else {
        echo "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Admin Profile</title>
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
.edit-profile-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 320px;
}
h2 {
    margin-bottom: 20px;
    color: #333;
}
input[type="text"], input[type="email"], input[type="tel"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
}
input[type="text"]:focus, input[type="email"]:focus, input[type="tel"]:focus {
    border-color: #ff3366;
    box-shadow: 0 0 5px rgba(255, 51, 102, 0.6);
}
.btn {
    margin-top: 10px;
    display: inline-block;
    padding: 10px 20px;
    background-color: #ff3366;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.3s ease;
}
.btn:hover {
    background-color: #ff3366;
    transform: scale(1.05);
}
.back-btn {
    margin-top: 20px;
    background: #4b4b4b;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
}
.back-btn:hover {
    background: #2c2c2c;
}
@keyframes fadeIn {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
.edit-profile-card {
    animation: fadeIn 1s ease-out;
}
</style>
</head>
<body>

<div class="edit-profile-card">
    <h2>Edit Admin Profile</h2>
    <form method="POST" action="">
        <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" placeholder="Username" required>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" placeholder="Email" required>
        <input type="tel" name="phone" value="<?= htmlspecialchars($admin['phone']) ?>" placeholder="Phone Number" required>
        <button type="submit" class="btn">Save Changes</button>
    </form>
    <a href="adminprofile.php" class="back-btn">← Back to Profile</a>
</div>

</body>
</html>
