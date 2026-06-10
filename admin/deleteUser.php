<?php

require_once '../config.php';


// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}



//check id found
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];


  //dont delete admin
    if ($id === $_SESSION['user_id']) {
        $_SESSION['user_error'] = "You cannot delete your own account.";
        header('Location: admin-dashboard.php');
        exit();
    }

    
    try {
        //delete user from datatabes
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = :id");
        $stmt->execute(['id' => $id]);

        //successe message
        $_SESSION['user_success'] = "User deleted successfully.";
        
    } catch (PDOException $e) {

        // storage erreurs in erreurs.log;
        $errorPath = __DIR__ . '/../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] DELETE USER ERROR: " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);

        // erreurs message
        $_SESSION['user_error'] = "Could not delete user. Please try again.";
    }
}

//back to page which request come
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-users.php';
header('Location: ' . $redirect_url);
exit();
?>