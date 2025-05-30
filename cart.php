<?php
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item = $_POST['item'];
    $price = $_POST['price'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $_SESSION['cart'][] = ['item' => $item, 'price' => $price, 'quantity' => $quantity];
    header('Location: cart.php');
    exit();
}

if (isset($_GET['delete'])) {
    $deleteIndex = $_GET['delete'];
    if (isset($_SESSION['cart'][$deleteIndex])) {
        unset($_SESSION['cart'][$deleteIndex]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body { font-family: Arial; background: #f0f8ff; padding: 20px; }
        .cart-item { background: #fff; padding: 10px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .cart-item h4 { margin: 0; }
        .cart-item .price { color: #009688; }
        .cart-item .remove { color: red; float: right; text-decoration: none; }
        .total { font-weight: bold; margin-top: 20px; }
        .btn-order { padding: 10px 15px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn-order:hover { background: #004d40; }
        .btn {
            margin-top: 10px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff3366;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background-color: #ff3366;
            transform: scale(1.05);
        }
        .center-btn { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<h2>Your Cart</h2>

<?php
$total = 0;
if (!empty($_SESSION['cart'])) {
    echo "<form action='place_order.php' method='post'>";
    foreach ($_SESSION['cart'] as $index => $cartItem) {
        $item = htmlspecialchars($cartItem['item']);
        $price = $cartItem['price'];
        $quantity = $cartItem['quantity'];
        $subtotal = $price * $quantity;
        $total += $subtotal;

        echo "<div class='cart-item'>
                <h4>$item <a class='remove' href='?delete=$index'>🗑</a></h4>
                <p class='price'>₹$price × $quantity = ₹$subtotal</p>
                <input type='hidden' name='product_name[]' value='$item'>
                <input type='hidden' name='quantity[]' value='$quantity'>
                <input type='hidden' name='price[]' value='$price'>
                <input type='hidden' name='total[]' value='$subtotal'>
              </div>";
    }
    echo "<p class='total'>Total: ₹$total</p>";
    echo "<button type='submit' class='btn-order'>Place Order</button>";
    echo "</form>";
} else {
    echo "<p>Your cart is empty.</p>";
}
?>
<div class="center-btn">
    <a href="home.php" class="btn">← Back</a>
</div>
</body>
</html>
