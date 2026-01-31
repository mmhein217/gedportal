<?php
require_once __DIR__ . '/backend/config.php';
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM subjects");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h1>Database Subjects</h1><table border='1'><tr><th>ID</th><th>Name</th><th>Code</th></tr>";
    foreach ($subjects as $s) {
        echo "<tr><td>{$s['id']}</td><td>{$s['name']}</td><td>'{$s['code']}'</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
