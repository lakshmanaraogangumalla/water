<?php
$host = 'localhost';        // Database host
$dbname = 'water';          // Database name
$username = 'root';         // Database username
$password = 'R@mu12072004'; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
