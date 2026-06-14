document.addEventListener("DOMContentLoaded", function() {
    let container = document.getElementById('map-container');
    if (!container) return;

    // Retrieving values from Data Attributes

    let lat = parseFloat(container.getAttribute('data-lat'));
    let lng = parseFloat(container.getAttribute('data-lng'));
    let title = container.getAttribute('data-title');

// Initialize the map
    let map = L.map('map', {
        fullscreenControl: true,
        maxZoom: 19 
    }).setView([lat, lng], 16);

// Activate Google Hybrid Map    
    L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 19,
        attribution: '© Google Maps'
    }).addTo(map);

    // add marker
    let marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup("<b>" + title + "</b><br>location of porperty").openPopup();

    // 5. Fullscreen button
    let fullScreenBtn = L.control({ position: 'topleft' });
    fullScreenBtn.onAdd = function (map) {
        let div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        div.innerHTML = '<button type="button" style="width:30px; height:30px; cursor:pointer;">⤢</button>';
        div.style.backgroundColor = 'white';
        div.onclick = function() {
            let elem = document.getElementById('map');
            if (elem.requestFullscreen) elem.requestFullscreen();
            else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
            else if (elem.msRequestFullscreen) elem.msRequestFullscreen();
        };
        return div;
    };
    fullScreenBtn.addTo(map);
});




//code delete message erreur in 3 second from browser
document.addEventListener('DOMContentLoaded', function() {
    //get all messages
    var alerts = document.querySelectorAll('.alert-msg');
    
    alerts.forEach(function(alert) {
// Hide the message after 3 seconds
        setTimeout(function() {
            alert.style.display = 'none';
        }, 3000);
    });
});