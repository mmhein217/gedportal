<?php
// Debug script for questions API
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Questions API Debug</h1>";

// Start session and set user
session_start();
$_SESSION['user_id'] = 4; // student1
$_SESSION['role'] = 'student';

echo "<h2>Session Info:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test database connection
echo "<h2>Database Connection:</h2>";
define('APP_ACCESS', true);
require_once __DIR__ . '/backend/config.php';

try {
    $db = getDB();
    echo "<p style='color:green'>✅ Database connected!</p>";

    // Check subjects
    echo "<h2>Subjects in Database:</h2>";
    $stmt = $db->query("SELECT * FROM subjects");
    $subjects = $stmt->fetchAll();
    echo "<pre>";
    print_r($subjects);
    echo "</pre>";

    // Check questions count
    echo "<h2>Questions Count:</h2>";
    $stmt = $db->query("SELECT subject_id, COUNT(*) as count FROM questions GROUP BY subject_id");
    $counts = $stmt->fetchAll();
    echo "<pre>";
    print_r($counts);
    echo "</pre>";

    // Test getting math questions
    echo "<h2>Testing Math Questions:</h2>";
    $stmt = $db->prepare("SELECT id FROM subjects WHERE code = ?");
    $stmt->execute(['MATH']);
    $subject = $stmt->fetch();

    if ($subject) {
        echo "<p>Math subject ID: " . $subject['id'] . "</p>";

        $stmt = $db->prepare("SELECT * FROM questions WHERE subject_id = ? LIMIT 3");
        $stmt->execute([$subject['id']]);
        $questions = $stmt->fetchAll();

        echo "<p>Found " . count($questions) . " questions (showing first 3):</p>";
        echo "<pre>";
        print_r($questions);
        echo "</pre>";
    } else {
        echo "<p style='color:red'>❌ Math subject not found!</p>";
    }

    // Now test the actual API
    echo "<h2>Testing Questions API Endpoint:</h2>";
    $_GET['subject'] = 'math';

    ob_start();
    require __DIR__ . '/api/questions.php';
    $output = ob_get_clean();

    echo "<pre>";
    echo htmlspecialchars($output);
    echo "</pre>";

    $data = json_decode($output, true);
    if ($data && $data['success']) {
        echo "<p style='color:green; font-size:20px'>✅ API WORKING! Found " . count($data['data']['questions']) . " questions</p>";
    } else {
        echo "<p style='color:red; font-size:20px'>❌ API FAILED!</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>