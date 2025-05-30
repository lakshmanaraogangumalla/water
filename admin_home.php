<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting (optional for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$adminName = $_SESSION['username'];
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Fetch orders for today and tomorrow
$sql = "SELECT 
            orders.id, orders.customer_name, orders.phone, orders.address, 
            orders.delivery_time, orders.order_date, 
            order_items.item_name, order_items.quantity, order_items.item_price, 
            (order_items.quantity * order_items.item_price) AS total_price, 
            orders.status, orders.delivery_status 
        FROM orders 
        LEFT JOIN order_items ON orders.id = order_items.order_id 
        WHERE DATE(orders.order_date) = :today OR DATE(orders.order_date) = :tomorrow 
        ORDER BY orders.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':today', $today);
$stmt->bindParam(':tomorrow', $tomorrow);
$stmt->execute();
$orders = $stmt->fetchAll();

// Fetch all coupons
$stmtCoupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC");
$coupons = $stmtCoupons->fetchAll(PDO::FETCH_ASSOC);

// Fetch all sales
$stmtSales = $pdo->query("SELECT * FROM sales");
$sales = $stmtSales->fetchAll(PDO::FETCH_ASSOC);

// Handle item deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_home.php");
    exit;
}

// Handle new item insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_name'], $_POST['price'])) {
    $item = $_POST['item_name'];
    $price = $_POST['price'];
    $stmt = $pdo->prepare("INSERT INTO items (name, price) VALUES (?, ?)");
    $stmt->execute([$item, $price]);
    header("Location: admin_home.php");
    exit;
}

// Fetch all items
$stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
$items = $stmt->fetchAll();

