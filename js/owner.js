
// code get location
document.addEventListener("DOMContentLoaded", function() {
    let container = document.getElementById('map-container');
    if (!container) return;

    // Retrieving values from Data Attributes
    let lat = parseFloat(container.getAttribute('data-lat'));
    let lng = parseFloat(container.getAttribute('data-lng'));
    let title = container.getAttribute('data-title');

    if (isNaN(lat) || isNaN(lng)) {
        console.error("Invalid map coordinates:", lat, lng);
        return;
    }

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
    marker.bindPopup("<b>" + title + "</b><br>location of property").openPopup();

    // 5. Fullscreen button manual fix if leaflet-fullscreen is missing
    let fullScreenBtn = L.control({ position: 'topleft' });
    fullScreenBtn.onAdd = function (map) {
        let div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
        div.innerHTML = '<button type="button" style="width:30px; height:30px; cursor:pointer; background: white; border: none; font-size: 18px;">⤢</button>';
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

// code to control message
document.addEventListener("DOMContentLoaded", function() {
    let alertMsg = document.querySelector('.alert-msg');
    if (alertMsg) {
        setTimeout(function() {
            alertMsg.style.transition = "opacity 0.5s ease";
            alertMsg.style.opacity = "0";
            setTimeout(function() {
                alertMsg.remove();
            }, 500); 
        }, 3000);
    }
});

// ajax update data
document.addEventListener("DOMContentLoaded", function() {
    let form = document.getElementById('propertyForm');
    if (!form) return;

    let submitBtn = form.querySelector('button[type="submit"]');
   let generalErrorDiv = document.getElementById('form-general-error');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        // 1. Clean previous errors
        document.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
        if(generalErrorDiv) {
            generalErrorDiv.style.display = 'none';
            generalErrorDiv.innerText = '';
        }

        // 2. Disable button
        submitBtn.disabled = true;
       let originalText = submitBtn.innerText;
        submitBtn.innerText = "Updating...";

        let formData = new FormData(form);

        // 3. Handle images (only if allUploadedFiles is used for preview/multi-select)
        if (typeof allUploadedFiles !== 'undefined' && allUploadedFiles.length > 0) {
            formData.delete('property_images[]'); 
            allUploadedFiles.forEach(file => {
                formData.append('property_images[]', file);
            });
        }

        // 4. Send request
        fetch('editeProperties.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Server returned ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alert(data.message); 
                window.location.href = 'myPropertiy.php';
            } else {
                if (data.type === 'validation') {
                    Object.keys(data.errors).forEach(key => {
                        let errorSpan = document.getElementById(`error-${key}`);
                        if (errorSpan) errorSpan.innerText = data.errors[key];
                    });
                } else {
                    let errorMsg = data.errors ? (Array.isArray(data.errors) ? data.errors[0] : (data.errors.system || 'An error occurred')) : 'An unknown error occurred';
                    if(generalErrorDiv) {
                        generalErrorDiv.innerText = errorMsg;
                        generalErrorDiv.style.display = 'block';
                    } else {
                        alert(errorMsg);
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("A network error occurred. Please try again.");
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        });
    });
});








//glarry images of viewPropertyOwner
function changeMainImage(src) {
   let mainImg = document.getElementById('current-img');
    if (mainImg) {
            mainImg.src = src;
    }
}



//code js editePropertiesOwner.php get location and updat data
document.addEventListener("DOMContentLoaded", function() {

// 1. Read coordinates from the HTML
    let mapData = document.getElementById('map');
    let lat = parseFloat(mapData.getAttribute('data-lat'));
    let lng = parseFloat(mapData.getAttribute('data-lng'));

    // 2. Leaflet Map Setup
    let map = L.map('map', {
        fullscreenControl: true,
        maxZoom: 19
    }).setView([lat, lng], 16);

    L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 19,
        attribution: '© Google Maps'
    }).addTo(map);

   let marker = L.marker([lat, lng], {
        draggable: true
    }).addTo(map);

    // 4. Map and marker events
    marker.on('dragend', function(e) {
        let pos = marker.getLatLng();
        document.getElementById('latitude').value = pos.lat;
        document.getElementById('longitude').value = pos.lng;
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });

    // 5. Image Deletion Handler
    window.deleteImage = function(id) { 
        if (confirm('Are you sure you want to delete this image?')) {
           let container = document.getElementById('img-container-' + id);
            if (container) {
                container.remove();
               let form = document.getElementById('propertyForm');
                let hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'deleted_images[]';
                hiddenInput.value = id;
                form.appendChild(hiddenInput);
            }
        }
    };

    // 6. New Images Preview Logic
    let allUploadedFiles = [];
    document.getElementById('Upload').addEventListener('change', function() {
        let previewContainer = document.getElementById('images-preview');
        previewContainer.innerHTML = ''; 
        let newFiles = this.files;

        if (newFiles) {
            for (let i = 0; i < newFiles.length; i++) {
                if (allUploadedFiles.length < 10) {
                    allUploadedFiles.push(newFiles[i]);
                }
            }
            
            allUploadedFiles.forEach((file, index) => {
                if (file.type.match('image.*')) {
                   let reader = new FileReader();
                    reader.onload = function(e) {
                       let imgWrapper = document.createElement('div');
                        imgWrapper.style.cssText = 'position:relative; width:100px; height:100px; border:2px solid #2c3e50; border-radius:6px; overflow:hidden;';
                        
                        let img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.cssText = 'width:100%; height:100%; object-fit:cover;';
                        
                       let removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '×';
                        removeBtn.style.cssText = 'position:absolute; top:2px; right:2px; background:rgba(255,0,0,0.8); color:white; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; font-weight:bold;';
                        removeBtn.onclick = function() {
                            allUploadedFiles.splice(index, 1);
                            imgWrapper.remove();
                        };

                        imgWrapper.appendChild(img);
                        imgWrapper.appendChild(removeBtn);
                        previewContainer.appendChild(imgWrapper);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
});