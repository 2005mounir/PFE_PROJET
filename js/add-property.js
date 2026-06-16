
// 1. Enable the map with fullscreen mode.   
// 1. Initialize the map with reasonable zoom limits
var map = L.map('map', {
    fullscreenControl: true,
    fullscreenControlOptions: {
        position: 'topleft'
    },
    maxZoom: 19 // Google Satellite mode with max zoom level 19 in Morocco to avoid blocking
}).setView([35.7595, -5.8340], 14); 



// 2. Correct and fast Google Hybrid map URL
var googleHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    maxZoom: 19,
    attribution: '© Google Maps'
}).addTo(map);

var marker;



// Function to update input coordinates and the map marker
function updateLocation(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);

    if (marker) {
        map.removeLayer(marker);
    }
    marker = L.marker([lat, lng]).addTo(map);
}


// Capture coordinates on standard map click
map.on('click', function(e) {
    updateLocation(e.latlng.lat, e.latlng.lng);
});



// 3. Create the location button with anti-conflict property
var locateControl = L.control({ position: 'topright' });

locateControl.onAdd = function (map) {
    var btn = L.DomUtil.create('button', 'leaflet-bar leaflet-control');
    btn.type = 'button';
    btn.id = 'btn-locate';
    btn.innerHTML = '📍 My Current Location';
    btn.style.background = '#ffffff';
    btn.style.border = '2px solid #2c3e50';
    btn.style.padding = '8px 12px';
    btn.style.fontWeight = 'bold';
    btn.style.cursor = 'pointer';
    btn.style.borderRadius = '4px';
    


    //  Crucial line: Prevents map click events from bubbling up and interrupting the button click 
    L.DomEvent.disableClickPropagation(btn);
    
    return btn;
};
locateControl.addTo(map);


// 4. Smart geolocation system triggered on button click
document.getElementById('btn-locate').addEventListener('click', function() {
    var currentButton = this;
    currentButton.innerHTML = '🔄 Locating...'; // Update text to show active progress


    // Plan A: Attempt to use the browser's native Geolocation API (GPS)
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            map.setView([userLat, userLng], 18);
            updateLocation(userLat, userLng);
            currentButton.innerHTML = 'My Current Location';
        }, function(error) {
        


            // Plan B (Fallback): If GPS is blocked or restricted, use IP-API immediately
            fetch('https://ip-api.com/json/')
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        map.setView([data.lat, data.lon], 15);
                        updateLocation(data.lat, data.lon);
                    } else {
                        alert("Could not determine location automatically. Please select your location manually by clicking on the map.");
                    }
                    currentButton.innerHTML = ' My Current Location';
                })
                .catch(() => {
                    alert("Connection error. Please select your location manually.");
                    currentButton.innerHTML = ' My Current Location';
                });
        }, { enableHighAccuracy: true, timeout: 5000 });
    } else {
        // Fallback: If Geolocation API is completely unsupported, default directly to IP-based location
        fetch('https://ip-api.com/json/')
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    map.setView([data.lat, data.lon], 15);
                    updateLocation(data.lat, data.lon);
                }
                currentButton.innerHTML = ' My Current Location';
            });
    }
});








// Global array to store all selected images across multiple clicks
let allUploadedFiles = [];

document.getElementById('Upload').addEventListener('change', function() {
    var previewContainer = document.getElementById('images-preview');
    
    // Clear the DOM preview container to redraw everything correctly
    previewContainer.innerHTML = ''; 
    
    // Force strict flexbox layout for horizontal alignment
    previewContainer.style.display = 'flex';
    previewContainer.style.flexDirection = 'row';
    previewContainer.style.flexWrap = 'wrap';
    previewContainer.style.gap = '15px';
    previewContainer.style.marginTop = '15px';
    
    // Get the newly selected files from this current click
    var newFiles = this.files;

    if (newFiles) {
        // Push the newly selected files into our global array
        for (let i = 0; i < newFiles.length; i++) {
            // Stop adding if we reach the maximum limit of 10 images
            if (allUploadedFiles.length < 10) {
                allUploadedFiles.push(newFiles[i]);
            } else {
                alert("Maximum limit of 10 images reached.");
                break;
            }
        }
        
        // Loop through the entire global array to render ALL images (old + new)
        for (let i = 0; i < allUploadedFiles.length; i++) {
            let file = allUploadedFiles[i];
            
            if (file.type.match('image.*')) {
                let reader = new FileReader();
                
                reader.onload = function(e) {
                    // Create a separate container wrapper for each single thumbnail
                    let imgWrapper = document.createElement('div');
                    imgWrapper.style.display = 'block';
                    imgWrapper.style.position = 'relative';
                    imgWrapper.style.width = '100px';
                    imgWrapper.style.height = '100px';
                    imgWrapper.style.border = '2px solid #2c3e50';
                    imgWrapper.style.borderRadius = '6px';
                    imgWrapper.style.overflow = 'hidden';
                    imgWrapper.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
                    imgWrapper.style.backgroundColor = '#f8f9fa';

                    // Create the actual image element
                    let img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';

                    imgWrapper.appendChild(img);
                    previewContainer.appendChild(imgWrapper);
                };
                
                reader.readAsDataURL(file);
            }
        }
    }
});








