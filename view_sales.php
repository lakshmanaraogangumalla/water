<?php
include 'db.php';
session_start();

// Fetch sales
$stmt = $pdo->query("SELECT * FROM sales");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Sale Offers</h2>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Discount (%)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?= $sale['id'] ?></td>
                <td><?= htmlspecialchars($sale['description']) ?></td>
                <td><?= htmlspecialchars($sale['discount_percent']) ?></td>
                <td>
                    <form action="delete_sale.php" method="POST" onsubmit="return confirm('Delete sale?');">
                        <input type="hidden" name="id" value="<?= $sale['id'] ?>">
                        <button type="submit" style="background:red;color:white;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
