<?php
/**
 * Authentication Handler
 * Handles login, logout, and session management
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Handle different actions
$action = $_POST['action'] ?? 'login';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        checkSession();
        break;
    default:
        jsonResponse(false, 'Invalid action', null, 400);
}

function handleLogin()
{
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = sanitize($_POST['role'] ?? '');
    $remember = isset($_POST['remember']);

    // Validation
    if (empty($username) || empty($password) || empty($role)) {
        jsonResponse(false, 'Please fill in all fields', null, 400);
    }

    if (!in_array($role, ['student', 'teacher', 'admin'])) {
        jsonResponse(false, 'Invalid role selected', null, 400);
    }

    try {
        $db = getDB();

        // Get user from database
        $stmt = $db->prepare("
            SELECT id, username, password_hash, role, email, full_name, is_active 
            FROM users 
            WHERE username = ? AND role = ?
        ");
        $stmt->execute([$username, $role]);
        $user = $stmt->fetch();

        if (!$user) {
            // Log failed attempt
            logAudit(null, 'LOGIN_FAILED', 'users', null, null, [
                'username' => $username,
                'role' => $role,
                'reason' => 'User not found'
            ]);

            jsonResponse(false, 'Invalid username or password', null, 401);
        }

        // Check if account is active
        if (!$user['is_active']) {
            jsonResponse(false, 'Your account has been deactivated. Please contact an administrator.', null, 403);
        }

        // Verify password
        if (!verifyPassword($password, $user['password_hash'])) {
            // Log failed attempt
            logAudit($user['id'], 'LOGIN_FAILED', 'users', $user['id'], null, [
                'username' => $username,
                'reason' => 'Invalid password'
            ]);

            jsonResponse(false, 'Invalid username or password', null, 401);
        }

        // Create session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days

            // Store token in database (you'd need a remember_tokens table for production)
        }

        // Log successful login
        logAudit($user['id'], 'LOGIN_SUCCESS', 'users', $user['id']);

        jsonResponse(true, 'Login successful', [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'full_name' => $user['full_name']
        ]);

    } catch (Exception $e) {
        error_log("Login Error: " . $e->getMessage());
        jsonResponse(false, 'An error occurred during login. Please try again.', null, 500);
    }
}

function handleLogout()
{
    if (isset($_SESSION['user_id'])) {
        logAudit($_SESSION['user_id'], 'LOGOUT', 'users', $_SESSION['user_id']);
    }

    // Clear session
    $_SESSION = [];

    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }

    session_destroy();

    jsonResponse(true, 'Logged out successfully');
}

function checkSession()
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }

    // Check session timeout (2 hours)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        handleLogout();
        jsonResponse(false, 'Session expired', null, 401);
    }

    // Update last activity
    $_SESSION['last_activity'] = time();

    jsonResponse(true, 'Session valid', [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'full_name' => $_SESSION['full_name']
    ]);
}
