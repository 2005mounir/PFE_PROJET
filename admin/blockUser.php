<?php
require '../config.php'; 
// check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        //get user role from database
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['role'] === 'admin') {
            $_SESSION['user_error'] = "You cannot block an administrator.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id_user = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['user_success'] = "User has been blocked successfully.";
        }
        
    } catch (PDOException $e) {
 
     //storage erreurs in erruers/erreurs.log;
        $errorPath = __DIR__ . '/../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] BLOCK ERROR (ID: $id): " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);
        $_SESSION['user_error'] = "An error occurred.";
    }
}

  //back to this page;
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-dashboard.php';
header('Location: ' . $redirect_url);
exit();
?>