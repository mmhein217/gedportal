<?php
/**
 * Debug Learning Time - Check database and API
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/backend/config.php';

echo "<h2>Debug Learning Time</h2>";
echo "<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:10px 0;} td,th{border:1px solid #ccc;padding:8px;} .success{color:green;} .error{color:red;}</style>";

try {
    $db = getDB();

    // Check if learning_analytics table exists
    echo "<h3>1. Check learning_analytics table:</h3>";
    try {
        $stmt = $db->query("DESCRIBE learning_analytics");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p class='success'>✓ Table exists with columns: " . implode(", ", $columns) . "</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Table missing! Creating it...</p>";

        // Create the table
        $db->exec("
            CREATE TABLE IF NOT EXISTS learning_analytics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                subject_id INT,
                activity_type ENUM('study', 'exam', 'review') DEFAULT 'study',
                score DECIMAL(5,2) DEFAULT NULL,
                time_spent_seconds INT DEFAULT 0,
                questions_attempted INT DEFAULT 0,
                correct_answers INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (subject_id) REFERENCES subjects(id)
            )
        ");
        echo "<p class='success'>✓ Created learning_analytics table</p>";
    }

    // Check data in the table
    echo "<h3>2. Check learning_analytics data:</h3>";
    $count = $db->query("SELECT COUNT(*) FROM learning_analytics")->fetchColumn();
    echo "<p>Records in table: <strong>$count</strong></p>";

    if ($count > 0) {
        $records = $db->query("SELECT la.*, u.full_name, s.name as subject_name 
            FROM learning_analytics la 
            LEFT JOIN users u ON la.user_id = u.id 
            LEFT JOIN subjects s ON la.subject_id = s.id 
            ORDER BY la.created_at DESC LIMIT 10")->fetchAll();
        echo "<table><tr><th>ID</th><th>User</th><th>Subject</th><th>Type</th><th>Time (sec)</th><th>Created</th></tr>";
        foreach ($records as $r) {
            echo "<tr><td>{$r['id']}</td><td>{$r['full_name']}</td><td>{$r['subject_name']}</td><td>{$r['activity_type']}</td><td>{$r['time_spent_seconds']}</td><td>{$r['created_at']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>No learning data yet. Use Study Mode to generate data.</p>";
    }

    // Check users count
    echo "<h3>3. Students in system:</h3>";
    $students = $db->query("SELECT id, full_name, email FROM users WHERE role = 'student' AND is_active = TRUE")->fetchAll();
    echo "<table><tr><th>ID</th><th>Name</th><th>Email</th></tr>";
    foreach ($students as $s) {
        echo "<tr><td>{$s['id']}</td><td>{$s['full_name']}</td><td>{$s['email']}</td></tr>";
    }
    echo "</table>";

    // Test the API
    echo "<h3>4. Test getLearningTime API:</h3>";
    $learningData = $db->query("
        SELECT 
            u.id,
            u.full_name,
            u.email,
            COALESCE(SUM(CASE WHEN la.activity_type = 'study' THEN la.time_spent_seconds ELSE 0 END), 0) as study_time,
            COALESCE(SUM(CASE WHEN la.activity_type = 'exam' THEN la.time_spent_seconds ELSE 0 END), 0) as exam_time,
            (SELECT COUNT(*) FROM exam_attempts ea WHERE ea.student_id = u.id AND ea.status = 'completed') as total_exams,
            (SELECT AVG(ea.score) FROM exam_attempts ea WHERE ea.student_id = u.id AND ea.status = 'completed') as avg_score
        FROM users u
        LEFT JOIN learning_analytics la ON u.id = la.user_id
        WHERE u.role = 'student' AND u.is_active = TRUE
        GROUP BY u.id
    ")->fetchAll();

    echo "<table><tr><th>Name</th><th>Study Time</th><th>Exam Time</th><th>Exams</th><th>Avg Score</th></tr>";
    foreach ($learningData as $s) {
        echo "<tr><td>{$s['full_name']}</td><td>{$s['study_time']}s</td><td>{$s['exam_time']}s</td><td>{$s['total_exams']}</td><td>" . round($s['avg_score'] ?? 0) . "%</td></tr>";
    }
    echo "</table>";

    // Add some test data
    echo "<h3>5. Add Test Study Data:</h3>";
    if (isset($_GET['add_test'])) {
        $student = $db->query("SELECT id FROM users WHERE role = 'student' LIMIT 1")->fetch();
        $subject = $db->query("SELECT id FROM subjects LIMIT 1")->fetch();

        if ($student && $subject) {
            $stmt = $db->prepare("INSERT INTO learning_analytics (user_id, subject_id, activity_type, time_spent_seconds, questions_attempted) VALUES (?, ?, 'study', 600, 10)");
            $stmt->execute([$student['id'], $subject['id']]);
            echo "<p class='success'>✓ Added 10 minutes study time for first student</p>";
        }
    } else {
        echo "<p><a href='?add_test=1'>Click to add 10 min test study data</a></p>";
    }

    echo "<h2 class='success'>Debug Complete!</h2>";
    echo "<p><a href='admin/analytics.php'>Go to Admin Analytics</a></p>";

} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
