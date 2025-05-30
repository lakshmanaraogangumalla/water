<?php $order_id = $_GET['order_id'] ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Segoe UI',sans-serif;
  background:#f0f8ff;
  height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  animation:fadeIn 2s ease-in-out;
}
.box{
  background:#fff;
  padding:40px;
  border-radius:15px;
  box-shadow:0 8px 16px rgba(0,0,0,0.1);
  max-width:600px;
  text-align:center;
  transform:scale(0.9);
  animation:slideIn 1s ease-out forwards;
  opacity:0;
}
h1{
  color:#28a745;
  font-size:2.5rem;
  margin-bottom:20px;
  animation:fadeInUp 1s ease-out forwards;
}
p{
  font-size:1.2rem;
  margin-bottom:20px;
  animation:fadeInUp 1.2s ease-out forwards;
}
.order-id{
  font-size:1.5rem;
  font-weight:bold;
  color:#007bff;
  margin-bottom:20px;
  animation:fadeInUp 1.4s ease-out forwards;
}
a{
  display:inline-block;
  margin-top:20px;
  background:#004aad;
  color:#fff;
  padding:12px 30px;
  text-decoration:none;
  border-radius:25px;
  font-size:1.2rem;
  box-shadow:0 4px 10px rgba(0,0,0,0.1);
  transition:transform 0.3s ease;
  animation:fadeInUp 1.6s ease-out forwards;
}
a:hover{
  background:#002f6c;
  transform:translateY(-5px);
}
@keyframes fadeIn{0%{opacity:0;}100%{opacity:1;}}
@keyframes slideIn{0%{transform:scale(0.9);opacity:0;}100%{transform:scale(1);opacity:1;}}
@keyframes fadeInUp{0%{opacity:0;transform:translateY(20px);}100%{opacity:1;transform:translateY(0);}}
</style>
<script>
window.onload=function(){
  const orderId="<?= htmlspecialchars($order_id) ?>";
  fetch('notify_admin.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({order_id:orderId})
  })
  .then(response=>response.text())
  .then(data=>{console.log("Admin notified:",data);});
  const audio=new Audio('success.mp3');
  audio.play();
};
</script>
</head>
<body>
<div class="box">
  <h1>🎉 Order Placed Successfully!</h1>
  <p>Your order has been successfully placed.</p>
  <p class="order-id">Your Order ID is: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
  <p>Thank you for choosing us! We will deliver your order soon.</p>
  <a href="home.php">Return to Home</a>
</div>
</body>
</html>
