<?php
include('db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email and password are empty
    if (empty($email) || empty($password)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Prepare SQL query to fetch user data
    $sql = "SELECT * FROM customers WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check if user exists and verify password
    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Set session variables for logged-in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role (admin or user)
            if ($user['role'] === 'admin') {
                header("Location: secure.php");  // Admin's home page
            } else {
                header("Location: home.php");  // Regular user's home page
            }
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No account found with that email.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Water App</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background:rgb(22, 23, 23);
}

nav {
    background: #006064;
    padding: 50px;
    display: flex;
    justify-content: space-around;
    color: white;
}

nav a {
    text-decoration: none;
    color: white;
    font-weight: bold;
}

.menu {
    position: absolute;
    top: 10px;
    right: 20px;
    color: #fff;
}

.menu .dropdown {
    display: none;
    position: absolute;
    background: #004d40;
    top: 40px;
    right: 0;
    padding: 10px;
    border-radius: 10px;
    z-index: 999;
}

.menu:hover .dropdown {
    display: block;
    background: rgb(184, 247, 236);
    color: #000;
}

table {
    border-collapse: collapse;
    width: 90%;
    margin: 30px auto;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

th,
td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #006064;
    color: white;
}

tr:hover {
    background-color: #e0f7fa;
}

.highlight {
    background-color: #ffeaea !important;
}

.tomorrow-highlight {
    background-color: #fff9e6 !important;
}

.accept-btn {
    background-color: #4CAF50;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.accept-btn:hover {
    background-color: #388e3c;
}

h1 {
    text-align: center;
    margin-top: 30px;
    color: #004d40;
}

.login-container {
    padding: 30px;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    text-align: center;
    width: 100%;
    max-width: 350px;
    margin: 0 auto;
}

input,
button {
    width: 100%;
    max-width: 250px;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: none;
}

button {
    background: #2196f3;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #1976d2;
}

a {
    color: #00f7ff;
    text-decoration: none;
    display: block;
    margin-top: 10px;
}

a:hover {
    text-decoration: underline;
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

        </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Log In</button>
        </form>
        <a href="signup.php">Don't have an account? Sign Up</a>
        <a href="forgotpassword.php">Forgot Password?</a>
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
