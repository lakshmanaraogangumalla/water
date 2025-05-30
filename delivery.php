<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to view delivery details.";
    exit;
}

$userId = $_SESSION['user_id'];
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Automatically update old delivery statuses
$today = date('Y-m-d');
$pdo->prepare("UPDATE orders SET delivery_status = 'Delivered' WHERE user_id = ? AND delivery_date < ? AND delivery_status != 'Delivered'")
    ->execute([$userId, $today]);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND delivery_date BETWEEN ? AND ? ORDER BY delivery_date DESC");
$stmt->execute([$userId, $start_date, $end_date]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Default coordinates
$userLat = 16.4725;
$userLng = 80.5929;
$adminLat = 17.4305;
$adminLng = 78.4011;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delivery Details & Live Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 960px; margin: auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h2, h3 { color: #007BFF; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #007BFF; color: white; }
        .form-inline { margin: 20px 0; }
        .form-inline input, .form-inline button { padding: 8px; margin-right: 10px; }
        .form-inline button { background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
        #map { width: 100%; height: 400px; margin-top: 20px; border-radius: 10px; }
        #controls { text-align: center; margin: 15px 0; }
        #controls input, #controls button { padding: 10px; font-size: 14px; margin: 5px; border-radius: 6px; border: 1px solid #ccc; }
        .btn { background: #3498db; color: white; border: none; cursor: pointer; }
        #eta { text-align: center; font-size: 16px; font-weight: 500; margin-top: 10px; color: #333; }
        .back-btn { display: inline-block; padding: 10px 15px; background: #e74c3c; color: white; text-decoration: none; border-radius: 8px; margin-bottom: 20px; }
        .back-btn:hover { background: #c0392b; }
    </style>
</head>
<body>
<div class="container">
    <a href="home.php" class="back-btn">← Back to Home</a>
    <h2>Your Delivery Orders</h2>

    <form method="GET" class="form-inline">
        <label>From: <input type="date" name="start_date" value="<?= $start_date ?>"></label>
        <label>To: <input type="date" name="end_date" value="<?= $end_date ?>"></label>
        <button type="submit">Filter</button>
    </form>

    <?php if (count($orders) > 0): ?>
        <table>
            <tr>
                <th>Address</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Track</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['delivery_date']) ?></td>
                    <td><?= htmlspecialchars($order['delivery_time']) ?></td>
                    <td><?= htmlspecialchars($order['delivery_status']) ?></td>
                    <td>
                        <a href="https://www.google.com/maps/dir/<?= $adminLat ?>,<?= $adminLng ?>/<?= urlencode($order['address']) ?>" target="_blank">
                            Track Delivery
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No delivery records found for this date range.</p>
    <?php endif; ?>

    <h3>🚚 Live Delivery Tracking</h3>
    <div id="controls">
        <input id="userInput" type="text" placeholder="Set User Location (city/village)">
        <input id="adminInput" type="text" placeholder="Set Admin Location (optional)">
        <button class="btn" onclick="geocodeLocation()">Update Location</button>
    </div>

    <div id="map"></div>
    <p id="eta">📏 Calculating distance and ETA...</p>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    let map, userMarker, adminMarker, routeLine;
    let userLocation = [<?= $userLat ?>, <?= $userLng ?>];
    let adminLocation = [<?= $adminLat ?>, <?= $adminLng ?>];

    function initMap() {
        map = L.map('map').setView(userLocation, 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        userMarker = L.marker(userLocation).addTo(map).bindPopup("User Location").openPopup();
        adminMarker = L.marker(adminLocation).addTo(map).bindPopup("Admin Location");

        drawRoute(userLocation, adminLocation);
    }

    function drawRoute(start, end) {
        if (routeLine) map.removeLayer(routeLine);
        routeLine = L.polyline([start, end], {
            color: 'blue',
            weight: 4
        }).addTo(map);
        updateEstimatedTime();
    }

    function updateEstimatedTime(speedKmph = 40) {
        const distance = haversineDistance(...userLocation, ...adminLocation);
        const timeHours = distance / speedKmph;
        const minutes = Math.round(timeHours * 60);
        document.getElementById("eta").innerText =
            `📏 Distance: ${distance.toFixed(2)} km | ⏱ ETA: ${minutes} minutes (at ${speedKmph} km/h)`;
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function toRad(deg) {
        return deg * Math.PI / 180;
    }

    function geocodeLocation() {
        const userInput = document.getElementById("userInput").value.trim();
        const adminInput = document.getElementById("adminInput").value.trim();

        if (userInput) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(userInput)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        userLocation = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                        userMarker.setLatLng(userLocation);
                        map.setView(userLocation, 12);
                        drawRoute(userLocation, adminLocation);
                    }
                });
        }

        if (adminInput) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adminInput)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        adminLocation = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                        adminMarker.setLatLng(adminLocation);
                        drawRoute(userLocation, adminLocation);
                    }
                });
        }
    }

    window.onload = initMap;
</script>
</body>
</html>
