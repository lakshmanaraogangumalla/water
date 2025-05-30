<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Accessories - Dispensers, Stands & More</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#f0f2f5;margin:0;padding:0;animation:fadeIn 0.6s ease-in-out;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.container{width:90%;max-width:1200px;margin:40px auto;padding:20px;}
h1{text-align:center;color:#28a745;margin-bottom:10px;}
.card-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.card{background:white;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);padding:20px;transition:transform 0.3s ease,box-shadow 0.3s ease;position:relative;}
.card:hover{transform:translateY(-5px);box-shadow:0 6px 20px rgba(0,0,0,0.15);}
.card img{width:100%;height:200px;object-fit:cover;border-radius:8px;}
.card h3{margin:15px 0 10px;font-size:1.2rem;color:#333;}
.card p{font-size:0.95rem;color:#555;}
.price{font-weight:bold;margin-top:10px;}
form{margin-top:10px;}
.btn{padding:10px 15px;background:#28a745;color:white;border:none;border-radius:5px;cursor:pointer;transition:background 0.3s;}
.btn:hover{background:#218838;}
ul{margin-top:40px;padding-left:20px;}ul li{margin-bottom:10px;color:#444;}
</style>
</head>
<body>
<div class="container">
<h1>Water Accessories</h1>
<p>We offer a variety of water dispensers, stands, and other accessories to complement your water needs.</p>
<div class="card-grid">
<div class="card">
<img src="images/dispensers.jpg" alt="Water Dispenser">
<h3>Water Dispenser</h3>
<p>Stay hydrated with our easy-to-use dispensers. Perfect for homes and offices!</p>
<div class="price">Price: ₹500.00</div>
<form method="post" action="cart.php">
<input type="hidden" name="item" value="Water Dispenser">
<input type="hidden" name="price" value="500">
<input type="hidden" name="quantity" value="1">
<button type="submit" class="btn">Add to Cart</button>
</form>
</div>
<div class="card">
<img src="images/stands.jpg" alt="Water Stand">
<h3>Water Stand</h3>
<p>Keep your water container elevated with our sturdy and durable stands.</p>
<div class="price">Price: ₹300.00</div>
<form method="post" action="cart.php">
<input type="hidden" name="item" value="Water Stand">
<input type="hidden" name="price" value="300">
<input type="hidden" name="quantity" value="1">
<button type="submit" class="btn">Add to Cart</button>
</form>
</div>
</div>
<h3>Terms and Conditions</h3>
<ul>
<li>Accessories are non-refundable once delivered.</li>
<li>Products are subject to availability.</li>
<li>Delivery time may vary based on location.</li>
</ul>
</div>
</body>
</html>
