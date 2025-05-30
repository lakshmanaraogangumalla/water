<?php
require_once 'db.php';

$stmt = $pdo->query("SELECT * FROM advertisements ORDER BY created_at DESC");
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Advertisements</h2>

<?php foreach ($ads as $ad): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin: 10px;">
        <p><?= htmlspecialchars($ad['ad_text']) ?></p>
        <?php
        $mediaFiles = explode(',', $ad['media_path']);
        foreach ($mediaFiles as $file):
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<img src='$file' width='150' style='margin:5px'>";
            } elseif (in_array($ext, ['mp4', 'webm'])) {
                echo "<video width='200' controls style='margin:5px'><source src='$file' type='video/$ext'></video>";
            }
        endforeach;
        ?>
        <br>
        <form action="delete_ad.php" method="POST" onsubmit="return confirm('Are you sure to delete?')">
            <input type="hidden" name="id" value="<?= $ad['id'] ?>">
            <button type="submit">Delete</button>
        </form>
    </div>
<?php endforeach; ?>
