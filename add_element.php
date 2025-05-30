<?php
include 'db.php';
$page_id = $_POST['page_id'];
$type = $_POST['type'];
$content = $_POST['content'];
$attributes = $_POST['attributes'];
$stmt = $pdo->prepare("INSERT INTO elements (page_id, type, content, attributes, position) VALUES (?, ?, ?, ?, 0)");
$stmt->execute([$page_id, $type, $content, $attributes]);
header("Location: admin_create_page.php?page_id=$page_id");