//Ajax

// Select form elements, submit button, and global error container
const form = document.getElementById('propertyForm');
const submitBtn = document.getElementById('btn-submit');
const generalErrorDiv = document.getElementById('form-general-error');


form.addEventListener('submit', function(e) {
    e.preventDefault(); 


// 1. Clear all previous error messages from the screen before new validation
    document.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
    generalErrorDiv.style.display = 'none';
    generalErrorDiv.innerText = '';


// 2. Disable submit button to prevent multiple clicks and duplicate submissions
    submitBtn.disabled = true;
    submitBtn.innerText = "Processing & Uploading...";


// 3. Collect all form data into a hidden FormData container
    const formData = new FormData(form);

// Important: remove old input images and replace with smart allUploadedFiles array
    formData.delete('property_images');
    formData.delete('property_images[]'); 

// Ensure allUploadedFiles is defined and exists in the script above this logic
    if (typeof allUploadedFiles !== 'undefined') {
        allUploadedFiles.forEach(file => {
            formData.append('property_images[]', file);
        });
    }




// 4. Send the FormData silently to add.php via AJAX
    fetch('add.php', {
        method: 'POST',
        body: formData // Data is being sent in the background
    })


    .then(response => {
          // Smart check: ensure the server responded successfully (Status 200) and no 500 error occurred    
    if (!response.ok) {
            throw new Error("An internal server error occurred.");
        }
        return response.json(); // Read PHP response and parse it as JSON
         })
      /*   
    .then(data => {
        if (data.status === 'success') {
            // Case A: everything was successfully saved to the database
            alert(data.message); 
            
           
            // Use setTimeout to ensure the alert is closed and the browser can handle the redirect properly
            // Also ensures that even if the promise chain is interrupted by navigation, it's done cleanly.
            setTimeout(() => {
                window.location.href = 'index.php'; 
            }, 100);
             } else {
           // Case B: there is an issue returned by the server
            if (data.type === 'validation') {
           // Loop through errors and inject each message directly under its corresponding input field    
            Object.keys(data.errors).forEach(key => {
                    const errorSpan = document.getElementById(`error-${key}`);
                    if (errorSpan) {
                        errorSpan.innerText = data.errors[key]; // The error is safely output using innerText
                    }
                   return;
                });
            } else {
                // General system error displayed at the top only                
                generalErrorDiv.innerText = data.errors[0];
                generalErrorDiv.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' }); // // Scroll the screen to the top
            }
            


            // Re-enable the button in case of an error
            submitBtn.disabled = false;
            submitBtn.innerText = "Save Property";
        } 

    })
    */



    .then(data => {
    // نوقفو الـ button باش ما يعاودش يتكليكا
    submitBtn.disabled = true; 

    if (data.status === 'success') {
        // حيدي الـ alert نهائيا حيت كيدير مشكل مع الـ Redirect
        console.log("Success:", data); 
        
        // redirect مباشرة
        window.location.href = 'index.php'; 
    } else {
        // إذا كان هناك خطأ في الـ Validation
        if (data.type === 'validation') {
            Object.keys(data.errors).forEach(key => {
                const errorSpan = document.getElementById(`error-${key}`);
                if (errorSpan) errorSpan.innerText = data.errors[key];
            });
        } else {
            generalErrorDiv.innerText = data.errors[0];
            generalErrorDiv.style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // رجعي الزر للخدمة إلا وقع خطأ
        submitBtn.disabled = false;
        submitBtn.innerText = "Save Property";
    }
})
    .catch(error => {
        console.error('Error:', error);
        generalErrorDiv.innerText = "A network error occurred. Please try again.";
        generalErrorDiv.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
         //unlock the button so the user can submit again
        submitBtn.disabled = false;
        submitBtn.innerText = "Save Property";
    });
});