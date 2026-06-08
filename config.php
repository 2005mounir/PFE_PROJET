<?php


// 1. SECURITY HEADERS

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");



// 2.  SESSION COOKIE CONFIGURATION

// Note: This function must be executed before session_start() to take effect
session_set_cookie_params([
    'lifetime' => 0,               // Session cookie expires when the browser is closed
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']), // Ensures cookie is only transmitted over secure HTTPS connections
    'httponly' => true,            // Prevents JavaScript access to cookies (mitigates XSS attacks)
    'samesite' => 'Strict'         // Restricts cookie transmission to first-party context (mitigates CSRF attacks)
]);



// 3. 🏁 SESSION INITIALIZATION

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// 4.  INACTIVITY SESSION TIMEOUT

$timeout_duration = 1800; // 30 minutes of inactivity

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    // Session has expired; clear and destroy the old session data
    session_unset();
    session_destroy();
    
    // Start a fresh, unauthenticated session for the user
    session_start(); 
}

// Update the last activity timestamp on each page load
$_SESSION['LAST_ACTIVITY'] = time();



// 5. CSRF TOKEN AUTO GENERATION

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}



// 6. DATABASE CONNECTION
require_once 'classes/Database.php';

try {
    $db = new Database();
    $pdo = $db->connect();
} catch (Exception $e) {
    // Log the error internally to shield absolute server paths from end-users
    error_log("[" . date('Y-m-d H:i:s') . "] Connection Config Error: " . $e->getMessage() . PHP_EOL, 3, "erreurs/erreur.logs");
    die("A server error occurred. Please try again later.");
}




//  CENTRALIZED AUTHENTICATION CHECKER
function check_auth() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        
        // The magic here: get the exact path of the current page (e.g. /project_pfe/add.php)
       // Then extract only the filename (add.php) using basename()
        $_SESSION['redirect_url'] = basename($_SERVER['REQUEST_URI']);
        
// Redirect to the login page with a clean and simple URL
        header("Location: login.php");
        exit();
    }
}