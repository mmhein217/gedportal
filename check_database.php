<?php
/**
 * Database Structure Fix Script - Complete Version
 * Fixes all column naming issues
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/backend/config.php';

echo "<h2>Database Structure Fix</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} table{border-collapse:collapse;margin:10px 0;} td,th{border:1px solid #ccc;padding:8px;}</style>";

try {
    $db = getDB();

    // Check exam_attempts structure
    $stmt = $db->query("DESCRIBE exam_attempts");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>Current exam_attempts columns:</h3>";
    echo "<pre>" . implode(", ", $columns) . "</pre>";

    $changes = [];

    // Add subject_id if missing
    if (!in_array('subject_id', $columns)) {
        $db->exec("ALTER TABLE exam_attempts ADD COLUMN subject_id INT AFTER student_id");
        $changes[] = "Added subject_id column";

        // Try to populate from exams table if it exists
        try {
            $db->exec("
                UPDATE exam_attempts ea
                SET subject_id = (SELECT subject_id FROM exams e WHERE e.id = ea.exam_id)
                WHERE ea.subject_id IS NULL AND ea.exam_id IS NOT NULL
            ");
            $changes[] = "Populated subject_id from exams table";
        } catch (Exception $e) {
            // exams table might not exist or be empty
        }
    }

    // Add foreign key if not exists
    try {
        $db->exec("ALTER TABLE exam_attempts ADD FOREIGN KEY (subject_id) REFERENCES subjects(id)");
    } catch (Exception $e) {
        // Foreign key might already exist
    }

    foreach ($changes as $change) {
        echo "<p class='success'>✓ $change</p>";
    }

    if (empty($changes)) {
        echo "<p class='success'>✓ Database structure is already correct</p>";
    }

    // Check subjects table
    echo "<h3>Subjects:</h3>";
    $subjects = $db->query("SELECT * FROM subjects")->fetchAll();
    echo "<table><tr><th>ID</th><th>Code</th><th>Name</th><th>Duration</th><th>Passing</th><th>Total Q</th></tr>";
    foreach ($subjects as $s) {
        echo "<tr><td>{$s['id']}</td><td>{$s['code']}</td><td>{$s['name']}</td>";
        echo "<td>" . ($s['duration_minutes'] ?? '90') . "</td>";
        echo "<td>" . ($s['passing_score'] ?? '70') . "%</td>";
        echo "<td>" . ($s['total_questions'] ?? '?') . "</td></tr>";
    }
    echo "</table>";

    // Check questions count
    echo "<h3>Questions per Subject:</h3>";
    $counts = $db->query("
        SELECT s.name, s.code, COUNT(q.id) as count 
        FROM subjects s 
        LEFT JOIN questions q ON s.id = q.subject_id AND q.is_active = TRUE
        GROUP BY s.id ORDER BY s.id
    ")->fetchAll();
    echo "<table><tr><th>Subject</th><th>Code</th><th>Questions</th></tr>";
    foreach ($counts as $c) {
        $color = $c['count'] > 0 ? 'green' : 'red';
        echo "<tr><td>{$c['name']}</td><td>{$c['code']}</td><td style='color:$color'>{$c['count']}</td></tr>";
    }
    echo "</table>";

    // Check users
    echo "<h3>Users:</h3>";
    $users = $db->query("SELECT id, username, full_name, role, is_active FROM users ORDER BY role, id")->fetchAll();
    echo "<table><tr><th>ID</th><th>Username</th><th>Name</th><th>Role</th><th>Active</th></tr>";
    foreach ($users as $u) {
        $active = $u['is_active'] ? '✓' : '✗';
        echo "<tr><td>{$u['id']}</td><td>{$u['username']}</td><td>{$u['full_name']}</td><td>{$u['role']}</td><td>$active</td></tr>";
    }
    echo "</table>";

    echo "<h2 class='success'>✓ Database check complete!</h2>";
    echo "<p><strong>Next step:</strong> <a href='login.php'>Go to Login</a></p>";
    echo "<p>Login as <code>admin</code> / <code>password123</code></p>";

} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
