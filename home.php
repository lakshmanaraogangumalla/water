<?php
// home.php - User Dashboard
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'water';
$username = 'root';
$password = 'R@mu12072004';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$customerName = $user['username'] ?? 'user';
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Water App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <style>
        /* Reset and base styles */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to right, #e0f2f1, #b2ebf2);
    overflow-x: hidden;
}

nav {
    background:rgb(61, 195, 248);
    padding: 20px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: fadeSlideDown 0.5s ease-in-out;
}

nav a {
    text-decoration: none;
    color: white;
    font-weight: bold;
    font-size: 16px;
    transition: color 0.3s ease;
}

nav a:hover {
    color:rgb(118, 161, 151);
}
.menu {
    position: absolute;
    top: 10px;
    right: 20px;
    color: white;
    text-align: right;
}

.menu .dropdown {
    display: none;
    position: absolute;
    top: 45px;
    right: 0;
    padding: 30px;
    background: linear-gradient(135deg, #00838f, #4dd0e1);
    border-radius: 12px;
    color: #ffffff;
    opacity: 0;
    transform: translateY(-15px) scale(0.95);
    animation: dropdownFadeIn 0.4s ease-out forwards;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

.menu:hover .dropdown {
    display: block;
    color: #e0f7fa;
}

@keyframes dropdownFadeIn {
    0% {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
.hero-banner {
    background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.4)), url('banner.jpg') center/cover no-repeat;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0 0 20px 20px;
    animation: slideDownFade 1s ease-out;
}

.hero-text {
    color: #ffffff;
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
    text-align: center;
    position: relative;
    animation: blinkText 2s infinite, waveFlow 4s linear infinite, burnText 1.5s ease-in-out infinite alternate;
    background: linear-gradient(90deg, #00cfff, #ffffff, #ff4500);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

@keyframes slideDownFade {
    0% {
        transform: translateY(-30px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes blinkText {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

@keyframes waveFlow {
    0% {
        background-position: 0% 50%;
    }
    100% {
        background-position: 200% 50%;
    }
}

@keyframes burnText {
    0% {
        text-shadow: 2px 2px 10px rgba(255,69,0,0.8), 0 0 30px rgba(255,69,0,0.6);
        color: #ff4500;
    }
    50% {
        text-shadow: 2px 2px 20px rgba(255,69,0,0.6), 0 0 40px rgba(255,69,0,0.5);
        color: #ff6347;
    }
    100% {
        text-shadow: 2px 2px 30px rgba(255,69,0,1), 0 0 50px rgba(255,69,0,1);
        color: #ff7f50;
    }
}

.user-greeting {
    margin: 30px auto;
    background: #004d99;
    padding: 25px;
    border-radius: 12px;
    color: #ffffff;
    text-align: center;
    font-size: 1.5rem;
    animation: glowPulse 2.5s ease-in-out infinite alternate;
    box-shadow: 0 0 12px #007fff;
}

.container {
    max-width: 850px;
    margin: 20px auto;
    padding: 25px;
    border-radius: 6px;
    border: 1px; /* Border style with color */
    background-color: white;
    display: flex;
    flex-direction: column; /* Ensures content is centered vertically */
    justify-content: center; /* Centers content vertically */
    align-items: center; /* Centers content horizontally */
}



.order-section{
    max-width: 850px;
    margin: 20px auto;
    padding: 25px;
    border-radius: 6px;
    border: 1px; /* Border style with color */
    background-color: white;
    display: flex;
    flex-direction: column; /* Ensures content is centered vertically */
    justify-content: center; /* Centers content vertically */
    align-items: center; /* Centers content horizontally */
}

.price-table {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.card {
    background: white;
    padding: 20px;
    margin: 10px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeInZoom 0.5s ease-in-out;
    min-width: 220px;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.card h3 {
    color: #00695c;
    margin-bottom: 10px;
}

.quantity {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px 0;
}

.quantity button {
    padding: 5px 10px;
    font-size: 18px;
    cursor: pointer;
    border: none;
    background: #00acc1;
    color: white;
    border-radius: 5px;
    transition: background 0.3s;
}

.quantity button:hover {
    background: #007c91;
}

.quantity input {
    width: 50px;
    text-align: center;
    font-size: 16px;
    margin: 0 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.add-to-cart {
    background-color: #00796b;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.add-to-cart:hover {
    background-color: #004d40;
}

.cart-link {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #00bcd4;
    font-weight: bold;
}

.order-link {
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.order-link p {
    margin: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
}

.order-link .fa-bag {
    margin-left: 10px;
}

.order-link:hover {
    background-color: #f0f0f0;
}

        
    .hero { background: url('banner.jpg') no-repeat center center / cover; height: 300px; display: flex; align-items: center; justify-content: center; color:#00bcd4 ; font-size: 2rem; font-weight: bold; }
    .greeting { padding: 20px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px; border-radius: 10px;display: flex; align-items: center; justify-content: center;background-color:#006064;color:white;}
    .categories, .benefits { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 20px; }
    .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
    .footer { background-color:rgb(31, 99, 154); color: #ddd; padding: 40px 20px; display: flex; justify-content: space-around; flex-wrap: wrap; }
    .footer div { margin: 10px; }
    .footer a { color: #ddd; text-decoration: none; display: block; margin: 5px 0; }
    .card {
  background: white;
  padding: 20px;
  margin: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  text-align: center;
  transition: transform 0.3s ease;
}

.card:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.card i {
  color: #007bff;
  margin-bottom: 10px;
}


.carousel-container{position:relative;width:90%;max-width:800px;margin:20px auto;overflow:hidden;border-radius:12px;background:#f9f9f9;box-shadow:0 8px 16px rgba(0,0,0,0.1);}
.carousel{display:flex;transition:transform 0.6s ease-in-out;width:100%;}
.carousel-slide{min-width:100%;box-sizing:border-box;text-align:center;display:none;padding:20px;}
.carousel-slide.active{display:block;}
.carousel-slide img,.carousel-slide video{max-width:100%;border-radius:8px;margin-top:10px;}
.ad-text{font-size:1.2rem;font-weight:bold;color:#333;}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:white;border:none;padding:10px;font-size:24px;cursor:pointer;border-radius:50%;z-index:1;}
.carousel-btn.prev{left:10px;}
.carousel-btn.next{right:10px;}
.dots{text-align:center;padding:10px 0;}
.dots .dot{display:inline-block;width:10px;height:10px;background:#bbb;border-radius:50%;margin:0 5px;cursor:pointer;}
.dots .dot.active{background:#333;}
 

.sales-container {
    background-color: #ffffff;
    border-radius: 12px;
    margin: 20px auto;
    max-width: 1000px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
    animation: fadeSlideUp 0.8s ease-out;
    padding: 30px;
    text-align: center;
}

.items-wrapper {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.sale, .coupon {
    min-width: 250px;
    padding: 20px;
    border-radius: 12px;
    background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
    transform: scale(1);
    animation: zoomInOut 2s ease-in-out infinite alternate;
    transition: transform 0.4s ease;
    color: #333;
    font-weight: bold;
}

/* Sales */
.sale {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
}

/* Coupons */
.coupon {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
}

/* Hover zoom */
.ad:hover, .sale:hover, .coupon:hover {
    transform: scale(1.08);
    z-index: 2;
}

/* Entry animation */
@keyframes fadeSlideUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Zoom in-out loop animation */
@keyframes zoomInOut {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(1.03);
    }
}

 
.animated-heading {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    animation: colorPulse 5s infinite linear;
}

@keyframes colorPulse {
    0%   { color: #00796b; }  /* Teal */
    25%  { color: #009688; }  /* Slightly lighter teal */
    50%  { color: #4db6ac; }  /* Aqua */
    75%  { color: #26a69a; }  /* Sea green */
    100% { color: #00796b; }  /* Back to teal */
}

    </style>
</head>
<body>
    

    <!-- Navigation Bar -->
    <nav>
        <a href="home.php"><p>Home <span class="fa fa-home"></span></p></a>
        <a href="help.php"><p>Help <span class="fa fa-robot"></span></p></a>
        <a href="cart.php"><p>Cart <span class="fa fa-shopping-cart"></span></p></a>
        <a href="userfeedback.php"><p>Feedback <span class="fa fa-comment-dots"></span></p></a>
        <a href="your_orders.php" class="order-link"><p>My Orders <span class="fa fa-bag-shopping"></span></p></a>
    </nav>

    <!-- Menu with Dropdown -->
    <div class="menu">
        <h1><span class="fa fa-bars"></span></h1>
        <div class="dropdown">
            <a href="profile.php"><p>Profile <span class="fa fa-user"></span></p></a>
            <a href="settings.php"><p>Settings <span class="fa fa-cog"></span></p></a>
            <a href="logout.php"><p>Sign Out <span class="fa fa-sign-out-alt"></span></p></a>
        </div>
    </div>
    

<!-- USER CALL PANEL -->
<div class="call-container">
<h2>User Panel</h2>
<button id="callBtn" class="btn">📞 Call Admin</button>
<p id="callStatus" class="status-text">Not in a call</p>

<!-- Incoming Call UI -->
<div id="incomingCallDiv" class="incoming-call">
<p>📞 Incoming Call from Admin</p>
<button id="answerBtn" class="btn">✅ Answer</button>
<button id="rejectBtn" class="btn danger">❌ Reject</button>
</div>

<!-- Outgoing Call UI -->
<div id="outgoingCallDiv" class="outgoing-call">
<p>📞 Calling Admin...</p>
<button id="hangUpOutgoingBtn" class="btn danger">❌ Hang Up</button>
</div>

<!-- Active Call Controls -->
<div id="callControls" style="display: none;">
<button id="hangUpBtn" class="btn danger">❌ Hang Up</button>
<button id="muteBtn" class="btn">🔇 Mute</button>
<p id="callTime" class="status-text"></p>
</div>

<audio id="remoteAudio" autoplay style="display: none;"></audio>
<audio id="ringtone" src="ringtone.mp3" loop></audio>
</div>

<!-- Styles -->
<style>
.call-container {
width: 90%;
max-width: 500px;
margin: 0 auto;
padding: 20px;
background: #fff;
border-radius: 10px;
text-align: center;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.btn {
padding: 10px 16px;
font-size: 16px;
margin: 5px;
border: none;
border-radius: 8px;
background-color: #4caf50;
color: white;
cursor: pointer;
}
.btn:hover { background-color: #45a049; }
.btn.danger { background-color: #f44336; }
.btn.danger:hover { background-color: #d32f2f; }
.outgoing-call, .incoming-call {
background: #ffcccc;
padding: 10px;
border-radius: 8px;
margin-top: 10px;
display: none;
}
.status-text {
font-weight: bold;
font-size: 14px;
margin-top: 10px;
}
</style>

<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
const socket = io("http://localhost:3000");
socket.emit("register", "user");

let pc = null;
let localStream = null;
let callTimer = null;
let callDuration = 0;
let unansweredTimer = null;
let remoteId = null;

// ICE config
function createPeerConnection(targetId) {
    const peer = new RTCPeerConnection({
        iceServers: [{ urls: "stun:stun.l.google.com:19302" }]
    });

    peer.onicecandidate = (e) => {
        if (e.candidate) {
            socket.emit("ice-candidate", {
                candidate: e.candidate,
                to: targetId
            });
        }
    };

    peer.ontrack = (e) => {
        document.getElementById("remoteAudio").srcObject = e.streams[0];
    };

    return peer;
}

// Call Admin
document.getElementById("callBtn").onclick = async () => {
    document.getElementById("callStatus").innerText = "Calling Admin...";
    document.getElementById("outgoingCallDiv").style.display = "block";

    localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
    pc = createPeerConnection("admin");
    localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);

    socket.emit("call-admin", {
        offer,
        userId: socket.id
    });

    unansweredTimer = setTimeout(() => {
        if (!pc.remoteDescription) {
            endCall();
            document.getElementById("callStatus").innerText = "Call Unanswered";
            socket.emit("hang-up");
        }
    }, 30000);
};

// Incoming Call
socket.on("incoming-call", async ({ from, offer }) => {
    remoteId = from;
    window.incomingCall = { from, offer };
    document.getElementById("incomingCallDiv").style.display = "block";
    document.getElementById("callStatus").innerText = "Incoming call...";
    document.getElementById("ringtone").play();
});

// Answer Call
document.getElementById("answerBtn").onclick = async () => {
    const { from, offer } = window.incomingCall;
    document.getElementById("incomingCallDiv").style.display = "none";
    document.getElementById("ringtone").pause();

    localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
    pc = createPeerConnection(from);
    localStream.getTracks().forEach(track => pc.addTrack(track, localStream));

    await pc.setRemoteDescription(new RTCSessionDescription(offer));
    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);

    socket.emit("answer-call", { answer, to: from });
    document.getElementById("callStatus").innerText = "Call Connected";
    showControls();
    startTimer();
};

// Reject Call
document.getElementById("rejectBtn").onclick = () => {
    socket.emit("reject-call", { to: window.incomingCall.from });
    document.getElementById("incomingCallDiv").style.display = "none";
    document.getElementById("ringtone").pause();
    document.getElementById("callStatus").innerText = "Call Rejected";
};

// Call Answered
socket.on("call-answered", async ({ answer, from }) => {
    clearTimeout(unansweredTimer);
    remoteId = from;
    await pc.setRemoteDescription(new RTCSessionDescription(answer));
    document.getElementById("callStatus").innerText = "Call Connected";
    document.getElementById("outgoingCallDiv").style.display = "none";
    showControls();
    startTimer();
});

// Call Rejected
socket.on("call-rejected", () => {
    clearTimeout(unansweredTimer);
    endCall();
    document.getElementById("callStatus").innerText = "Call Rejected by Admin";
});

// Hang Up
document.getElementById("hangUpBtn").onclick =
document.getElementById("hangUpOutgoingBtn").onclick = () => {
    socket.emit("hang-up", { to: remoteId });
    endCall();
};

socket.on("call-ended", () => {
    endCall();
});

// Handle ICE Candidates
socket.on("ice-candidate", async ({ candidate }) => {
    try {
        await pc?.addIceCandidate(new RTCIceCandidate(candidate));
    } catch (e) {
        console.error("Error adding ICE candidate:", e);
    }
});

// Mute / Unmute
document.getElementById("muteBtn").onclick = () => {
    const track = localStream?.getAudioTracks()[0];
    if (track) {
        track.enabled = !track.enabled;
        document.getElementById("callStatus").innerText = track.enabled ? "Unmuted" : "Muted";
    }
};

// UI Helpers
function showControls() {
    document.getElementById("callControls").style.display = "block";
}

function startTimer() {
    callDuration = 0;
    callTimer = setInterval(() => {
        callDuration++;
        const m = Math.floor(callDuration / 60);
        const s = callDuration % 60;
        document.getElementById("callTime").innerText = `Call Time: ${m}:${s < 10 ? '0' : ''}${s}`;
    }, 1000);
}

function endCall() {
    pc?.close();
    localStream?.getTracks().forEach(track => track.stop());
    pc = null;
    localStream = null;
    remoteId = null;
    clearInterval(callTimer);
    clearTimeout(unansweredTimer);

    document.getElementById("callControls").style.display = "none";
    document.getElementById("outgoingCallDiv").style.display = "none";
    document.getElementById("incomingCallDiv").style.display = "none";
    document.getElementById("callStatus").innerText = "Call Ended";
    document.getElementById("callTime").innerText = "";
    document.getElementById("ringtone").pause();
}
</script>











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

</style>

<div class="fun">
  <div class="sky">
    <div class="sun"></div>
    <div class="cloud cloud1"></div>
    <div class="cloud cloud2"></div>
<div class="bird"><img src="bird.png" alt="Bird" style="width:30px; height:30px;" /></div>



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



    
    <div class="container">
        <div class="support">
            <h2 style="color:#00796b; text-align: center;">Support</h2>
            <p><strong>Shop Name:</strong> Seta Rama Water Plant</p>
            <p><strong>Phone No:</strong> +91-9849672368</p>
            <p><strong>Address:</strong> China Korumilli Main Road near by Seat Rama Kapula Kalayana Mandapam</p>
            <p><strong>Timings:</strong> 6:00 AM to 10:00 PM</p>
            <p><strong>Current Date:</strong> <?= $currentDate ?></p>
            <p><strong>Current Time:</strong> <?= $currentTime ?></p>
        </div>
</div>

<h3 class="animated-heading">🔥 Today's Offers</h3>
<div class="carousel-container">
<div class="carousel" id="adCarousel">
<?php
$ads = $pdo->query("SELECT * FROM advertisements ORDER BY id DESC LIMIT 5")->fetchAll();
foreach ($ads as $index => $ad) {
$media_files = explode(',', $ad['media_path']);
?>
<div class="carousel-slide<?= $index === 0 ? ' active' : '' ?>">
<p class="ad-text">📢 <?= htmlspecialchars($ad['ad_text']) ?></p>
<?php foreach ($media_files as $file): ?>
<?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)): ?>
<img src="<?= htmlspecialchars($file) ?>" alt="Ad Image">
<?php elseif (preg_match('/\.(mp4|avi)$/i', $file)): ?>
<video controls><source src="<?= htmlspecialchars($file) ?>" type="video/mp4">Your browser does not support the video tag.</video>
<?php endif; ?>
<?php endforeach; ?>
</div>
<?php } ?>
</div>
<button class="carousel-btn prev" onclick="changeSlide(-1)">⟵</button>
<button class="carousel-btn next" onclick="changeSlide(1)">⟶</button>
<div class="dots" id="dotsContainer"></div>
</div>

<script>
let currentIndex=0;
const slides=document.querySelectorAll('.carousel-slide');
const dotsContainer=document.getElementById('dotsContainer');

function showSlide(index){
slides.forEach((slide,i)=>{slide.classList.toggle('active',i===index);});
const dots=document.querySelectorAll('.dot');
dots.forEach((dot,i)=>{dot.classList.toggle('active',i===index);});
currentIndex=index;
}

function changeSlide(direction){
let newIndex=currentIndex+direction;
if(newIndex<0)newIndex=slides.length-1;
if(newIndex>=slides.length)newIndex=0;
showSlide(newIndex);
resetAutoSlide();
}

function createDots(){
slides.forEach((_,i)=>{
const dot=document.createElement('span');
dot.classList.add('dot');
if(i===0)dot.classList.add('active');
dot.onclick=()=>{showSlide(i);resetAutoSlide();};
dotsContainer.appendChild(dot);
});
}

let autoSlide=setInterval(()=>changeSlide(1),5000);
function resetAutoSlide(){
clearInterval(autoSlide);
autoSlide=setInterval(()=>changeSlide(1),5000);
}
createDots();
</script>




        <div class="order-section">
            <h2 style="color:#00796b; text-align: center;">Water Pricing</h2>
            <div class="price-table">
                <div class="card">
                    <h3>Cooling Water</h3>
                    <p>₹40 each</p>
                    <form action="cart.php" method="post">
                        <div class="quantity">
                            <button type="button" onclick="changeQty('qty1', -1)">-</button>
                            <input type="number" name="quantity" id="qty1" value="0" min="0">
                            <button type="button" onclick="changeQty('qty1', 1)">+</button>
                        </div>
                        <input type="hidden" name="item" value="Cooling Water">
                        <input type="hidden" name="price" value="40">
                        <button class="add-to-cart" type="submit">Add to Cart</button>
                    </form>
                </div>
                <div class="card">
                    <h3>Normal Water</h3>
                    <p>₹15 each</p>
                    <form action="cart.php" method="post">
                        <div class="quantity">
                            <button type="button" onclick="changeQty('qty2', -1)">-</button>
                            <input type="number" name="quantity" id="qty2" value="0" min="0">
                            <button type="button" onclick="changeQty('qty2', 1)">+</button>
                        </div>
                        <input type="hidden" name="item" value="Normal Water">
                        <input type="hidden" name="price" value="15">
                        <button class="add-to-cart" type="submit">Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php
        // Fetch items from the database
        $stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through items and display them dynamically
        foreach ($items as $index => $item):
        ?>
            <div class="card">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p>₹<?= number_format($item['price'], 2) ?> each</p>
                <form action="cart.php" method="post">
                    <div class="quantity">
                        <button type="button" onclick="changeQty('qty<?= $index ?>', -1)">-</button>
                        <input type="number" name="quantity" id="qty<?= $index ?>" value="0" min="0">
                        <button type="button" onclick="changeQty('qty<?= $index ?>', 1)">+</button>
                    </div>
                    <input type="hidden" name="item" value="<?= htmlspecialchars($item['name']) ?>">
                    <input type="hidden" name="price" value="<?= $item['price'] ?>">
                    <button class="add-to-cart" type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
        
<style>
    .price-table { display: flex; flex-wrap: wrap; gap: 20px; padding: 20px; }
    .card {
        width: 250px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .quantity { display: flex; align-items: center; gap: 5px; margin: 10px 0; }
    .quantity input { width: 50px; text-align: center; }
    .quantity button { padding: 5px 10px; }
    .add-to-cart {
        background-color: #28a745; color: white;
        padding: 10px; border: none; border-radius: 5px;
    }
    .add-to-cart:hover { background-color: #218838; }
</style>

<script>
    function changeQty(id, delta) {
        const input = document.getElementById(id);
        let value = parseInt(input.value) || 0;
        value += delta;
        if (value < 0) value = 0;
        input.value = value;
    }
</script>



            <a href="cart.php" class="cart-link">View Cart</a>
        </div>
    </div>
    <script>
        function changeQty(id, delta) {
            const input = document.getElementById(id);
            let qty = parseInt(input.value);
            qty = isNaN(qty) ? 0 : qty;
            qty += delta;
            if (qty < 0) qty = 0;
            input.value = qty;
        }
    </script>
<div class="user-greeting">
    <h2>Hello again, <?= htmlspecialchars($customerName) ?> 🌟</h2>
    <p>📅 <?= $currentDate ?> | ⏰ <?= $currentTime ?></p>
</div>
  

<div class="sales-container">
    <h3 style="color:#00796b;">Sales & Discounts</h3>
    <div class="items-wrapper">
        <?php
        $sales = $pdo->query("SELECT * FROM sales ORDER BY id DESC LIMIT 3")->fetchAll();
        foreach ($sales as $sale) {
        ?>
            <div class="sale">
                <p>🔥 <?= htmlspecialchars($sale['description']) ?> - <?= $sale['discount_percent'] ?>% off</p>
            </div>
        <?php } ?>
    </div>

    <h3 style="color:#00796b; margin-top: 30px;">Available Coupons</h3>
    <div class="items-wrapper">
        <?php
        $coupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC LIMIT 3")->fetchAll();
        foreach ($coupons as $coupon) {
        ?>
            <div class="coupon">
                <p>🎟 Code: <strong><?= htmlspecialchars($coupon['code']) ?></strong> - ₹<?= $coupon['value'] ?> OFF</p>
            </div>
        <?php } ?>
    </div>
</div>



<div class="hero-banner">
    <div class="hero-text">💧 Clean Water, Quick to Your Doorstep</div>
</div>






<section class="categories">
<a href="bottles.php">
  <div class="card">
    <i class="fas fa-bottle-water fa-2x"></i>
    <h3>Bottled Water</h3>
    <p>Order 1L, 2L bottles delivered fast.</p>
  </div>
      </a>
      <a href="cans.php">
  <div class="card">
    <i class="fas fa-glass-martini-alt fa-2x"></i>
    <h3> plastic Water Cans</h3>
    <p>2oLiters - family packs available.</p>
  </div>
      </a>
  <a href="delivery.php">
  <div class="card">
    <i class="fas fa-truck fa-2x"></i>
    <h3>Bulk Delivery</h3>
    <p>Perfect for offices and events.</p>
  </div>
  </a>
  <a href="Accessories.php">
  <div class="card">
    <i class="fas fa-tools fa-2x"></i>
    <h3>Accessories</h3>
    <p>Dispensers, Stands & more.</p>
  </div>
      </a>
</section>

<section class="benefits">
<a href="Original.php">
  <div class="card">
    <i class="fas fa-check-circle fa-2x"></i>
    <h3>100% Original</h3>
    <p>Certified water products.</p>
  </div>
      </a>
      <a href="Return.php">
  <div class="card">
    <i class="fas fa-undo fa-2x"></i>
    <h3>14-Day Return</h3>
    <p>Easy return on damaged items.</p>
  </div>
      </a>
  <a href="delivery.php">
  <div class="card">
    <i class="fas fa-shipping-fast fa-2x"></i>
    <h3>Fast Delivery</h3>
    <p>Same-day delivery available.</p>
  </div>
</a>

</section>

<footer class="footer">
  <div>
    <h4>SHOP</h4>
    <a href="privacy-policy.php">Privacy Policy</a>
    <a href="#">Kids</a>
        <a href="contactus.php">Contact Us</a>
  </div>
  <div>
    <h4>CUSTOMER POLICIES</h4>
    <a href="cancellations-and-refunds.php"> Cancellations and Refunds</a>
    <a href="shipping-policy.php">Shipping Policy</a>
    <a href="terms-and-conditions.php">Terms of Use</a>
  </div>
  <div>
    <h4>ADDRESS</h4>
    <p>SR-Water Pvt Ltd,<br>KOrumilli, India<br>+91-9390198031</p>
  </div>
</footer>

</body>
</html>