// Handle call history logging via raw JSON input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['caller_id'], $data['caller_name'], $data['call_start'], $data['call_end'])) {
        $stmt = $pdo->prepare("INSERT INTO call_history (caller_id, caller_name, call_start, call_end) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['caller_id'],
            $data['caller_name'],
            $data['call_start'],
            $data['call_end']
        ]);
        echo json_encode(["status" => "success", "message" => "Call logged successfully."]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid input."]);
        exit;
    }
}
// search-users.php logic directly in the page
$users = [];
if (isset($_GET['query'])) {
    $search = trim($_GET['query']);
    if (!empty($search)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=water", "root", "R@mu12072004");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT id, username, phone FROM customers WHERE username LIKE ? OR phone LIKE ?");
            $stmt->execute(["%$search%", "%$search%"]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
:root {
--primary: #6a1b9a;
--secondary: #283593;
--accent: #00bcd4;
--warning: #f44336;
--light-bg: #f3e5f5;
--white: #ffffff;
--text-dark: #1a237e;
--hover-shadow: 0 8px 24px rgba(106, 27, 154, 0.25);
}

body {
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
background-color: var(--light-bg);
margin: 0;
padding: 0;
animation: fadeInBody 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
color: var(--text-dark);
}

@keyframes fadeInBody {
from {
opacity: 0;
transform: translateY(40px);
}
to {
opacity: 1;
transform: translateY(0);
}
}

h1, h2 {
text-align: center;
color: var(--secondary);
margin: 30px 0;
animation: fadeInUp 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes fadeInUp {
from {
opacity: 0;
transform: translateY(20px);
}
to {
opacity: 1;
transform: translateY(0);
}
}

nav {
background-color: var(--primary);
padding: 15px 25px;
display: flex;
justify-content: center;
flex-wrap: wrap;
gap: 16px;
position: sticky;
top: 0;
z-index: 999;
box-shadow: 0 4px 12px rgba(0,0,0,0.1);
animation: slideDown 1s ease-out;
}

@keyframes slideDown {
from {
transform: translateY(-100%);
opacity: 0;
}
to {
transform: translateY(0);
opacity: 1;
}
}

nav a.nav-btn {
background: transparent;
border: 2px solid transparent;
color: var(--white);
font-weight: 600;
font-size: 16px;
padding: 10px 18px;
border-radius: 8px;
cursor: pointer;
text-decoration: none;
display: flex;
align-items: center;
gap: 8px;
transition:
color 0.3s ease,
border-color 0.3s ease,
box-shadow 0.3s ease,
transform 0.3s ease;
}

nav a.nav-btn:hover,
nav a.nav-btn:focus {
color: var(--accent);
border-color: var(--accent);
box-shadow: var(--hover-shadow);
transform: scale(1.1);
outline: none;
}

.btn-primary {
background-color: var(--secondary);
border-color: var(--secondary);
transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary:hover,
.btn-primary:focus {
background-color: #43a047;
border-color: var(--accent);
color: var(--white);
box-shadow: 0 0 14px var(--accent);
}

.btn-success {
background-color: #4caf50;
border-color: #4caf50;
color: var(--white);
transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.btn-success:hover,
.btn-success:focus {
background-color: #43a047;
border-color: #43a047;
box-shadow: 0 0 14px #43a047;
}

.card {
opacity: 0;
transform: translateY(30px);
animation: fadeInCard 1s forwards;
}

@keyframes fadeInCard {
to {
opacity: 1;
transform: translateY(0);
}
}

a {
transition: color 0.3s ease;
}

a:hover {
color: var(--warning);
}

input:focus, textarea:focus, select:focus {
outline: none;
border: 2px solid var(--accent);
box-shadow: 0 0 10px var(--accent);
transition: all 0.3s ease;
}

/* FORMS */
form {
  background: var(--white);
  padding: 30px;
  border-radius: 14px;
  max-width: 460px;
  margin: 50px auto;
  box-shadow: var(--hover-shadow);
  animation: slideInForm 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes slideInForm {
  from {
    opacity: 0;
    transform: translateX(-60px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

input[type="text"],
input[type="number"],
input[type="file"] {
  width: 100%;
  padding: 14px;
  margin: 14px 0;
  border: 1.8px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
  background: #fafafa;
  transition: border-color 0.3s ease;
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="file"]:focus {
  border-color: var(--accent);
  outline: none;
  box-shadow: 0 0 8px var(--accent);
}

input[type="submit"],
.accept-btn,
.update-btn {
  background: var(--secondary);
  color: var(--white);
  border: none;
  padding: 12px 24px;
  margin-top: 16px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-size: 16px;
  transition:
    background 0.35s ease,
    transform 0.25s ease,
    box-shadow 0.35s ease;
}

input[type="submit"]:hover,
input[type="submit"]:focus,
.accept-btn:hover,
.accept-btn:focus,
.update-btn:hover,
.update-btn:focus {
  background: var(--primary);
  transform: scale(1.07);
  box-shadow: 0 0 20px rgba(42, 0, 80, 0.4);
  outline: none;
}

.delete-btn {
  background: var(--warning);
  color: var(--white);
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  transition: background 0.3s ease, transform 0.2s ease;
  cursor: pointer;
}

.delete-btn:hover,
.delete-btn:focus {
  background: darkred;
  transform: scale(1.07);
  outline: none;
}
/* MENUS */
.menu, .menu1 {
  position: absolute;
  top: 18px;
  color: #fff;
  font-weight: 700;
  cursor: pointer;
  z-index: 1000;
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  user-select: none;
  animation: popIn 0.8s ease;
  perspective: 1000px;
}

@keyframes popIn {
  0% {
    opacity: 0;
    transform: scale(0.6) rotateY(-60deg);
  }
  60% {
    opacity: 1;
    transform: scale(1.1) rotateY(10deg);
  }
  100% {
    transform: scale(1) rotateY(0deg);
  }
}

.menu {
  right: 22px;
  text-align: right;
}

.menu1 {
  left: 22px;
  text-align: left;
}

.menu:hover, .menu:focus,
.menu1:hover, .menu1:focus {
  transform: rotateY(5deg) scale(1.1);
  outline: none;
  animation: pulseGlow 0.6s ease-in-out;
}

@keyframes pulseGlow {
  0% {
    text-shadow: 0 0 0 rgba(255,255,255,0.2);
  }
  50% {
    text-shadow: 0 0 15px rgba(255,255,255,0.5);
  }
  100% {
    text-shadow: 0 0 0 rgba(255,255,255,0.2);
  }
}

.menu .dropdown,
.menu1 .dropdown {
  display: block;
  position: absolute;
  top: 38px;
  background: linear-gradient(135deg, #512da8, #7e57c2);
  padding: 14px 20px;
  border-radius: 14px;
  opacity: 0;
  pointer-events: none;
  transform: rotateX(-90deg);
  transition: all 0.6s ease;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  min-width: 180px;
  z-index: 1001;
  transform-origin: top;
}

.menu .dropdown {
  right: 0;
}

.menu1 .dropdown {
  left: 0;
}

.menu:hover .dropdown,
.menu:focus .dropdown,
.menu1:hover .dropdown,
.menu1:focus .dropdown {
  opacity: 1;
  pointer-events: auto;
  transform: rotateX(0deg);
  animation: dropdown3D 0.6s ease forwards;
}

@keyframes dropdown3D {
  0% {
    opacity: 0;
    transform: rotateX(-90deg);
  }
  100% {
    opacity: 1;
    transform: rotateX(0deg);
  }
}

.menu .dropdown p,
.menu .dropdown a,
.menu1 .dropdown p,
.menu1 .dropdown a {
  color: var(--white);
  margin: 8px 0;
  padding: 10px 16px;
  border-radius: 10px;
  text-decoration: none;
  display: block;
  font-weight: 600;
  opacity: 0;
  transform: translateX(-20px) scale(0.9);
  animation: dropdownItemSlide 0.6s forwards;
  transition: background 0.35s ease, transform 0.25s ease;
}

.menu .dropdown p:nth-child(1),
.menu .dropdown a:nth-child(1),
.menu1 .dropdown p:nth-child(1),
.menu1 .dropdown a:nth-child(1) {
  animation-delay: 0.2s;
}
.menu .dropdown p:nth-child(2),
.menu .dropdown a:nth-child(2),
.menu1 .dropdown p:nth-child(2),
.menu1 .dropdown a:nth-child(2) {
  animation-delay: 0.35s;
}
.menu .dropdown p:nth-child(3),
.menu .dropdown a:nth-child(3),
.menu1 .dropdown p:nth-child(3),
.menu1 .dropdown a:nth-child(3) {
  animation-delay: 0.5s;
}

@keyframes dropdownItemSlide {
  0% {
    opacity: 0;
    transform: translateX(-20px) scale(0.9);
  }
  100% {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

.menu .dropdown p:hover,
.menu .dropdown a:hover,
.menu1 .dropdown p:hover,
.menu1 .dropdown a:hover {
  background: #b39ddb;
  transform: translateX(10px) scale(1.1);
  box-shadow: 0 3px 10px rgba(255, 255, 255, 0.2);
}


/* TABLE */
table {
  width: 90%;
  margin: 30px auto;
  border-collapse: collapse;
  background: white;
  box-shadow: var(--hover-shadow);
  animation: fadeInTable 1s ease;
}

@keyframes fadeInTable {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

th, td {
  padding: 14px;
  border: 1px solid #d1c4e9;
  text-align: center;
}

th {
  background: var(--secondary);
  color: white;
  font-weight: bold;
}

tr {
  transition: all 0.3s ease;
}

tr:hover {
  background-color: #e1f5fe;
  transform: scale(1.01);
  box-shadow: 0 5px 12px rgba(0, 150, 136, 0.15);
}

/* IMAGES & VIDEOS */
img, video {
  max-width: 100px;
  border-radius: 6px;
  margin: 5px;
}

/* SELECT & BUTTON */
.select, button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* INCOMING CALL */
#incomingCallDiv {
  display: none;
  background-color: #ffcccb;
  padding: 10px;
  border-radius: 10px;
  animation: ringPulse 1s infinite;
}

@keyframes ringPulse {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,0,0,0.7); }
  70% { transform: scale(1.05); box-shadow: 0 0 10px 20px rgba(255,0,0,0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,0,0,0); }
}

#callStatus, #timer, #hangUpBtn, #muteBtn {
  display: none;
}

/* RESPONSIVE */
@media screen and (max-width: 768px) {
  nav {
    flex-direction: column;
  }

  table, form {
    width: 95%;
    font-size: 14px;
  }

  .menu, .menu1 {
    position: static;
    margin: 10px;
    text-align: center;
  }

  .menu .dropdown, .menu1 .dropdown {
    position: static;
    transform: none;
    display: block;
    opacity: 1;
    background: #616161;
    margin-top: 10px;
  }

  .dropdown a p {
    background: #757575;
    margin: 5px 0;
    padding: 8px;
    border-radius: 6px;
  }
}

/* General Styling */

/* Dashboard Stats */
.dashboard-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.dashboard-stats div {
    flex: 1;
    background: #ffffff;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    font-size: 16px;
    font-weight: bold;
}

/* Date Filter Form */
form {
    margin-bottom: 20px;
}

form input[type="date"],
form button {
    padding: 10px;
    margin-right: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

form button {
    background: #3498db;
    color: #fff;
    border: none;
    cursor: pointer;
}

form button:hover {
    background: #2980b9;
}

/* Orders Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}


    </style>
</head>
<body>
<nav role="navigation" aria-label="Admin dashboard navigation">
  <a href="admin_home.php" class="nav-btn">
    <i class="fa fa-home" aria-hidden="true"></i> Home
  </a>
  <a href="adminhelp.php" class="nav-btn">
    <i class="fa fa-question-circle" aria-hidden="true"></i> Help
  </a>
  <a href="allbills.php" class="nav-btn">
    <i class="fa fa-book" aria-hidden="true"></i> All Bills
  </a>
  <a href="notepad.php" class="nav-btn" role="menuitem">
    <i class="fa fa-sticky-note" aria-hidden="true"></i> Notepad
  </a>
  <a href="history.php" class="nav-btn btn-primary">
    <i class="fa fa-history" aria-hidden="true"></i> All Orders History
  </a>
  <a href="adminview_feedback.php" class="nav-btn btn-success">
    <i class="fas fa-user-circle" aria-hidden="true"></i> All Feedbacks
  </a>
</nav>

<!-- Right Side Menu -->
<div class="menu">
  <h4><?= htmlspecialchars($adminName) ?> <span class="fa fa-bars"></span></h4>
  <div class="dropdown">
    <a href="adminprofile.php">
      <p><span class="fa fa-user"></span> Profile</p>
    </a>
    <a href="settings.php">
      <p><span class="fa fa-cog"></span> Settings</p>
    </a>
    <a href="admin_call_history.php">
      <p><span class="fa fa-phone"></span> Call History</p>
    </a>
    <a href="logout.php">
      <p><span class="fa fa-sign-out-alt"></span> Sign Out</p>
    </a>
  </div>
</div>

<!-- Left Side Menu -->
<div class="menu1">
  <h4><?= htmlspecialchars($adminName) ?> <span class="fa fa-bars"></span></h4>
  <div class="dropdown">
    <a href="admin_messages.php">
      <p><span class="fa fa-comment"></span> Issues</p>
    </a>
    <a href="terms-and-conditions.php">
      <p><span class="fa fa-file-alt"></span> Terms & Conditions</p>
    </a>
    <a href="cancellations-and-refunds.php">
      <p><span class="fa fa-undo"></span> Refunds</p>
    </a>
    <a href="privacy-policy.php">
      <p><span class="fa fa-shield-alt"></span> Privacy Policy</p>
    </a>
  </div>
</div>




<?php
// Connect to your database
$pdo = new PDO("mysql:host=localhost;dbname=water", "root", "R@mu12072004");

// Search logic
$query = isset($_GET['query']) ? $_GET['query'] : '';
$users = [];

if ($query) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE username LIKE ? OR phone LIKE ?");
    $stmt->execute(['%' . $query . '%', '%' . $query . '%']);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
  :root {
    --bg-color: rgb(199, 245, 255);
    --mountain-color: rgb(57, 121, 64);
    --road-color: #333;
    --line-color: #fff;
  }

  .fun {
    background: var(--bg-color);
    overflow: hidden;
    font-family: 'Open Sans', sans-serif;
    height: 250px;
    width: 100%;
    position: relative;
  }

  .sky {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
  }

  .sun {
    width: 80px;
    height: 80px;
    background: #FFD54F;
    border-radius: 50%;
    position: absolute;
    top: 20px;
    left: 290px;
    box-shadow: 0 0 30px #FFD54F;
    animation: sunRotate 20s linear infinite;
  }

  .cloud {
    position: absolute;
    top: 40px;
    width: 120px;
    height: 60px;
    background: #fff;
    border-radius: 60px;
    opacity: 0.8;
       z-index: 2;
 
  }

  .cloud::before,
  .cloud::after {
    content: '';
    position: absolute;
    background: #fff;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    top: -15px;
  }

  .cloud::before { left: 15px; }
  .cloud::after { right: 15px; }

  .cloud1 { left: -150px; animation: cloudAnim 60s linear infinite; }
  .cloud2 { left: -300px; top: 80px; animation: cloudAnim 90s linear infinite; }

  .bird {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    position: absolute;
    top: 100px;
    left: -60px;
    animation: birdFly 25s linear infinite;
       z-index: 2;
    
  }

  .loop-wrapper {
    margin: 0 auto;
    position: relative;
    width: 100%;
    max-width: 900px;
    height: 250px;
    overflow: hidden;
    border-bottom: 3px solid #fff;
    z-index: 1;
  }

  .hill {
    position: absolute;
    right: -900px;
    bottom: -50px;
    width: 400px;
    height: 12px;
    box-shadow:
      0 0 0 50px var(--mountain-color),
      -20px 0 0 20px var(--mountain-color),
      -90px 0 0 50px var(--mountain-color),
      250px 0 0 50px var(--mountain-color),
      290px 0 0 50px var(--mountain-color),
      620px 0 0 50px var(--mountain-color);
    animation: hill 4s 2s linear infinite;
  }

  .mountain {
    position: absolute;
    right: -900px;
    bottom: -20px;
    width: 2px;
    height: 2px;
    box-shadow:
      0 0 0 50px var(--mountain-color),
      60px 50px 0 70px var(--mountain-color),
      90px 90px 0 50px var(--mountain-color),
      250px 250px 0 50px var(--mountain-color),
      290px 320px 0 50px var(--mountain-color),
      320px 400px 0 50px var(--mountain-color);
    transform: rotate(130deg);
    animation: mtn 20s linear infinite;
  }

  .tree, .house {
    position: absolute;
    bottom: 10.2px;
    font-size: 32px;
    animation: treeMove 10s linear infinite;
  }

  .house {
    animation-duration: 15s;
  }

  .truck {
    background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/130015/truck.svg') no-repeat;
    background-size: contain;
    width: 85px;
    height: 60px;
    margin-right: -60px;
    bottom: 10px;
    right: 50%;
    position: absolute;
    animation: truck 4s ease infinite;
    z-index: 3;
    display: flex;

    /* Added watery transparent look */
    opacity: 0.7; /* makes it semi-transparent */
    filter: drop-shadow(0 0 5px rgba(136, 245, 243, 0.6)); /* bluish glow like water reflection */
    mix-blend-mode: screen; /* lightens the truck blending it with water */
    transition: opacity 0.5s ease;
}

/* Optional: on hover or some trigger, change opacity to simulate water splash */
.truck:hover {
    opacity: 1;
    filter: drop-shadow(0 0 10px rgba(174, 237, 255, 0.8));
}
@keyframes truck {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

  .wheels {
    position: absolute;
    bottom: 5px;
    right: calc(42.5% + 10px);
    width: 65px;
    display: flex;
    justify-content: space-between;
    padding: 0 5px;
    z-index: 2;
  }

  .wheels::before,
  .wheels::after {
    content: "";
    width: 15px;
    height: 15px;
    background: #222;
    border-radius: 50%;
    display:flex;
  }

  .road {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 10px;
    background: var(--road-color);
    z-index: 2;
    overflow: hidden;
  }

  .road::before {
    content: "";
    display: block;
    width: 200%;
    height: 4px;
    background: repeating-linear-gradient(
      to right,
      var(--line-color),
      var(--line-color) 20px,
      transparent 20px,
      transparent 40px
    );
    animation: roadAnim 1s linear infinite;
  }

  @keyframes truck {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-2px); }
  }
   @keyframes wheels{
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-2px); }
  }

  @keyframes mtn {
    100% {
      transform: translateX(-2000px) rotate(130deg);
    }
  }

  @keyframes hill {
    100% {
      transform: translateX(-2000px);
    }
  }

  @keyframes sunRotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  @keyframes cloudAnim {
    100% { transform: translateX(1500px); }
  }

  @keyframes birdFly {
    100% { transform: translateX(1600px) translateY(-40px); }
  }

  @keyframes roadAnim {
    0% { transform: translateX(0); }
    100% { transform: translateX(-40px); }
  }

  @keyframes treeMove {
    0% { transform: translateX(0); }
    100% { transform: translateX(-2000px); }
  }
  .board {
  position: absolute;
  bottom: 12.5px; /* adjust as needed */
  background-color: #111; /* blackboard background */
  color: #fff; /* white chalk-like text */
  border: 2px solid #555;
  border-radius: 8px;
  padding: 10px 20px;
  font-family: 'Courier New', monospace; /* chalk-like font */
  font-size: 14px;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
  animation: boardMove 12s linear infinite;
}

@keyframes boardMove {
  0% { transform: translateX(0); }
  100% { transform: translateX(-2000px); }
}
.bird {
  background: url("bird.png") no-repeat center;
  background-size: contain;
  width: 50px; /* smaller width */
  height: 50px; /* smaller height */
  position: absolute;
  top: 30%;
  left: -60px;
  animation: fly 20s linear infinite, flap 1s steps(2) infinite; /* slower speed */
  z-index: 5;
}

/* Animation for flying across the screen */
@keyframes fly {
  0% {
    left: -60px;
    transform: translateY(0) rotate(0deg);
  }
  20% {
    transform: translateY(-20px) rotate(-5deg);
  }
  100% {
    left: 100vw;
    transform: translateY(0) rotate(0deg);
  }
}

/* Optional: mimic flapping with slight scale pulse */
@keyframes flap {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}


</style>

<div class="fun">
  <div class="sky">
    <div class="sun"></div>
    <div class="cloud cloud1"></div>
    <div class="cloud cloud2"></div>
<div class="bird"></div>



  </div>

  <div class="loop-wrapper">
    <div class="mountain"></div>
    <div class="hill"></div>
        <div class="tree" style="left: 40%;">🌲</div>
    <div class="tree" style="left: 80%;">🌳</div>
    <div class="tree" style="left: 100%;">🌴</div>
        <div class="tree" style="left: 10%;">🌴</div>
    <div class="house" style="left: 90%;">🏠</div>
     <div class="house" style="left: 50%;">🏢</div>
       <div class="house" style="left: 20%;">🏭</div>
         <div class="house" style="left: 70%;">🏥</div>
         <div class="board" style="left: 70%;">
  <div>SETA rama raw water plant</div>
</div>

    <div class="truck"></div>
    <div class="wheels"></div>
    <div class="road"></div>
  </div>
</div>

<br>
<div class="call-container">
  <h2>📞 Admin Panel</h2>
  <p id="adminStatus" class="status-text">Waiting for call...</p>

  <!-- Search Form -->
  <div class="section fade-in">
    <h3>🔍 Search Users</h3>
    <form method="get" action="admin_home.php" class="search-form">
      <input type="text" name="query" placeholder="Search by Username or Phone" value="<?= htmlspecialchars($query) ?>">
      <button type="submit" class="btn">Search</button>
    </form>

    <ul id="userList">
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
          <li onclick="callUser('<?= $user['id'] ?>')" class="slide-in">
            <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['phone']) ?>
          </li>
        <?php endforeach; ?>
      <?php elseif ($query): ?>
        <li class="fade-in">No users found.</li>
      <?php endif; ?>
    </ul>
  </div>

  <!-- Incoming Call UI -->
  <div id="incomingCallDiv" class="incoming-call hidden bounce-in">
    <p>📞 Incoming Call from User...</p>
    <button id="acceptBtn" class="btn">✅ Accept</button>
    <button id="declineBtn" class="btn danger">❌ Decline</button>
  </div>

  <!-- Outgoing Call UI -->
  <div id="outgoingCallDiv" class="outgoing-call hidden fade-in">
    <p>📞 Calling User...</p>
    <button id="hangUpOutgoingBtn" class="btn danger">❌ Cancel</button>
  </div>

  <!-- Call Controls -->
  <div id="callControls" class="hidden slide-up">
    <button id="hangUpBtn" class="btn danger">❌ Hang Up</button>
    <button id="muteBtn" class="btn">🔇 Mute</button>
    <p id="callTime" class="status-text">00:00</p>
  </div>

  <audio id="remoteAudio" autoplay></audio>
</div>

<style>

.call-container {
  max-width: 600px;
  margin: auto;
  background: #ffffff;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  text-align: center;
  animation: fadeIn 0.8s ease-in-out;
}

h2 {
  color: #333;
  margin-bottom: 10px;
}

.status-text {
  font-size: 16px;
  color: #555;
  margin-bottom: 20px;
}

.section {
  margin-top: 30px;
}

.search-form {
  display: flex;
  justify-content: center;
  margin-bottom: 15px;
}

.search-form input {
  padding: 10px;
  font-size: 14px;
  width: 60%;
  border-radius: 8px 0 0 8px;
  border: 1px solid #ccc;
  outline: none;
}

.search-form button {
  padding: 10px 20px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 0 8px 8px 0;
  cursor: pointer;
}

.search-form button:hover {
  background-color: #2980b9;
}

#userList {
  list-style: none;
  padding: 0;
  margin: 0;
}

#userList li {
  background-color: #ecf0f1;
  padding: 10px;
  margin: 8px 0;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.3s ease;
  animation: slideIn 0.3s ease;
}

#userList li:hover {
  background-color: #d0dfe5;
}

/* Call Popups */
.incoming-call,
.outgoing-call {
  padding: 20px;
  margin: 20px auto;
  border-radius: 12px;
  background: #fef9e7;
  border: 2px dashed #f1c40f;
  width: 80%;
  animation: bounceIn 0.6s ease;
}

#callControls {
  margin-top: 20px;
  animation: slideUp 0.5s ease;
}

.hidden {
  display: none;
}

/* Buttons */
.btn {
  padding: 10px 20px;
  margin: 5px;
  background-color: #27ae60;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease, transform 0.2s ease;
}

.btn:hover {
  background-color: #219150;
  transform: scale(1.05);
}

.btn.danger {
  background-color: #e74c3c;
}

.btn.danger:hover {
  background-color: #c0392b;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(-30px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes bounceIn {
  0% { transform: scale(0.5); opacity: 0; }
  60% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(1); }
}

