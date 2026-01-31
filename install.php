<?php
/**
 * Automatic Database Setup Script
 * This script will create the database and load sample data automatically
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'exam_management';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - GED Exam System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #1f2937; margin-bottom: 1rem; }
        .step {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
            background: #f9fafb;
        }
        .success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        .error {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        .info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
        }
        pre {
            background: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
<div class='container'>
    <h1>🚀 Automatic Database Setup</h1>
";

try {
    // Step 1: Connect to MySQL
    echo "<div class='step info'><strong>Step 1:</strong> Connecting to MySQL server...</div>";
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='step success'>✓ Connected to MySQL successfully!</div>";

    // Step 2: Create database
    echo "<div class='step info'><strong>Step 2:</strong> Creating database '$dbname'...</div>";
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    $pdo->exec("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");
    echo "<div class='step success'>✓ Database '$dbname' created successfully!</div>";

    // Step 3: Load schema
    echo "<div class='step info'><strong>Step 3:</strong> Loading database schema...</div>";
    $schemaFile = __DIR__ . '/database/database_schema.sql';

    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }

    $schema = file_get_contents($schemaFile);

    // Remove USE database statement as we already selected it
    $schema = preg_replace('/USE\s+`?exam_management`?;/i', '', $schema);

    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));

    $tableCount = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
            if (stripos($statement, 'CREATE TABLE') !== false) {
                $tableCount++;
            }
        }
    }

    echo "<div class='step success'>✓ Schema loaded! Created $tableCount tables.</div>";

    // Step 4: Load seed data
    echo "<div class='step info'><strong>Step 4:</strong> Loading sample data...</div>";
    $seedFile = __DIR__ . '/database/seed_data.sql';

    if (!file_exists($seedFile)) {
        throw new Exception("Seed data file not found: $seedFile");
    }

    $seedData = file_get_contents($seedFile);

    // Remove USE database statement
    $seedData = preg_replace('/USE\s+`?exam_management`?;/i', '', $seedData);

    // Execute seed data
    $statements = array_filter(array_map('trim', explode(';', $seedData)));

    $insertCount = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
            if (stripos($statement, 'INSERT INTO') !== false) {
                $insertCount++;
            }
        }
    }

    echo "<div class='step success'>✓ Sample data loaded! Executed $insertCount insert statements.</div>";

    // Step 5: Verify
    echo "<div class='step info'><strong>Step 5:</strong> Verifying setup...</div>";

    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
    $questionCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM subjects");
    $subjectCount = $stmt->fetchColumn();

    echo "<div class='step success'>";
    echo "✓ Setup verified!<br>";
    echo "• Users: $userCount<br>";
    echo "• Questions: $questionCount<br>";
    echo "• Subjects: $subjectCount<br>";
    echo "</div>";

    // Success message
    echo "<div class='step success' style='margin-top: 2rem; padding: 2rem;'>";
    echo "<h2 style='margin-bottom: 1rem;'>🎉 Setup Complete!</h2>";
    echo "<p>Your database is ready. You can now login with:</p>";
    echo "<pre>";
    echo "Student:  student1 / password123\n";
    echo "Teacher:  teacher1 / password123\n";
    echo "Admin:    admin / password123";
    echo "</pre>";
    echo "<a href='login.php' class='btn'>Go to Login Page →</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='step error'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
    echo "<div class='step info'>";
    echo "<strong>Troubleshooting:</strong><br>";
    echo "• Make sure XAMPP MySQL is running<br>";
    echo "• Check that database files exist in /database/ folder<br>";
    echo "• Verify MySQL credentials (default: root with no password)";
    echo "</div>";
}

echo "</div></body></html>";
?>