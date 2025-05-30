<?php
include 'db.php';

// Check if ID is provided and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the advertisement details to remove media files
    $stmt = $pdo->prepare("SELECT media_path FROM advertisements WHERE id = ?");
    $stmt->execute([$id]);
    $ad = $stmt->fetch();

    if ($ad) {
        // Delete the media files from the server (if applicable)
        $media_files = explode(',', $ad['media_path']);
        foreach ($media_files as $file) {
            if (file_exists($file)) {
                unlink($file); // Delete the file from the server
            }
        }

        // Now delete the advertisement entry from the database
        $stmt = $pdo->prepare("DELETE FROM advertisements WHERE id = ?");
        $stmt->execute([$id]);

        // Redirect back to the page after deletion
        header("Location: admin_home.php");
        exit();
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
