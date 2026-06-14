<?php

require_once '../config.php';//connect with data base


//check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        //get path of images
        $stmt = $pdo->prepare("SELECT image_path FROM images WHERE id_property = ?");
        $stmt->execute([$id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // delete images from server
        foreach ($images as $img) {
            $filePath = '../'. $img['image_path']; 
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        //delete pth of images from data base
        $stmt = $pdo->prepare("DELETE FROM images WHERE id_property = ?");
        $stmt->execute([$id]);

        // delete propertie from data base
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id_property = ?");
        $stmt->execute([$id]);

        // succsess message
        $_SESSION['message'] = " The property and all its images have been successfully deleted!";
        $_SESSION['msg_type'] = "success";

    } catch (PDOException $e) {
        // storage erreur in file of erreurs
        $logFile = '../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error deleting property ID $id: " . $e->getMessage() . PHP_EOL;
        error_log($errorMessage, 3, $logFile);
        
        // erreurs message
        $_SESSION['message'] = "An error occurred while deleting. Please try again later.";
        $_SESSION['msg_type'] = "error";
    }

    header('Location: managmentProperties.php');
    exit;
}
?>