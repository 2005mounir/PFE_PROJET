<?php



// Linking with settings and property class
require_once 'config.php';      
require_once 'classes/Property.php'; // The class that contains the save() method

//get function rederict form config.php
check_auth();


// 1. Page protection: ensure the user is logged in and authenticated
if (!isset($_SESSION['user_id'])) {
    if (isset($_SERVER['HTTP_X_REQUEST_WITH']) && strtolower($_SERVER['HTTP_X_REQUEST_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'error', 'type' => 'system', 'errors' => ['  Please log in first.']]);
        exit();
    }
    header("Location: login.php");
    exit();
}




// Handling the AJAX request (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $errors = [];   // Array to collect errors for each field


// 2. Strong CSRF protection (secure comparison resistant to timing attacks)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['status' => 'error', 'type' => 'system', 'errors' => ['خطأ أمني: حماية الجلسة غير صالحة (CSRF Invalid).']]);
        exit();
    }

    

// 3. Sanitization and strict validation of inputs against XSS
    
// Title validation
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    if (empty($title) || mb_strlen($title, 'UTF-8') < 5 || mb_strlen($title, 'UTF-8') > 100) {
        $errors['title'] = "The title is required and must be between 5 and 100 characters.";
    }


// Validate property type and rental duration
    $allowed_types = ['Apartment', 'Villa', 'Studio', 'Commercial', 'House'];
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    if (!in_array($type, $allowed_types)) {
        $errors['type'] = "The selected property type is invalid. Tampering detected.";
    }

    $allowed_rent_types = ['daily', 'weekly', 'monthly'];
    $rent_type = isset($_POST['rent_type']) ? trim($_POST['rent_type']) : '';
    if (!in_array($rent_type, $allowed_rent_types)) {
        $errors['rent_type'] = "The selected rental duration is invalid.";
    }




// Validate numeric values (type casting) against injection
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    if ($price === false || $price <= 0 || $price > 99999999.99) {
        $errors['price'] = "Please enter a valid and reasonable price.";
    }

    $rooms = filter_input(INPUT_POST, 'rooms', FILTER_VALIDATE_INT);
    if ($rooms === false || $rooms < 0 || $rooms > 50) {
        $errors['rooms'] = "Invalid number of rooms.";
    }

    $bathrooms = filter_input(INPUT_POST, 'bathrooms', FILTER_VALIDATE_INT);
    if ($bathrooms === false || $bathrooms < 0 || $bathrooms > 50) {
        $errors['bathrooms'] = "Invalid number of bathrooms.";
    }




// Validate required identifiers (Foreign Keys)
    $id_country = filter_input(INPUT_POST, 'id_country', FILTER_VALIDATE_INT);
    $id_city = filter_input(INPUT_POST, 'id_city', FILTER_VALIDATE_INT);
    if (!$id_country || !$id_city) {
        $errors['system'] = "The selected country or city is invalid.";
    }




// Validate full address and description
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    if (empty($address) || mb_strlen($address, 'UTF-8') < 10) {
        $errors['address'] = "Please enter a complete and detailed address (at least 10 characters).";
    }

    $discription = isset($_POST['discription']) ? trim($_POST['discription']) : '';
    if (empty($discription) || mb_strlen($discription, 'UTF-8') < 20) {
        $errors['discription'] = "Please write a detailed description of the property to attract visitors.";
    }




// Validate geographic coordinates accurately (Regex validation)
    $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';

// Ensure coordinates are within global range and are valid decimal numbers
    if (!preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/', $latitude) || 
        !preg_match('/^[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/', $longitude)) {
        $errors['map'] = "Please select a valid and real location for the property on the map.";
    }




// 4. Maximum security for image uploads (File Upload Security Zone)
    if (!isset($_FILES['property_images']) || empty($_FILES['property_images']['name'][0])) {
        $errors['property_images'] = "You must upload at least two images for the property.";
    } else {
        $files = $_FILES['property_images'];
        $file_count = count($files['name']);
        
        if ($file_count < 2 || $file_count > 10) {
            $errors['property_images'] = "The allowed number of images is between 2 and 10 only.";
        } else {
            // Allowed and secure file extensions only
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            // Supported real MIME types (to block forged files)        
           $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

            foreach ($files['tmp_name'] as $key => $tmp_name) {
                if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                    $errors['property_images'] = "An error occurred while uploading one of the images. Please try again.";
                    break;
                }

               // File size check (maximum 5MB per image)
                if ($files['size'][$key] > 5 * 1024 * 1024) {
                    $errors['property_images'] = "The image size is too large. The maximum allowed size is 5MB.";
                    break;
                }

               // Check file extension from filename
                $ext = strtolower(pathinfo($files['name'][$key], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_extensions)) {
                    $errors['property_images'] = "Invalid image extension! Only JPG, PNG, and WEBP are allowed.";
                    break;
                }

               // Real security check: verify MIME type from actual file content (not filename)             
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $real_mime = finfo_file($finfo, $tmp_name);
                finfo_close($finfo);

                if (!in_array($real_mime, $allowed_mimes)) {
                    $errors['property_images'] = "The uploaded file is not a valid image. It may be corrupted or forged! Operation blocked.";
                    break;
                }
            }
        }
    }





