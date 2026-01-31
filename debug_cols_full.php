<?php
require_once __DIR__ . '/backend/config.php';
$db = getDB();
echo "--- learning_analytics ---\n";
$stmt = $db->query("DESCRIBE learning_analytics");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo $col['Field'] . "\n";
}
echo "\n--- exam_attempts ---\n";
$stmt = $db->query("DESCRIBE exam_attempts");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo $col['Field'] . "\n";
}
