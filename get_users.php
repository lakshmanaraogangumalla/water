<?php
// DB connection
$servername = "localhost";
$username = "root";
$password = "R@mu12072004";
$dbname = "water";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['searchTerm'])) {
    $searchTerm = "%" . $conn->real_escape_string($_GET['searchTerm']) . "%";
    $sql = "SELECT username, mobile FROM customers WHERE username LIKE ? OR mobile LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}

$conn->close();
?>
