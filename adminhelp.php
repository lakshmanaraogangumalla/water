<?php
include('db.php');  // Ensure db.php contains the PDO connection
session_start();

// Admin login check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: secure.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Help Chat - Water App</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <style>
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: white;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .chat-container {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      width: 90%;
      max-width: 600px;
      padding: 30px;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 28px;
      color: #00ffff;
    }

    .chat-box {
      height: 300px;
      background: rgba(255, 255, 255, 0.07);
      border-radius: 15px;
      overflow-y: auto;
      padding: 15px;
      margin-bottom: 15px;
      border: 1px solid rgba(255,255,255,0.2);
      animation: slideIn 1s ease;
    }

    @keyframes slideIn {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    input[type="text"] {
      width: 70%;
      padding: 10px;
      border: none;
      border-radius: 10px;
      margin-right: 10px;
      font-size: 16px;
    }

    button {
      padding: 10px 15px;
      border: none;
      border-radius: 10px;
      background-color: #00ffff;
      color: #000;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #00dddd;
    }

    #mic-btn {
      margin-top: 10px;
      font-size: 18px;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.1); opacity: 0.7; }
      100% { transform: scale(1); opacity: 1; }
    }

    .back-button {
      display: inline-block;
      margin-top: 20px;
      color: #00ffff;
      text-decoration: none;
      font-size: 16px;
      transition: transform 0.2s;
    }

    .back-button:hover {
      transform: translateX(-5px);
    }

    @media (max-width: 600px) {
      .chat-container {
        padding: 20px;
      }

      input[type="text"] {
        width: 100%;
        margin-bottom: 10px;
      }

      button {
        width: 100%;
        margin-top: 5px;
      }
    }
  </style>
</head>
<body>

  <div class="chat-container">
    <h1>Admin Help Desk</h1>
    <div id="chat-box" class="chat-box"></div>
    <input type="text" id="user-input" placeholder="Type a message..." />
    <button onclick="sendMessage()">Send</button>
    <button onclick="startVoice()" id="mic-btn">🎤 Speak</button>
    <br>
    <a href="admin_home.php" class="back-button">← Back to Dashboard</a>
  </div>

  <script src="admin_helpdesk.js"></script>
  <script>
    window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  </script>
</body>
</html>