// If any tampering or error is detected, stop the process immediately and return errors
    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'type' => 'validation',
            'errors' => $errors
        ]);
        exit();
    }




// 5. Safely pass filtered data to the class (built with prepared statements to prevent SQL injection)
    try {
        $propertyManager = new Property($pdo); 
        
       // Reassemble clean and sanitized data
        $clean_data = [
            'title'       => $title,
            'id_country'  => $id_country,
            'id_city'     => $id_city,
            'type'        => $type,
            'rent_type'   => $rent_type,
            'price'       => $price,
            'rooms'       => $rooms,
            'bathrooms'   => $bathrooms,
            'address'     => $address,
            'discription' => $discription,
            'latitude'    => $latitude,
            'longitude'   => $longitude
        ];





$files_to_send = $_FILES['property_images'] ?? [];



// 2. Fix "Undefined array key 'role'" issue:
// Check if role exists in session; if not, assign default value 'tenant' to avoid warning
$current_role = $_SESSION['role'] ?? 'tenant';
$current_user_id = $_SESSION['user_id'] ?? null;


   $result = $propertyManager->save($clean_data, $files_to_send, $current_user_id, $current_role);
          echo json_encode($result);
        exit();


    } catch (Exception $e) {
// The folder and file are already prepared, directly write the log without validation
        error_log("[" . date('Y-m-d H:i:s') . "] Security/System Error: " . $e->getMessage() . PHP_EOL, 3, "erreurs/erreurs.log");

        echo json_encode([
            'status' => 'error',
            'type' => 'system',
            'errors' => ["An error occurred on the server. The issue has been logged and is being investigated."]
        ]);
        exit();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Property - Rentora</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.css" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">

</head>
<body class="body">



<?php
require_once 'config.php';
include 'includes/header.php'; 





try {
//  Securely fetch all countries in an organized way
    $stmt_countries = $pdo->query("SELECT id_country, country_name FROM countries ORDER BY country_name ASC");
    $countries = $stmt_countries->fetchAll(PDO::FETCH_ASSOC);


//  Fetch cities directly while including the foreign key (id_country) in the query
    $stmt_cities = $pdo->query("SELECT id_city, city_name, id_country FROM cities ORDER BY city_name ASC");
    $cities = $stmt_cities->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

// Professional practice: protect database paths from being exposed to users (Security Baseline)
    error_log("[" . date('Y-m-d H:i:s') . "] DB Fetch Error: " . $e->getMessage() . PHP_EOL, 3, "erreurs/erreur.logs");
    die("A server error occurred. Please try again later.");
}
?>






    <div class="main-content">  
        <h2>Add New House</h2>
        <p class="addP">Provide the property details, and pinpoint its exact location on the Google satellite map</p>


      
        <div id="form-general-error"></div>

        <form id="propertyForm" method="POST" enctype="multipart/form-data" autocomplete="off">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label class="addLable" >Property Title:</label>
                <input type="text" name="title" placeholder="e.g. Beautiful Apartment near Tangier Boulevard">
                <span class="error-msg" id="error-title"></span>
            </div>




        <div class="row-selects">
                <div class="form-group">
                    <label for="id_country" class="addLable">Country:</label>
                    <select name="id_country" id="id_country" required>
                        <option value="">-- Select Country --</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo (int)$country['id_country']; ?>">
                                <?php echo htmlspecialchars($country['country_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-msg" id="error-id_country"></span>
                </div>

            <div class="form-group">
                    <label for="id_city" class="addLable">City:</label>
                    <select name="id_city" id="id_city" required>
                        <option value="">-- Select City --</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo (int)$city['id_city']; ?>" data-country="<?php echo (int)$city['id_country']; ?>">
                                <?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-msg" id="error-id_city"></span>
                </div>
            </div>


            <div class="row-selects">
                <div class="form-group">
                    <label class="addLable">Property Type:</label>
                    <select name="type">
                        <option value="">-- Choose Type --</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Villa">Villa</option>
                        <option value="Studio">Studio</option>
                        <option value="Commercial">Commercial</option>
                        <option value="House">House</option>
                    </select>
                    <span class="error-msg" id="error-type"></span>
                </div>

                <div class="form-group">
                    <label  class="addLable">Rent Period :</label>
                    <select name="rent_type">
                        <option value="">-- Choose Period --</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    <span class="error-msg" id="error-rent_type"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="price" class="addLable" >Price (DH):</label>
                <input type="number" step="0.01" name="price" id="price" placeholder="e.g. 3500">
                <span class="error-msg" id="error-price"></span>
            </div>

            <div class="form-group">
                <label for="Rooms" class="addLable">Rooms:</label>
                <input type="number" name="rooms" id='Rooms' >
                <span class="error-msg" id="error-rooms"></span>
            </div>

            <div class="form-group">
                <label for="Bathrooms" class="addLable" >Bathrooms:</label>
                <input type="number" name="bathrooms" id="Bathrooms" >
                <span class="error-msg" id="error-bathrooms"></span>
            </div>

            <div class="form-group">
                <label for="Adress" class="addLable">Full Address in Tangier:</label>
                <textarea name="address" rows="2" id="Adress" placeholder="e.g. Branes, Malabata, California..."></textarea>
                <span class="error-msg" id="error-address"></span>
            </div>
            
            <div class="form-group">
                <label for="Discription" class="addLable" >Property Description :</label>
                <textarea name="discription" rows="4" id="Discription" placeholder="Write more details about your property (amenities, rules, etc.)..."></textarea>
                <span class="error-msg" id="error-discription"></span>
            </div>

            <div class="form-group">
                <label for="Upload" class="addLable">Upload Property Images (From 2 to 10 images):</label>
                <input type="file" name="property_images[]" id="Upload" accept="image/*" multiple>
                <span class="error-msg" id="error-property_images"></span>
                <div id="images-preview"></div>
           </div>

            <div class="form-group">
                <label class="addLable">Pin Location in Tangier (Go Fullscreen ⛶ for high precision):</label>
                <span class="error-msg" id="error-map" style="margin-bottom: 5px; font-weight: bold;"></span>
                
                <div id="map"></div>
                
                <div class="latlng-inputs">
                    <div>
                        <label  class="addLable">Latitude:</label>
                        <input type="text" id="latitude" name="latitude" readonly  placeholder="Click on map">
                    </div>
                    <div>
                        <label class="addLable">Longitude:</label>
                        <input type="text" id="longitude" name="longitude" readonly placeholder="Click on map">
                    </div>
                </div>
            </div>

            <button type="submit" id="btn-submit" class="btn-submit">Save Property</button>
        </form>

         
        
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.js"></script>
    <script src="js/add-property.js"></script>
    <?php include 'includes/footer.php'; ?>

</body>
</html>