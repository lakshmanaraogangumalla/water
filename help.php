<?php
include('db.php');  // Ensure db.php contains the PDO connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Optional: Fetch order history (if needed)
$sql = "SELECT * FROM orders WHERE user_id=:user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Help Desk - Water App</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Roboto', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(120deg, #74ebd5, #ACB6E5);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .chat-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      padding: 30px;
      width: 90%;
      max-width: 600px;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 28px;
      color: #fff;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }

    .chat-box {
      height: 300px;
      overflow-y: auto;
      padding: 15px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 15px;
      margin-bottom: 20px;
      color: #fff;
    }

    input[type="text"] {
      width: 100%;
      padding: 12px 15px;
      border-radius: 12px;
      border: none;
      margin-bottom: 10px;
      font-size: 16px;
    }

    button {
      padding: 10px 15px;
      border-radius: 10px;
      border: none;
      background-color: #ffffff;
      color: #333;
      font-weight: bold;
      margin-right: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: #e6e6e6;
      transform: scale(1.05);
    }

    #mic-btn {
      font-size: 18px;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.1); opacity: 0.7; }
    }

    .back-button {
      display: inline-block;
      margin-top: 20px;
      color: #fff;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    .back-button:hover {
      color: #ffefba;
    }

    @media (max-width: 500px) {
      .chat-container {
        padding: 20px;
      }

      button {
        width: 100%;
        margin-top: 10px;
      }

      input[type="text"] {
        margin-bottom: 15px;
      }
    }
  </style>
</head>
<body>

  <div class="chat-container">
    <h1>Help Desk - Water App</h1>
    <div id="chat-box" class="chat-box"></div>
    <input type="text" id="user-input" placeholder="Ask something..." />
    <button onclick="sendMessage()">Send</button>
    <button onclick="startVoice()" id="mic-btn">🎤 Speak</button>
    <br><br>
    <a href="home.php" class="back-button">← Back to Home</a>
  </div>

  <script src="help_script.js"></script>
  <script>
    window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  </script>
</body>
</html>
