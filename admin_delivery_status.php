<?php
$userLat = 16.4725;
$userLng = 80.5929;
$adminLat = 17.4305;
$adminLng = 78.4011;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Live Delivery Route Tracking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.marker.slideto@0.2.0/leaflet.sli.css"/>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #eaf0f6;
      margin: 0;
      padding: 0;
    }
    h2 {
      text-align: center;
      margin: 20px 0;
      color: #34495e;
    }
    #map {
      height: 75vh;
      width: 90%;
      margin: 20px auto;
      border-radius: 12px;
      border: 3px solid #3498db;
      animation: fadeIn 1s ease-in-out;
    }
    #controls {
      text-align: center;
      margin-bottom: 15px;
    }
    input, button {
      padding: 10px;
      margin: 5px;
      font-size: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
      max-width: 250px;
      transition: all 0.3s ease;
    }
    input:focus {
      border-color: #3498db;
      outline: none;
    }
    .btn {
      background: #3498db;
      color: white;
      border: none;
      cursor: pointer;
    }
    .btn:hover {
      background: #2980b9;
      transform: scale(1.05);
    }
    .back-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      padding: 10px 15px;
      background: #e74c3c;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      z-index: 1000;
    }
    .back-btn:hover {
      background: #c0392b;
    }
    #eta {
      text-align: center;
      font-size: 18px;
      color: #2c3e50;
      font-weight: 500;
      margin-bottom: 15px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.98); }
      to { opacity: 1; transform: scale(1); }
    }
    @media (max-width: 600px) {
      input, button {
        width: 90%;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

<a href="javascript:history.back()" class="back-btn">← Back</a>
<h2>🚚 Live Delivery Tracking (User → Admin)</h2>

<div id="controls">
  <input id="userInput" type="text" placeholder="Set User Location (city/village)">
  <input id="adminInput" type="text" placeholder="Set Admin Location (optional)">
  <button class="btn" onclick="geocodeLocation()">Update Location</button>
</div>

<div id="map"></div>
<p id="eta">📏 Calculating distance and time...</p>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.marker.slideto@0.2.0/leaflet.sli.min.js"></script>
<script>
let map, userMarker, adminMarker, routeLine;
let userLocation = [<?= $userLat ?>, <?= $userLng ?>];
let adminLocation = [<?= $adminLat ?>, <?= $adminLng ?>];

function initMap() {
  map = L.map('map').setView(userLocation, 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  userMarker = L.marker(userLocation).addTo(map).bindPopup("User Location").openPopup();
  adminMarker = L.marker(adminLocation).addTo(map).bindPopup("Admin Location");
  drawRoute(userLocation, adminLocation);
}

function drawRoute(start, end) {
  if (routeLine) map.removeLayer(routeLine);
  routeLine = L.polyline([start, end], {
    color: 'blue',
    weight: 4,
    smoothFactor: 1
  }).addTo(map);
  updateEstimatedTime();
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
function toRad(deg) { return deg * Math.PI / 180; }

function updateEstimatedTime(speedKmph = 40) {
  const distance = haversineDistance(userLocation[0], userLocation[1], adminLocation[0], adminLocation[1]);
  const timeHours = distance / speedKmph;
  const minutes = Math.round(timeHours * 60);
  document.getElementById("eta").innerText =
    `📏 Distance: ${distance.toFixed(2)} km | ⏱ ETA: ${minutes} min (at ${speedKmph} km/h)`;
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
          userMarker.setLatLng(userLocation).bindPopup("User Location (updated)").openPopup();
          map.panTo(userLocation);
          drawRoute(userLocation, adminLocation);
        } else {
          alert("User location not found.");
        }
      });
  }

  if (adminInput) {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adminInput)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          adminLocation = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
          adminMarker.setLatLng(adminLocation).bindPopup("Admin Location (updated)").openPopup();
          drawRoute(userLocation, adminLocation);
        } else {
          alert("Admin location not found.");
        }
      });
  }
}

function trackLiveUser() {
  if (navigator.geolocation) {
    setInterval(() => {
      navigator.geolocation.getCurrentPosition(pos => {
        const lat = pos.coords.latitude;
        const lon = pos.coords.longitude;
        userLocation = [lat, lon];
        userMarker.slideTo(userLocation, {
          duration: 1000,
          keepAtCenter: false
        }).bindPopup(`📍 Live User: ${lat.toFixed(4)}, ${lon.toFixed(4)}`).openPopup();
        drawRoute(userLocation, adminLocation);
      }, (err) => {
        console.warn("Geolocation error: ", err.message);
      });
    }, 5000);
  } else {
    alert("Geolocation not supported.");
  }
}

window.onload = () => {
  initMap();
  trackLiveUser();
};
</script>
</body>
</html>
