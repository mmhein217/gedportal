<?php
/**
 * Fix Password Hashes
 * This script regenerates password hashes for all users
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/backend/config.php';

try {
    $db = getDB();
    
    // The password we want to set for all users
    $password = 'password123';
    
    // Generate a proper bcrypt hash
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    echo "Generated hash: " . $hash . "\n\n";
    
    // Update all users with the new hash
    $stmt = $db->prepare("UPDATE users SET password_hash = ?");
    $stmt->execute([$hash]);
    
    echo "✅ Password hashes updated successfully!\n";
    echo "Affected rows: " . $stmt->rowCount() . "\n\n";
    
    // Verify the hash works
    if (password_verify($password, $hash)) {
        echo "✅ Password verification test: PASSED\n";
    } else {
        echo "❌ Password verification test: FAILED\n";
    }
    
    // Show all users
    echo "\n📋 Current users:\n";
    $users = $db->query("SELECT username, role FROM users")->fetchAll();
    foreach ($users as $user) {
        echo "  - {$user['username']} ({$user['role']})\n";
    }
    
    echo "\n🔑 All users now have password: password123\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
