function initMap() {
    const deliveryPoint = { lat: deliveryLat, lng: deliveryLng };
    
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 14,
      center: deliveryPoint,
    });
  
    const marker = new google.maps.Marker({
      position: deliveryPoint,
      map,
      title: "Delivery Location",
      icon: {
        url: "vehicle-icon.png",
        scaledSize: new google.maps.Size(40, 40),
      }
    });
  
    if (deliveryStatus === "Pending") {
      let moveLat = deliveryLat;
      let moveLng = deliveryLng;
  
      const interval = setInterval(() => {
        moveLat += 0.0001;
        moveLng += 0.0001;
        marker.setPosition({ lat: moveLat, lng: moveLng });
        map.panTo({ lat: moveLat, lng: moveLng });
  
        // Simulate delivery completion
        if (moveLat - deliveryLat > 0.002) {
          clearInterval(interval);
          document.getElementById("status").innerText = "Delivered";
        }
      }, 1000);
    }
  }
  
  window.onload = initMap;
  