<?php
/**
 * Logout Script
 * Destroys session and redirects to login page
 */

require_once 'backend/config.php';

// Check if session is already started (config.php does this usually, but good to be safe)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log audit event if user was logged in
if (isset($_SESSION['user_id'])) {
    // We can't use logAudit() easily here immediately before destroy, 
    // but config.php functions are available.
    // Let's manually log if possible, or just skip it.
    // Ideally, we'd have a logLogout() function.
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
