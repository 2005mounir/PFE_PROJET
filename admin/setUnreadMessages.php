<?php
require_once '../config.php';

//check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// check if id found in url
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    
    try {
        // change status of message
        $stmt = $pdo->prepare("UPDATE messages SET status = 'unread' WHERE id_message = :id");
        $stmt->execute(['id' => $id]);

        // success message
        $_SESSION['success'] = "Message marked as unread.";

        //back to page which request come;
        header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php'));
        exit();
        
    } catch (PDOException $e) {

        // storage erreurs in file errurs.log
        $errorPath = __DIR__ . '/../errors/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);
        
        $_SESSION['error'] = "Could not update message status. Please try again.";
        
        header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-dashboard.php'));
        exit();
    }
}


?>