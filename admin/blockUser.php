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

    // dont block admin
    if ($id === $_SESSION['user_id']) {
        $_SESSION['user_error'] = "You cannot block your own account.";
        header('Location: admin-dashboard.php');
        exit();
    }

    try {
        
        $stmt = $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id_user = :id");
        $stmt->execute(['id' => $id]);

        // succsess message
        $_SESSION['user_success'] = "User has been blocked successfully.";
        
    } catch (PDOException $e) {

         //storage erreurs in erreurs.log
        $errorPath = __DIR__ . '/../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] BLOCK USER ERROR: " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);

        // erreur message
        $_SESSION['user_error'] = "Could not block user. Please try again.";
    }
}

//back to page which request come
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-users.php';
header('Location: ' . $redirect_url);
exit();
?>