@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}


</style>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
const socket = io("http://localhost:3000");
socket.emit("register", "admin");

let pc = null, localStream = null, remoteUserId = null, callTimer = null, callDuration = 0;
let isMuted = false;

function createPeer(target) {
  const peer = new RTCPeerConnection({
    iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
  });

  peer.ontrack = (event) => {
    document.getElementById("remoteAudio").srcObject = event.streams[0];
  };

  peer.onicecandidate = (event) => {
    if (event.candidate && target) {
      socket.emit("ice-candidate", {
        candidate: event.candidate,
        to: target
      });
    }
  };

  return peer;
}

function startTimer() {
  callDuration = 0;
  callTimer = setInterval(() => {
    callDuration++;
    const minutes = Math.floor(callDuration / 60);
    const seconds = callDuration % 60;
    document.getElementById("callTime").innerText = `Call Time: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
  }, 1000);
}

function endCall() {
  pc?.close();
  localStream?.getTracks().forEach(track => track.stop());
  clearInterval(callTimer);

  pc = null;
  localStream = null;
  remoteUserId = null;
  callTimer = null;
  callDuration = 0;
  isMuted = false;

  document.getElementById("callControls").classList.add("hidden");
  document.getElementById("outgoingCallDiv").classList.add("hidden");
  document.getElementById("incomingCallDiv").classList.add("hidden");
  document.getElementById("callTime").innerText = "";
  document.getElementById("muteBtn").innerText = "🔇 Mute";
}

// Incoming call handler
socket.on("incoming-call", async ({ offer, userId }) => {
  remoteUserId = userId;
  document.getElementById("incomingCallDiv").classList.remove("hidden");

  document.getElementById("acceptBtn").onclick = async () => {
    try {
      document.getElementById("incomingCallDiv").classList.add("hidden");

      localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      pc = createPeer(userId);
      localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

      await pc.setRemoteDescription(new RTCSessionDescription(offer));
      const answer = await pc.createAnswer();
      await pc.setLocalDescription(answer);

      socket.emit("answer-user", { answer, userId });

      document.getElementById("callControls").classList.remove("hidden");
      startTimer();
    } catch (err) {
      console.error("Error accepting call:", err);
      endCall();
    }
  };

  document.getElementById("declineBtn").onclick = () => {
    socket.emit("hang-up", { to: userId });
    document.getElementById("incomingCallDiv").classList.add("hidden");
  };
});

// ICE handling
socket.on("ice-candidate", async ({ candidate }) => {
  try {
    if (candidate) await pc?.addIceCandidate(new RTCIceCandidate(candidate));
  } catch (err) {
    console.error("ICE Candidate Error:", err);
  }
});
// Outgoing call
function callUser(userId) {
  remoteUserId = userId;
  document.getElementById("outgoingCallDiv").classList.remove("hidden");

  navigator.mediaDevices.getUserMedia({ audio: true }).then(async stream => {
    localStream = stream;
    pc = createPeer(userId);
    localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);

    socket.emit("call-user", { offer, userId });

    document.getElementById("callControls").classList.remove("hidden");
    startTimer();
  }).catch(err => {
    console.error("Error starting outgoing call:", err);
    endCall();
  });
}

// Handle answer
socket.on("call-answered", async ({ answer }) => {
  try {
    await pc.setRemoteDescription(new RTCSessionDescription(answer));
  } catch (err) {
    console.error("Error setting remote description:", err);
  }
});

// Hang up
document.getElementById("hangUpBtn").onclick = () => {
  if (remoteUserId) socket.emit("hang-up", { to: remoteUserId });
  endCall();
};

document.getElementById("hangUpOutgoingBtn").onclick = () => {
  if (remoteUserId) socket.emit("hang-up", { to: remoteUserId });
  endCall();
};

// Mute toggle
document.getElementById("muteBtn").onclick = () => {
  if (!localStream) return;
  isMuted = !isMuted;
  localStream.getAudioTracks().forEach(track => track.enabled = !isMuted);
  document.getElementById("muteBtn").innerText = isMuted ? "🔊 Unmute" : "🔇 Mute";
};

// Remote hang-up
socket.on("call-ended", () => {
  endCall();
});
</script>





<h1>Today's and Tomorrow's Orders</h1>

<table>
  <thead>
    <tr>
      <th>Customer</th>
      <th>Phone</th>
      <th>Address</th>
      <th>Delivery Time</th>
      <th>Item Name</th>
      <th>Quantity</th>
      <th>Item Price</th>
      <th>Total Price</th>
      <th>Order Date</th>
      <th>Status</th>
      <th>Delivery Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($orders)): ?>
      <tr><td colspan="12">No orders found.</td></tr>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= htmlspecialchars($order['customer_name']) ?></td>
          <td><?= htmlspecialchars($order['phone']) ?></td>
          <td><?= htmlspecialchars($order['address']) ?></td>
          <td><?= htmlspecialchars($order['delivery_time'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($order['item_name'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($order['quantity'] ?? 0) ?></td>
          <td>₹<?= number_format($order['item_price'] ?? 0, 2) ?></td>
          <td>₹<?= number_format($order['total_price'] ?? 0, 2) ?></td>
          <td><?= htmlspecialchars($order['order_date']) ?></td>
          <td><?= htmlspecialchars($order['status']) ?></td>
          <td><?= htmlspecialchars($order['delivery_status']) ?></td>
          <td>
            <?php if ($order['status'] === 'pending'): ?>
              <form action="update_status.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" class="accept-btn">Accept</button>
              </form>
            <?php else: ?>
              <form action="all_orders.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" class="update-btn">Update Delivery Status</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<br>
<br>
<?php
// db.php - include your database connection file
include 'db.php';

// Today's Order Stats
$today = date('Y-m-d');
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = '$today'")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND DATE(order_date) = '$today'")->fetchColumn();
$completed_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completed' AND DATE(order_date) = '$today'")->fetchColumn();
$total_earnings = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'completed' AND DATE(order_date) = '$today'")->fetchColumn();
?>

<!-- HTML Dashboard Stats Section -->
<div class="dashboard-stats">
    <div>Total Orders Today: <?= $total_orders ?></div>
    <div>Pending Orders: <?= $pending_orders ?></div>
    <div>Completed Orders: <?= $completed_orders ?></div>
    <div>Total Earnings: ₹<?= $total_earnings ?></div>
</div>

<!-- Add this above the closing </body> tag -->

<style>
.order-container {
    display: none; /* Hidden by default */
    margin: 20px auto;
    width: 90%;
    max-width: 600px;
    background: #fdfdfd;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
    font-family: Arial, sans-serif;
}

.order-container p {
    padding: 8px 0;
    border-bottom: 1px dashed #ccc;
    font-size: 16px;
}

.toggle-btn {
    display: block;
    margin: 20px auto;
    padding: 10px 20px;
    font-size: 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.toggle-btn:hover {
    background-color: #0056b3;
}
</style>





<!-- Bulk Status Update -->
<form method="POST" action="bulk_update.php">
<table>
<tr><th>Select</th><th>Order ID</th><th>Status</th></tr>
<?php
$orders = $pdo->query("SELECT * FROM orders WHERE status='pending'");
foreach ($orders as $order) {
    echo "<tr>
            <td><input type='checkbox' name='order_ids[]' value='{$order['id']}'></td>
            <td>{$order['id']}</td>
            <td>{$order['status']}</td>
          </tr>";
}
?>
</table>
<button type="submit">Mark as Delivered</button>
</form>

<!-- bulk_update.php -->
<?php
// Handle bulk update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_ids'])) {
    $ids = implode(",", array_map('intval', $_POST['order_ids']));
    $pdo->query("UPDATE orders SET status='completed' WHERE id IN ($ids)");
    header("Location: admin_home.php");
    exit();
}
?>
<?php if (isset($_SESSION['success'])): ?>
    <div style="color: green; font-weight: bold;"><?= $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div style="color: red; font-weight: bold;"><?= $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>


<div style="width: 90%; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
  <!-- Advertisement -->
  <h2>Admin Controls</h2>
  <form action="add_advertisement.php" method="POST" enctype="multipart/form-data">
    <h3>Add Advertisement</h3>
    <input type="text" name="ad_text" placeholder="Advertisement Text" required style="width:80%;"><br><br>
    <label for="media">Upload Images/Videos (1 to 5 files):</label><br>
    <input type="file" name="media_files[]" id="media" accept="image/*,video/*" multiple required onchange="validateFiles(this.files)" style="margin-top:8px;"><br><br>
    <button type="submit" class="update-btn">Add Advertisement</button>
  </form>

  <h2>Existing Advertisements</h2>
<table>
  <thead>
    <tr>
      <th>Text</th>
      <th>Media</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $ads = $pdo->query("SELECT * FROM advertisements ORDER BY created_at DESC")->fetchAll();
    foreach ($ads as $ad):
    ?>
    <tr>
      <td><?= htmlspecialchars($ad['ad_text']) ?></td>
      <td>
        <?php
        $media_files = explode(',', $ad['media_path']);
        foreach ($media_files as $file):
          if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)):
        ?>
            <img src="<?= $file ?>" alt="Ad Media" style="width:100px;">
        <?php else: ?>
            <video width="150" controls>
              <source src="<?= $file ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
        <?php
          endif;
        endforeach;
        ?>
      </td>
      <td>
        <a href="delete_ad.php?id=<?= $ad['id'] ?>" onclick="return confirm('Are you sure you want to delete this advertisement?');">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
  
  <!-- Sale Offer -->
  <form action="add_sale.php" method="POST" style="margin-top:30px;">
    <h3>Create Sale Offer</h3>
    <input type="text" name="sale_description" placeholder="Sale Description" required>
    <input type="number" name="discount_percent" placeholder="Discount (%)" required>
    <button type="submit" class="update-btn">Add Sale</button>
  </form>


  <h2>All Sale Offers</h2>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Discount (%)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?= $sale['id'] ?></td>
                <td><?= htmlspecialchars($sale['description']) ?></td>
                <td><?= htmlspecialchars($sale['discount_percent']) ?></td>
                <td>
                    <form action="delete_sale.php" method="POST" onsubmit="return confirm('Delete sale?');">
                        <input type="hidden" name="id" value="<?= $sale['id'] ?>">
                        <button type="submit" style="background:red;color:white;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
  <!-- Coupon Code -->
  <form action="add_coupon.php" method="POST" style="margin-top:30px;">
    <h3>Create Coupon Code</h3>
    <input type="text" name="code" placeholder="Coupon Code" required>
    <input type="number" name="value" placeholder="Value ₹" required>
    <button type="submit" class="accept-btn">Add Coupon</button>
  </form>
</div>




<h2>All Coupons</h2>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Value (%)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($coupons as $coupon): ?>
            <tr>
                <td><?= $coupon['id'] ?></td>
                <td><?= htmlspecialchars($coupon['code']) ?></td>
                <td><?= htmlspecialchars($coupon['value']) ?></td>
                <td>
                    <form action="delete_coupon.php" method="POST" onsubmit="return confirm('Delete this coupon?');">
                        <input type="hidden" name="id" value="<?= $coupon['id'] ?>">
                        <button type="submit" style="background:red; color:white;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<h2>Admin Panel - Add New Item</h2>
    <form method="POST">
        <input type="text" name="item_name" placeholder="Item name" required>
        <input type="number" name="price" placeholder="Price" required step="0.01">
        <input type="submit" value="Add Item">
    </form>

    <h2>Item List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['id']) ?></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td>
                <a class="delete-btn" href="?delete=<?= $item['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<script>
  function validateFiles(files) {
    if (files.length < 1 || files.length > 5) {
      alert("Please upload between 1 and 5 files.");
      document.getElementById('media').value = "";
    }
  }
  
</script>




</body>
</html>
