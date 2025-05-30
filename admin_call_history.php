<?php
// Connect to the database
$pdo = new PDO("mysql:host=localhost;dbname=water", "root", "R@mu12072004");

// Fetch call history from the database
$history = $pdo->query("SELECT * FROM call_history ORDER BY call_start DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Call History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h2>📞 Admin Call History</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Caller</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Duration</th>
        </tr>
        <?php foreach ($history as $row):
            $start = new DateTime($row['call_start']);
            $endTime = '—';
            $duration = 'Ongoing';

            // Check if the call has ended, and calculate the duration
            if (!empty($row['call_end'])) {
                $end = new DateTime($row['call_end']);
                $endTime = $end->format('H:i:s');
                $interval = $start->diff($end);
                $duration = $interval->format('%h hr %i min %s sec');
            }
        ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['caller_name']) ?> (<?= htmlspecialchars($row['caller_id']) ?>)</td>
            <td><?= $start->format('Y-m-d') ?></td>
            <td><?= $start->format('H:i:s') ?></td>
            <td><?= $endTime ?></td>
            <td><?= $duration ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
