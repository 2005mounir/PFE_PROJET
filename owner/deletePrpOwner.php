<?php
require_once '../config.php';
session_start();

// check if user found
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $owner_id = $_SESSION['user_id'];

    // get images to this property
    $stmt = $pdo->prepare("SELECT i.image_path 
                           FROM images i
                           JOIN properties p ON i.id_property = p.id_property
                           WHERE i.id_property = ? AND p.id_user = ?");
    $stmt->execute([$id, $owner_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // delete images from server
    foreach ($images as $img) {
        $filePath = '../'. $img['image_path']; 
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // delete data from data base and use condition id_proerty and id_user
    
    $stmt = $pdo->prepare("DELETE FROM images WHERE id_property = ?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM properties WHERE id_property = ? AND id_user = ?");
    $result = $stmt->execute([$id, $owner_id]);

    // check if datat deleted and storege message in session
    if ($result && $stmt->rowCount() > 0) {
        $_SESSION['message'] = "Property deleted successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        // storage erreur in file erreurs
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] Failed to delete property ID $id by user $owner_id" . PHP_EOL;
        file_put_contents('../erreurs/erreurs.log', $errorMessage, FILE_APPEND);
        
        $_SESSION['message'] = "Error: Could not delete property.";
        $_SESSION['msg_type'] = "error";
    }

    header('Location: myProperties.php');
    exit;
}
?>