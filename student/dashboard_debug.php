<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../middleware/auth_check.php';
// requireAuth(['student']); // Temporarily disable to test without session if needed, but we likely need session.

echo "<h1>Debug Dashboard</h1>";

if (session_status() === PHP_SESSION_NONE)
    session_start();
echo "Session Status: " . session_status() . "<br>";
if (isset($_SESSION)) {
    echo "Session Data: <pre>" . print_r($_SESSION, true) . "</pre>";
} else {
    echo "No Session Data<br>";
}

try {
    $db = getDB();
    echo "DB Connection Successful<br>";

    $stmt = $db->query("SELECT * FROM subjects ORDER BY id");
    if (!$stmt) {
        echo "Query Failed: " . print_r($db->errorInfo(), true) . "<br>";
    } else {
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Subjects Count: " . count($subjects) . "<br>";
        echo "<pre>" . print_r($subjects, true) . "</pre>";
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
}
?>