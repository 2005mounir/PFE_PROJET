<?php
session_start();
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        //update status in database;
        $stmt = $pdo->prepare("UPDATE properties SET status = 'rejected' WHERE id_property = ?");
        $stmt->execute([$id]);

        // check if property found and updated
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "The property has been successfully rejected.";
            $_SESSION['msg_type'] = "error"; 
        } else {
            $_SESSION['message'] = " Property not found.";
            $_SESSION['msg_type'] = "error";
        }

    } catch (PDOException $e) {
        //storage errurs in erreurs.log
        $logFile = '../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error Rejecting ID $id: " . $e->getMessage() . PHP_EOL;
        error_log($errorMessage, 3, $logFile);
        
        // message to user
        $_SESSION['message'] = " An internal error occurred during the rejection process.";
        $_SESSION['msg_type'] = "error";
    }

    header('Location: managmentProperties.php');
    exit;
}
?>