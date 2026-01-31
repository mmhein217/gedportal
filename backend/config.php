<?php
ob_start(); // Buffer all output to prevent stray characters/notices
/**
 * Database Configuration
 * XAMPP Local Server Setup
 */

// Prevent direct access
if (!defined('APP_ACCESS')) {
    define('APP_ACCESS', true);
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'exam_management');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'GED Exam Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Pearson');

// Session Configuration
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('SESSION_NAME', 'exam_session');

// Security Configuration
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);

// Exam Security Settings
define('MAX_VIOLATIONS_ALLOWED', 5);
define('FULLSCREEN_REQUIRED', true);
define('KEYBOARD_BLOCKING_ENABLED', true);

// Timezone
date_default_timezone_set('Asia/Yangon');

// Error Reporting (Production Settings for API)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Crucial: Do not echo errors to output
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_error.log');

// Database Connection Class
class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed. Please ensure XAMPP is running.'
            ]));
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Prevent cloning
    private function __clone()
    {
    }

    // Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Helper Functions
function getDB()
{
    return Database::getInstance()->getConnection();
}

function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function jsonResponse($success, $message, $data = null, $httpCode = 200)
{
    // Clear any previous output (warnings, whitespace, etc.)
    if (ob_get_length())
        ob_clean();

    http_response_code($httpCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function logAudit($userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null)
{
    try {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO audit_log (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $action,
            $tableName,
            $recordId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        error_log("Audit Log Error: " . $e->getMessage());
    }
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
}

function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start([
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_httponly' => true,
        'cookie_secure' => false, // Set to true in production with HTTPS
        'use_strict_mode' => true
    ]);
}
