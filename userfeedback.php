<?php
include('db.php');  // Include database connection

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data
    $productName = trim($_POST['product_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $complaint = isset($_POST['complaint']) ? 1 : 0; // 1 for complaint, 0 for not
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Ensure that the rating is between 1 and 5
    if ($rating >= 1 && $rating <= 5) {
        // Insert the feedback into the database
        $stmt = $pdo->prepare("INSERT INTO feedback (product_name, rating, comment, complaint, username, phone, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$productName, $rating, $comment, $complaint, $username, $phone, $address]);

        // Redirect to a success message page or show a success alert
        echo "<script>alert('Feedback submitted successfully!');</script>";
    } else {
        echo "<script>alert('Please provide a valid rating between 1 and 5.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Feedback</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #4A90E2;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            transition: transform 0.3s ease-in-out;
            animation: slideIn 0.5s ease-out;
        }

        form:hover {
            transform: scale(1.02);
        }

        label {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: #4A90E2;
            outline: none;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button {
            background-color: #4A90E2;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #357ab7;
        }

        input[type="checkbox"] {
            margin-top: 10px;
        }

        /* Back Button */
        .back-button {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-size: 1.1rem;
            color: #4A90E2;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: #357ab7;
        }

        /* Animation for form */
        @keyframes slideIn {
            from {
                transform: translateY(-50%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Ensure body scroll is smooth */
        html, body {
            scroll-behavior: smooth;
        }

        /* Prevent page from overflowing vertically */
        .form-container {
            max-height: 100vh;
            overflow-y: auto;
        }

    </style>
</head>
<body>
    <div class="form-container">
        <h1>Provide Feedback for a Product</h1>

        <form method="POST" action="userfeedback.php">
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" required>

            <label for="rating">Rating (1 to 5):</label>
            <input type="number" name="rating" min="1" max="5" required>

            <label for="comment">Comment:</label>
            <textarea name="comment" required></textarea>

            <label for="complaint">Is this a complaint?</label>
            <input type="checkbox" name="complaint">

            <label for="username">Your Name:</label>
            <input type="text" name="username" required>

            <label for="phone">Your Phone Number:</label>
            <input type="text" name="phone" required>

            <label for="address">Your Address:</label>
            <input type="text" name="address" required>

            <button type="submit">Submit Feedback</button>
        </form>

        <a href="home.php" class="back-button">Back to Homepage</a>
    </div>
</body>
</html>
