<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "Please enter your email.";
        exit();
    }

    $sql = "SELECT * FROM customers WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // In real projects, generate and send a reset link via email
        echo "A password reset link has been sent to your email (demo placeholder).";
    } else {
        echo "No account found with this email.";
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Forgot Password - Water App</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #000;
      overflow: hidden;
      font-family: Arial, sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .forgot-container {
      z-index: 10;
      text-align: center;
      padding: 40px 30px;
      color: white;
      border-radius: 15px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
    }
    input, button {
      padding: 10px;
      margin: 10px auto;
      width: 100%;
      max-width: 250px;
      border-radius: 5px;
      border: none;
    }
    button {
      background: #2196f3;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #1e88e5;
    }
    .back-link {
      margin-top: 10px;
      display: block;
      color: #00f7ff;
      text-decoration: none;
    }
    .back-link:hover {
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
  <div class="forgot-container">
    <h1>Forgot Password</h1>
    <form method="POST" action="forgotpassword.php">
      <input type="email" name="email" placeholder="Enter your registered email" required><br>
      <button type="submit">Send Reset Link</button>
    </form>
    <a class="back-link" href="login.php">Back to Login</a>
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
