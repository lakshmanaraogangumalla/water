<?php
// Start the session
session_start();

// Password validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the entered password matches
    if (isset($_POST['password']) && $_POST['password'] === 'GANGUMALLA') {
        // Set session variable to indicate that the admin is logged in
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_home.php'); // Redirect to admin home
        exit();
    } else {
        // Invalid password
        $error_message = "Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* Basic styling for the login form */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f2f2f2;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>
    <form action="secure.php" method="POST">
        <input type="password" name="password" placeholder="Enter Admin Password" required>
        <button type="submit">Submit</button>
    </form>

    <?php if (isset($error_message)) : ?>
        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
</div>

</body>
</html>
