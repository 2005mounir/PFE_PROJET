<?php
session_start();
require_once '../config.php';

// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// check if id found
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
       // update status
        $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id_message = :id");
        $stmt->execute(['id' => $id]);

        // succsses message
        $_SESSION['success'] = "Message marked as read.";
        
    } catch (PDOException $e) {

        //storage erreurs in errerus.log
        $errorPath = __DIR__ . '/../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] READ ERROR: " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);

        //errurs message
        $_SESSION['error'] = "Could not update message status.";
    }
}

// back to page which request come;
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-dashboard.php';
header('Location: ' . $redirect_url);
exit();
?>