<?php
require_once __DIR__ . '/backend/config.php';
$db = getDB();
$stmt = $db->query("DESCRIBE learning_analytics");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n\nDESCRIBE exam_attempts:\n";
$stmt = $db->query("DESCRIBE exam_attempts");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
