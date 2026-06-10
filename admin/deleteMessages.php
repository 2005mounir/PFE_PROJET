<?php
require_once '../config.php'; 

// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// check if id found in url
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // delete message from database
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id_message = :id");
        $stmt->execute(['id' => $id]);

        // success message
        $_SESSION['success'] = "Message deleted successfully.";
        
    } catch (PDOException $e) {
        //storage erreurs in erreurs.log
        $errorPath = __DIR__ . '/../errors/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] DELETE ERROR: " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);

        // erreur message
        $_SESSION['error'] = "Could not delete message. Please try again.";
    }
}

// back to page which request come
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-dashboard.php';
header('Location: ' . $redirect_url);
exit();
?>