<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        //update in database
        $stmt = $pdo->prepare("UPDATE properties SET status = 'approved' WHERE id_property = ?");
        $stmt->execute([$id]);


        // 2. Verify that the update was completed successfully
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = " The property has been approved successfully and is now visible to the public!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = " Property not found or it has already been approved.";
            $_SESSION['msg_type'] = "error";
        }

    } catch (PDOException $e) {

    //storage erreur in erreur.log
        $logFile = '../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error Approving ID $id: " . $e->getMessage() . PHP_EOL;
        error_log($errorMessage, 3, $logFile);
        
        // Display a general message to the user (without technical details for better security)
        $_SESSION['message'] = " Internal error occurred. The issue has been logged, please contact support.";
        $_SESSION['msg_type'] = "error";
    }

    header('Location: managmentProperties.php');
    exit;
}
?>