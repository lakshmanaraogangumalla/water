<?php
// Admin page to view all orders and their items
$host = "localhost";
$username = "root";
$password = "R@mu12072004";
$database = "water"; // your database name
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders and their items
$sql = "SELECT o.id AS order_id, o.user_id, o.total_price, o.order_time, i.item_name, i.price, i.quantity
        FROM orders o
        LEFT JOIN order_items i ON o.id = i.order_id
        ORDER BY o.id DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Order ID: " . $row['order_id'] . "<br>";
        echo "User ID: " . $row['user_id'] . "<br>";
        echo "Total Price: ₹" . $row['total_price'] . "<br>";
        echo "Order Time: " . $row['order_time'] . "<br>";
        echo "Item: " . $row['item_name'] . " - ₹" . $row['price'] . " x " . $row['quantity'] . "<br>";
        echo "<hr>";
    }
} else {
    echo "No orders found.";
}

$conn->close();
?>
