<?php
require_once 'config.php';
require_once 'classes/users.php';




/*
// 1. Initial Security: If the user is already logged in, redirect them out of the login page
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header("Location:admin/admin-dashboard.php");
    } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner') {
        header("Location: owner_profile.php");
    } else {
        header("Location: index.php");
    }
    exit();
}
*/


$errors = [];


// 2. Rate limiting protection (cleaning and monitoring login attempts in the last 60 seconds)
if (!isset($_SESSION['login_attempt'])) {
    $_SESSION['login_attempt'] = [];
}
$_SESSION['login_attempt'] = array_filter(
    $_SESSION['login_attempt'],
    fn($t) => $t > time() - 60
);



// If more than 5 attempts per minute, block the request
if (count($_SESSION['login_attempt']) >= 5) {
    die("Too many login attempts. Please wait 60 seconds.");
}

  //Handle form submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

   // CSRF protection
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF Token Invalid");
    }

    // Record the current attempt time in storage
    $_SESSION['login_attempt'][] = time();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

     // Initial input validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

     // 4. If inputs are clean, proceed to database validation
    if (empty($errors)) {
        
        $userObj = new users($pdo);
        $userData = $userObj->login($email, $password);

        if ($userData) {
          // Login successful! Regenerate session ID to protect against session hijacking          
        session_regenerate_id(true);

           // Store basic user data in session
            $_SESSION['user_id'] = $userData['id_user'];
            $_SESSION['user_name'] = $userData['name'];
            $_SESSION['user_role'] = $userData['role'];

          // Clear failed login attempts since the user successfully logged in
            unset($_SESSION['login_attempt']);

            

               // Inside login.php (after a successful login):
                if (isset($_SESSION['redirect_url']) && !empty($_SESSION['redirect_url'])) {
                    $destination = $_SESSION['redirect_url'];
                    
                    unset($_SESSION['redirect_url']); // Remove the variable from the session so it does not remain set
                     header("Location: " . $destination);
                    exit();
                } else {
                    if ($_SESSION['user_role'] === 'admin') {
                          header("Location: admin/admin-dashboard.php");
                    } elseif ($_SESSION['user_role'] === 'owner') {
                          header("Location: owner/myProperties.php");
                     } else {
                           header("Location: index.php");
                    }
                    exit();
                }
        } else {
             // Generic message to protect accounts from enumeration attacks
             $errors[] = "Invalid email or password.";
        }
    }
}

// Generate a new CSRF token if it does not exist in the session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/main.css">
    <title>Login</title>
</head>
<body class="loginBody">
   <?php
     include "includes/header.php"; 
     
   ?>
<div class="register-wrapper">
    <div class="loginImage">
        <img src="../assets/images/contactImage.png" alt="Login Image" class="login-image">
    </div>

   <div class="register-container">
        <h2>Login to Your Account</h2>

            <?php if (!empty($errors)) : ?>
            <div style="color: red; margin-bottom: 15px;">
                <?php foreach ($errors as $error) : ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="email">
                <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required placeholder='your email...'>
            </div>
            <br>

            <div class="password">
                <input type="password" name="password" required placeholder="password...">
            </div>
            <br>

            <button type="submit" name="login" class="btnLogin">Log In</button>
        </form>

        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
        
     
  </div>
</div>



 <?php
     include "includes/footer.php";
   ?>
 
</body>
</html>