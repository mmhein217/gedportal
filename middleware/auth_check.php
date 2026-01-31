<?php
/**
 * Authentication Middleware
 * Checks if user is logged in and has required role
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/../backend/config.php';

function requireAuth($allowedRoles = [])
{
    // Check if session exists
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_destroy();
        header('Location: ../login.php?timeout=1');
        exit;
    }

    // Update last activity
    $_SESSION['last_activity'] = time();

    // Check role if specified
    if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
        http_response_code(403);
        die('Access denied. Insufficient permissions.');
    }

    return true;
}

function getCurrentUser()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'full_name' => $_SESSION['full_name'],
        'email' => $_SESSION['email']
    ];
}

function isRole($role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function hasRole($roles)
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}
