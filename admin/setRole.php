<?php
require_once '../config.php';

//check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

//check if id found
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // get role of user
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            
            if ($user['role'] === 'admin') {
                $_SESSION['user_error'] = "This user is already an administrator.";
            } else {
                // change role if user is not admin;
                $update = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id_user = :id");
                $update->execute(['id' => $id]);
                $_SESSION['user_success'] = "User has been promoted to administrator.";
            }
        }
        
    } catch (PDOException $e) {
        $errorPath = __DIR__ . '/../erreurs/erreurs.log';
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] ROLE PROMOTION ERROR (ID: $id): " . $e->getMessage() . PHP_EOL;
        file_put_contents($errorPath, $errorMessage, FILE_APPEND);
        $_SESSION['user_error'] = "Could not update role.";
    }
}

// back to page which request come;
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin-dashboard.php';
header('Location: ' . $redirect_url);
exit();