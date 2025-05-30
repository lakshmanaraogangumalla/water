<?php
include('db.php');  // Include database connection

// Admin login check (Ensure that the admin is logged in)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: secure.php');  // Redirect to login page if not logged in
    exit();
}

// Fetch feedbacks from the database, ordered by creation date
$stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Feedbacks</title>
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
        }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #4A90E2;
            margin-bottom: 20px;
        }

        .table-container {
            max-height: 400px; /* Adjust the height as needed */
            overflow-y: auto;
            margin-top: 20px;
            width: 100%;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4A90E2;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: #4A90E2;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #357ab7;
        }
    </style>
</head>
<body>
    <div>
        <h1>All User Feedbacks</h1>

        <!-- Scrollable Table Container -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Complaint</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($feedback['product_name']); ?></td>
                            <td><?php echo $feedback['rating']; ?></td>
                            <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
                            <td><?php echo $feedback['complaint'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['phone']); ?></td>
                            <td><?php echo htmlspecialchars($feedback['address']); ?></td>
                            <td><?php echo $feedback['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <br>
        <a href="admin_home.php">Back to Dashboard</a>
    </div>
</body>
</html>
