<?php
ob_start(); 
require_once '../config.php';

// AJAX POST Request Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_property'])) {
    ob_clean();
    header('Content-Type: application/json');
    
    $id_property = (int)$_POST['id_property'];
    $owner_id = $_SESSION['user_id'];
    $errors = [];

    // 1. Basic Validation
    if (empty($_POST['title'])) $errors['title'] = "Title is required.";
    if (empty($_POST['price']) || !is_numeric($_POST['price'])) $errors['price'] = "A valid price is required.";
    if (empty($_POST['id_country'])) $errors['id_country'] = "Country is required.";
    if (empty($_POST['id_city'])) $errors['id_city'] = "City is required.";
    if (empty($_POST['type'])) $errors['type'] = "Property type is required.";
    if (empty($_POST['rent_type'])) $errors['rent_type'] = "Rent period is required.";

    // 2. Rooms and Bathrooms Validation
    if (!isset($_POST['rooms']) || $_POST['rooms'] < 0) $errors['rooms'] = "Please enter valid number of rooms.";
    if (!isset($_POST['bathrooms']) || $_POST['bathrooms'] < 0) $errors['bathrooms'] = "Please enter valid number of bathrooms.";

    // 3. Text Areas Validation
    if (empty($_POST['address'])) $errors['address'] = "Address is required.";
    if (empty($_POST['discription'])) $errors['discription'] = "Description is required.";

    // 4. Map Location Validation
    if (empty($_POST['latitude']) || empty($_POST['longitude'])) $errors['map'] = "Please pin the location on the map.";

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'type' => 'validation', 'errors' => $errors]);
        exit;
    }





    // 5. Image Validation
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM images WHERE id_property = ?");
    $stmt->execute([$id_property]);
    $existing_count = $stmt->fetchColumn();
    
    $deleted_count = !empty($_POST['deleted_images']) ? count($_POST['deleted_images']) : 0;
    $new_count = (!empty($_FILES['property_images']['name'][0])) ? count($_FILES['property_images']['name']) : 0;
    
    if (($existing_count - $deleted_count + $new_count) < 2) {
        $errors['property_images'] = "Property must have at least 2 images.";
    }

    if ($new_count > 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        foreach ($_FILES['property_images']['tmp_name'] as $key => $tmp_name) {
            if (empty($tmp_name)) continue;
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = @finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            if (!$mime_type || !in_array($mime_type, $allowed_types)) {
                $errors['property_images'] = "File " . $_FILES['property_images']['name'][$key] . " is not a valid image.";
                break; 
            }

            if ($_FILES['property_images']['size'][$key] > $max_size) {
                $errors['property_images'] = "Image " . $_FILES['property_images']['name'][$key] . " is too large (Max 5MB).";
                break;
            }
        }
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'type' => 'validation', 'errors' => $errors]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Update Property Details
        $sql = "UPDATE properties SET title = ?, price = ?, id_country = ?, id_city = ?, type = ?, rent_type = ?, address = ?, discription = ?, latitude = ?, longitude = ?, rooms = ?, bathrooms = ? WHERE id_property = ? AND id_user = ?";
        $pdo->prepare($sql)->execute([
            $_POST['title'], $_POST['price'], $_POST['id_country'], $_POST['id_city'], 
            $_POST['type'], $_POST['rent_type'], $_POST['address'], $_POST['discription'], 
            $_POST['latitude'], $_POST['longitude'], $_POST['rooms'], $_POST['bathrooms'],
            $id_property, $owner_id
        ]);

        // 2. Delete Selected Images
        if (!empty($_POST['deleted_images'])) {
            foreach ($_POST['deleted_images'] as $img_id) {
                $stmt = $pdo->prepare("SELECT image_path FROM images WHERE id_image = ? AND id_property = ?");
                $stmt->execute([$img_id, $id_property]);
                $img = $stmt->fetch();
                if ($img && file_exists("../" . $img['image_path'])) {
                    unlink("../" . $img['image_path']);
                }
                $pdo->prepare("DELETE FROM images WHERE id_image = ?")->execute([$img_id]);
            }
        }

        // 3. Upload New Images
        if ($new_count > 0) {
            $upload_dir = '../uploads/properties/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            foreach ($_FILES['property_images']['tmp_name'] as $key => $tmp_name) {
                if (empty($tmp_name)) continue;

                $file_name = $_FILES['property_images']['name'][$key];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (empty($file_ext)) $file_ext = 'jpg';
                
                $new_name = "prop_" . $id_property . "_" . uniqid() . "." . $file_ext;
                $target_path = $upload_dir . $new_name;
                $db_path = "uploads/properties/" . $new_name;

                if (move_uploaded_file($tmp_name, $target_path)) {
                    $pdo->prepare("INSERT INTO images (id_property, image_path) VALUES (?, ?)")->execute([$id_property, $db_path]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Property updated successfully!']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'errors' => ['system' => $e->getMessage()]]);
    }
    exit;
}


        //check if id found
        if (!isset($_GET['id'])) die("ID missing");
        $id = (int)$_GET['id'];


        //get data by this id from database 
        $stmt = $pdo->prepare("SELECT p.* FROM properties p WHERE p.id_property = ? AND p.id_user = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$property) die("Property not found or access denied!");

        $countries = $pdo->query("SELECT * FROM countries ORDER BY country_name ASC")->fetchAll();
        $cities = $pdo->query("SELECT * FROM cities ORDER BY city_name ASC")->fetchAll();
        $images = $pdo->prepare("SELECT * FROM images WHERE id_property = ?");
        $images->execute([$property['id_property']]);
        $images = $images->fetchAll(PDO::FETCH_ASSOC);

        include 'includes/header.php';
?>

<div class="main-content">  
    <h2>Edit Property</h2>
    <p class="addP">Modify the property details, and update its exact location on the map</p>

    <div id="form-general-error" class="alert-error" style="display:none; margin-bottom: 20px; padding: 15px; border-radius: 8px; background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 500;"></div>

    <form id="propertyForm" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="id_property" value="<?= $property['id_property'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="form-group">
            <label class="addLable">Property Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($property['title']) ?>" placeholder="e.g. Beautiful Apartment near Tangier Boulevard">
            <span class="error-msg" id="error-title"></span>
        </div>

        <div class="row-selects">
            <div class="form-group">
                <label class="addLable">Country:</label>
                <select name="id_country" id="id_country">
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $c): ?>
                        <option value="<?= $c['id_country'] ?>" <?= ($property['id_country'] == $c['id_country']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['country_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-msg" id="error-id_country"></span>
            </div>
            <div class="form-group">
                <label class="addLable">City:</label>
                <select name="id_city" id="id_city">
                    <option value="">Select City</option>
                    <?php foreach ($cities as $ci): ?>
                        <option value="<?= $ci['id_city'] ?>" data-country="<?= $ci['id_country'] ?>" <?= ($property['id_city'] == $ci['id_city']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ci['city_name']) ?>
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
                    <?php foreach(['Apartment', 'Villa', 'Studio', 'Commercial', 'House'] as $t): ?>
                        <option value="<?= $t ?>" <?= ($property['type'] == $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-msg" id="error-type"></span>
            </div>
            <div class="form-group">
                <label class="addLable">Rent Period:</label>
                <select name="rent_type">
                    <?php foreach(['daily', 'weekly', 'monthly'] as $p): ?>
                        <option value="<?= $p ?>" <?= ($property['rent_type'] == $p) ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error-msg" id="error-rent_type"></span>
            </div>
        </div>

        <div class="form-group">
            <label class="addLable">Price (DH):</label>
            <input type="number" step="0.01" name="price" value="<?= $property['price'] ?>" placeholder="e.g. 3500">
            <span class="error-msg" id="error-price"></span>
        </div>

        <div class="form-group">
            <label class="addLable">Rooms:</label>
            <input type="number" name="rooms" value="<?= $property['rooms'] ?>" placeholder="Number of rooms">
            <span class="error-msg" id="error-rooms"></span>
        </div>

        <div class="form-group">
            <label class="addLable">Bathrooms:</label>
            <input type="number" name="bathrooms" value="<?= $property['bathrooms'] ?>" placeholder="Number of bathrooms">
            <span class="error-msg" id="error-bathrooms"></span>
        </div>

        <div class="form-group">
            <label class="addLable">Full Address:</label>
            <textarea name="address" rows="2" placeholder="e.g. Branes, Malabata, California..."><?= htmlspecialchars($property['address']) ?></textarea>
            <span class="error-msg" id="error-address"></span>
        </div>

        <div class="form-group">
            <label class="addLable">Property Description:</label>
            <textarea name="discription" rows="4" placeholder="Write more details about your property..."><?= htmlspecialchars($property['discription']) ?></textarea>
            <span class="error-msg" id="error-discription"></span>
        </div>

        <div class="form-group">
            <label class="addLable">Existing Images:</label>
            <div id="current-images" style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
                <?php foreach ($images as $img): ?>
                    <div class="img-wrapper" id="img-container-<?= $img['id_image'] ?>" style="position: relative; width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd;">
                        <img src="../<?= htmlspecialchars($img['image_path']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" onclick="deleteImage(<?= $img['id_image'] ?>)" style="position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold;">×</button>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <label for="Upload" class="addLable">Add New Images:</label>
            <input type="file" name="property_images[]" id="Upload" accept="image/*" multiple>
            <div id="images-preview" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;"></div>
            <span class="error-msg" id="error-property_images"></span>
        </div>

        <div class="form-group">
            <label class="addLable">Pin Location:</label>
            <span class="error-msg" id="error-map" style="margin-bottom: 5px; font-weight: bold;"></span>
            <div id="map"  data-lat="<?= htmlspecialchars($property['latitude']) ?>" 
                          data-lng="<?= htmlspecialchars($property['longitude']) ?>" 
            style="height: 400px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 15px;">
                  
                   
               

            </div>

            <div class="latlng-inputs" style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <label class="addLable">Latitude:</label>
                    <input type="text" id="latitude" name="latitude" value="<?= $property['latitude'] ?>" readonly>
                </div>
                <div style="flex: 1;">
                    <label class="addLable">Longitude:</label>
                    <input type="text" id="longitude" name="longitude" value="<?= $property['longitude'] ?>" readonly>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">Update Property</button>
    </form>
</div>

</div> <!-- Close content-body -->
</div> <!-- Close main-panel -->
</div> <!-- Close wrapper -->



<?php include '../includes/footer.php'; ?>
