<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $role = $_POST['role'];  // Get the role (User or Admin)

    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($role)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match("/^[0-9\-\+]{9,15}$/", $phone)) {
        $error = "Invalid phone number.";
    } else {
        // Check if email already exists
        $checkSql = "SELECT * FROM customers WHERE email = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            $error = "This email is already registered. <a href='login.php'>Login here</a>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Ensure that only authorized users can register as admin
            if ($role == 'admin') {
                $admin_check_sql = "SELECT * FROM customers WHERE role = 'admin'";
                $admin_check_stmt = $pdo->prepare($admin_check_sql);
                $admin_check_stmt->execute();
                if ($admin_check_stmt->rowCount() > 0) {
                    $error = "An admin already exists. Only one admin is allowed.";
                    $role = 'user';  // Default to 'user' if admin limit is reached
                }
            }

            $insertSql = "INSERT INTO customers (username, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $pdo->prepare($insertSql);

            if ($insertStmt->execute([$username, $email, $phone, $hashed_password, $role])) {
                $success = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $error = "Error inserting user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Signup - Water App</title>
    <style>
       body {
    margin: 0;
    padding: 0;
    background: #000;
    font-family: Arial, sans-serif;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.snow {
    position: fixed;
    top: -10px;
    width: 10px;
    height: 10px;
    background: white;
    border-radius: 50%;
    opacity: 0.8;
    animation: fall linear infinite;
    pointer-events: none;
}

@keyframes fall {
    to {
        transform: translateY(100vh);
    }
}

.signup-container {
    z-index: 10;
    text-align: center;
    padding: 40px 30px;
    color: white;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
}

input, button, select {
    padding: 10px;
    margin: 10px auto;
    width: 100%;
    max-width: 250px;
    border-radius: 5px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 14px;
}

input:focus, select:focus, button:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.4);
}

button {
    background: #4caf50;
    color: white;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #45a049;
}

select {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 14px;
}

.login-link {
    margin-top: 10px;
    display: block;
    color: #00f7ff;
    text-decoration: none;
}

.login-link:hover {
    text-decoration: underline;
}

.message {
    margin-top: 15px;
    font-weight: bold;
    color: #ff4444;
}

.message.success {
    color: #00ff99;
}

    </style>
</head>
<body>

<div class="signup-container">
    <h1>Signup</h1>
    <form method="POST" action="signup.php">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="phone" placeholder="Phone Number" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br>
        <button type="submit">Sign Up</button>
    </form>
    <a class="login-link" href="login.php">Already have an account? Login</a>

    <?php if (isset($error)): ?>
        <div class="message"><?= $error ?></div>
    <?php elseif (isset($success)): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>
</div>

<script>
    const snowflakeCount = 100;
    for (let i = 0; i < snowflakeCount; i++) {
        let snow = document.createElement("div");
        snow.className = "snow";
        snow.style.left = Math.random() * 100 + "vw";
        snow.style.animationDuration = (Math.random() * 3 + 2) + "s";
        snow.style.opacity = Math.random();
        snow.style.width = snow.style.height = (Math.random() * 5 + 5) + "px";
        document.body.appendChild(snow);
    }
</script>

</body>
</html>
