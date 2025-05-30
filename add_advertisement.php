<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_text = $_POST['ad_text'];
    $media_path = '';

    // Handle file upload
    if (isset($_FILES['media_files']) && $_FILES['media_files']['error'][0] === 0) {
        $upload_dir = 'uploads/ads/';
        $media_files = $_FILES['media_files'];
        $file_names = [];

        for ($i = 0; $i < count($media_files['name']); $i++) {
            $tmp_name = $media_files['tmp_name'][$i];
            $name = basename($media_files['name'][$i]);
            $target_file = $upload_dir . time() . '_' . $name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $file_names[] = $target_file;
            }
        }

        $media_path = implode(',', $file_names);
    }

    $stmt = $pdo->prepare("INSERT INTO advertisements (ad_text, media_path) VALUES (?, ?)");
    $stmt->execute([$ad_text, $media_path]);

    header('Location: admin_home.php');
    exit();
}
?>
