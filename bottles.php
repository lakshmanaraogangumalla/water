<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Water Bottles - SR Water</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
    .order-section { max-width: 1000px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2 { text-align: center; }
    .price-table { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
    .card { background: #f1f8e9; padding: 20px; border-radius: 10px; width: 240px; text-align: center; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
    .card h3 { margin-top: 0; }
    .quantity { display: flex; justify-content: center; align-items: center; margin: 10px 0; }
    .quantity button { padding: 6px 12px; font-size: 16px; cursor: pointer; }
    .quantity input { width: 50px; text-align: center; font-size: 16px; margin: 0 5px; }
    .add-to-cart { background-color: #689f38; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
    .add-to-cart:hover { background-color: #33691e; }
    .cart-link { display: block; text-align: center; margin-top: 20px; font-weight: bold; text-decoration: none; color: #689f38; }
    .cart-link:hover { text-decoration: underline; }
  </style>
  <script>
    function changeQty(id, delta) {
      const input = document.getElementById(id);
      let value = parseInt(input.value);
      if (isNaN(value)) value = 0;
      value += delta;
      if (value < 0) value = 0;
      input.value = value;
    }
  </script>
</head>
<body>
<div class="order-section">
  <h2>Water Bottles</h2>
  <p style="text-align:center;">Select your preferred size of bottled water</p>
  <div class="price-table">
    <?php
    $bottles = [
      ['name' => '1 Liter Bottle', 'price' => 15, 'id' => 'qty1L'],
      ['name' => '500ml Bottle', 'price' => 10, 'id' => 'qty500ml'],
      ['name' => '2 Liter Bottle', 'price' => 25, 'id' => 'qty2L'],
      ['name' => '5 Liter Bottle', 'price' => 40, 'id' => 'qty5L']
    ];
    foreach ($bottles as $bottle) {
      echo '
      <div class="card">
        <h3>' . $bottle['name'] . '</h3>
        <p>₹' . $bottle['price'] . ' each</p>
        <form action="bottles.php" method="post">
          <div class="quantity">
            <button type="button" onclick="changeQty(\'' . $bottle['id'] . '\', -1)">-</button>
            <input type="number" name="quantity" id="' . $bottle['id'] . '" value="0" min="0">
            <button type="button" onclick="changeQty(\'' . $bottle['id'] . '\', 1)">+</button>
          </div>
          <input type="hidden" name="item" value="' . $bottle['name'] . '">
          <input type="hidden" name="price" value="' . $bottle['price'] . '">
          <button class="add-to-cart" type="submit" name="add">Add to Cart</button>
        </form>
      </div>';
    }
    ?>
  </div>
  <a href="cart.php" class="cart-link">View Cart</a>
</div>
<?php
if (isset($_POST['add'])) {
  $item = $_POST['item'];
  $price = $_POST['price'];
  $qty = $_POST['quantity'];
  if ($qty > 0) {
    $cartItem = ['item' => $item, 'price' => $price, 'quantity' => $qty];
    $_SESSION['cart'][] = $cartItem;
    echo "<script>alert('Added to cart: $item x $qty');</script>";
  } else {
    echo "<script>alert('Please select at least 1 quantity.');</script>";
  }
}
?>
</body>
</html>
