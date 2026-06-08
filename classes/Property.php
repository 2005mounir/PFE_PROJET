<?php


class Property {
    private $db;

    
    public function __construct($pdo) {
        $this->db = $pdo;
    }


   // Added $user_id and $user_role parameters to remove dependency on SESSION inside the class
    public function save($data, $files, $user_id, $user_role) {
        try {
            $query = "INSERT INTO properties 
                (id_user, title, id_country, id_city, type, rent_type, price, rooms, bathrooms, address, discription, latitude, longitude) 
                VALUES 
                (:id_user, :title, :id_country, :id_city, :type, :rent_type, :price, :rooms, :bathrooms, :address, :discription, :latitude, :longitude)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_user'     => $user_id, 
                ':title'       => trim($data['title']),
                ':id_country'  => $data['id_country'],
                ':id_city'     => $data['id_city'],
                ':type'        => $data['type'],
                ':rent_type'   => $data['rent_type'],
                ':price'       => $data['price'],
                ':rooms'       => $data['rooms'],
                ':bathrooms'   => $data['bathrooms'],
                ':address'     => trim($data['address']),
                ':discription' => trim($data['discription']),
                ':latitude'    => $data['latitude'],
                ':longitude'   => $data['longitude']
            ]);



             // Get the property ID
            $property_id = $this->db->lastInsertId();




            // Image upload code
            $images_uploaded_successfully = true; 

            if (isset($files['tmp_name']) && $property_id) {
                $upload_dir = 'uploads/properties/';
                
              // Ensure the directory exists
                if (!is_dir($upload_dir)) { 
                    mkdir($upload_dir, 0755, true); 
                }

            //  The loop iterates directly over $files['tmp_name']
                foreach ($files['tmp_name'] as $key => $tmp_name) {
                    
          // If the field is empty in the preview, we skip it to avoid warnings.
                    if (empty($tmp_name)) {
                        continue;
                    }

                    $file_name = $files['name'][$key];
                    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    

                    if (empty($file_ext)) {
                        $file_ext = 'jpg';
                    }

                    $new_name  = "prop_" . $property_id . "_" . uniqid() . "." . $file_ext;
                    $target    = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $target)) {
                        $img_stmt = $this->db->prepare("INSERT INTO images (id_property, image_path) VALUES (?, ?)");
                        $img_stmt->execute([$property_id, $target]);
                    } else {
                        // If an image upload fails, notify the system
                        $images_uploaded_successfully = false;
                    }
                }
            }





             //  Update the user's role (only if all images are successfully uploaded)
            if ($user_role === 'tenant' && $images_uploaded_successfully) {
                $update_user = $this->db->prepare("UPDATE users SET role = 'owner' WHERE id_user = ?");
                $update_user->execute([$user_id]);
                
                $upgrade_role = true;
            } else {
                $upgrade_role = false;
            }

            return [
                'status'       => 'success', 
                'message'      => "Property saved successfully!",
                'upgrade_role' => $upgrade_role  // Send this flag to add.php
            ];

        } catch (Exception $e) {
                    // Log the error directly in your file (removed empty mkdir check since error logs are already set up)
            error_log("[" . date('Y-m-d H:i:s') . "] OOP DB Error: " . $e->getMessage() . PHP_EOL, 3, "erreurs/erreurs.log");

            return [
                'status' => 'error', 
                'type'   => 'system', 
                'errors' => ["An error occurred while saving data to the system."]
            ];
        }
    }
}
?>