<?php
include 'db.php';

// Initialize variables to avoid undefined variable warnings
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if ($name && $email && $subject && $message) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = "Your message has been sent successfully!";
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Admin</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f8ff;
            padding: 40px;
        }
        .container {
            width: 500px;
            margin: auto;
            background: #fff;
            padding: 25px;
            box-shadow: 0 0 15px #ccc;
            border-radius: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #aaa;
        }
        input[type=submit] {
            background: #28a745;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
        }
        .message {
            margin-top: 10px;
            color: green;
        }
        .error {
            margin-top: 10px;
            color: red;
        }
         .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .back-button:active {
            background-color: #004080;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Contact Admin</h2>
    <?php if ($success) echo "<div class='message'>$success</div>"; ?>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Your Full Name" required>
        <input type="email" name="email" placeholder="Your Email Address" required>
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="message" rows="5" placeholder="Describe your issue here..." required></textarea>
        <input type="submit" value="Send Message">
        
<a href="home.php" class="back-button">← Go Back</a>
    </form>
</div>
</body>
</html>
