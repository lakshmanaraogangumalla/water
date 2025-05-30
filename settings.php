<?php
include('db.php');  // Ensure db.php contains the PDO connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data to display in the settings form
$sql = "SELECT * FROM customers WHERE id=:user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Check if visibility is set, else default to 'public'
    $visibility = isset($_POST['visibility']) ? $_POST['visibility'] : 'public';

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update the user's settings
    $sql = "UPDATE customers SET email=:email, password=:password, visibility=:visibility WHERE id=:user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':visibility', $visibility);
    $stmt->bindParam(':user_id', $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Settings updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating settings');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - L2V4</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styling remains the same */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #fdfbfb, #ebedee);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .settings-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .settings-form input,
        .settings-form select {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .settings-form input:focus,
        .settings-form select:focus {
            border-color: #ff3366;
            outline: none;
        }

        .settings-form button {
            width: 100%;
            background-color: #ff3366;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .settings-form button:hover {
            background-color: #cc2e59;
        }

        .back-button {
            margin-top: 20px;
            display: block;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #3e8e41;
        }

        .icon {
            color: #888;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h2><i class="fa fa-cog icon"></i>Settings</h2>
        <form method="POST" class="settings-form">
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="Enter new email">

            <input type="password" name="password" required placeholder="Enter new password">

            <!-- Add visibility field for public or private settings -->
            <select name="visibility">
                <option value="public" <?php if ($user['visibility'] == 'public') echo 'selected'; ?>>Public Profile</option>
                <option value="private" <?php if ($user['visibility'] == 'private') echo 'selected'; ?>>Private Profile</option>
            </select>

            <button type="submit">Update Settings</button>
        </form>
        <a href="home.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</body>
</html>
