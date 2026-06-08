<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear the session array
$_SESSION = array();

//  Lock and permanently destroy the session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session on the server
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();