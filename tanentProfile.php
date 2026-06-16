<?php
require_once 'config.php';

//check if sessionof user found
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();


    
}

// get data of user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
</head>
<body>
  <?php  
     include 'includes/header.php';
  ?>
    
    <div class="profile-container">
    <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>
    
    <div class="profile-info">
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
    </div>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

    <?php  
     include 'includes/footer.php';
  ?>
<style>

body {
    background-color: #f4f7f6;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    line-height: 1.6;
}

.profile-container {
    max-width: 500px;
    margin: 60px auto;
    padding: 40px;
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    text-align: center;
}

.profile-container h1 {
    margin-bottom: 30px;
    color: #2c3e50;
    font-size: 1.8rem;
}

.profile-info {
    text-align: left;
    background: #fcfcfc;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #eee;
}

.profile-info p {
    margin: 0;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
}

.profile-info p:last-child {
    border-bottom: none;
}

.profile-info strong {
    color: #7f8c8d;
    font-weight: 600;
}


.logout-btn {
    display: inline-block;
    margin-top: 30px;
    padding: 12px 30px;
    background-color: #e74c3c;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: bold;
    transition: background 0.3s ease;
}

.logout-btn:hover {
    background-color: #c0392b;
}


@media (max-width: 500px) {
    .profile-container {
        margin: 20px;
        padding: 20px;
    }
}
</style>
