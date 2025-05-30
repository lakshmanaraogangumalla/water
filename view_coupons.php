<?php
include 'db.php';
session_start();

// Fetch all coupons
$stmt = $pdo->query("SELECT * FROM coupons ORDER BY id DESC");
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Coupons</h2>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Value (%)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($coupons as $coupon): ?>
            <tr>
                <td><?= $coupon['id'] ?></td>
                <td><?= htmlspecialchars($coupon['code']) ?></td>
                <td><?= htmlspecialchars($coupon['value']) ?></td>
                <td>
                    <form action="delete_coupon.php" method="POST" onsubmit="return confirm('Delete this coupon?');">
                        <input type="hidden" name="id" value="<?= $coupon['id'] ?>">
                        <button type="submit" style="background:red; color:white;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

