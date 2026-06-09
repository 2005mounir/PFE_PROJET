<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id_message = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "Message marked as read.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Could not update message.";
    }
}

header("Location: admin-dashboard.php");
exit();
?>