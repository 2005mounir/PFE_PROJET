<?php
// 1. Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Permission guard for Owner
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    header('Location: ../login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - Rentora</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="wrapper">

    <div class="main-panel">
        <header class="main-header">
            <div class="header-title">
                <h3>Dashboard <i class="fa-solid fa-gauge-high dashboard-title-icon"></i></h3>
                <button id="mobile-menu-btn" class="mobile-toggle-btn">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            
            <div class="header-profile">
                <div class="user-info">
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Owner'); ?></strong></span>
                    <span class="role-badge">Property Owner</span>
                </div>
            </div>
        </header>
            <?php include 'includes/sidebar.php'; ?>

        <div class="content-body">