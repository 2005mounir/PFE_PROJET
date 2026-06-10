<?php
require_once '../config.php'; 

// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        //change status 
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        
        $_SESSION['user_success'] = "User has been unblocked successfully.";
        
    } catch (PDOException $e) {
        $errorPath = __DIR__ . '/../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] UNBLOCK ERROR (ID: $id): " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);
        $_SESSION['user_error'] = "An error occurred during unblocking.";
    }
}

//back to this page 
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: managmentUsers.php');
}
exit();