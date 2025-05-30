<?php
include 'db.php';
$title = $_POST['title'];
$slug = $_POST['slug'];
$stmt = $pdo->prepare("INSERT INTO pages (title, slug) VALUES (?, ?)");
$stmt->execute([$title, $slug]);
header("Location: admin_create_page.php");
