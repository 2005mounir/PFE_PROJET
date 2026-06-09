<?php

require_once '../config.php'; 

//check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
      header("Location: login.php");
    exit();
}

//check if email found in url
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
          // delete message from database;
         $stmt = $pdo->prepare("DELETE FROM messages WHERE id_message = :id");
         $stmt->execute(['id' => $id]);

        //message of successe
        $_SESSION['success'] = "Message deleted successfully.";
    } catch (PDOException $e) {
        // erreur messsage
        $_SESSION['error'] = "Could not delete message.";
    }
}

//back to dashbord
header("Location: admin-dashboard.php");
exit();
?>