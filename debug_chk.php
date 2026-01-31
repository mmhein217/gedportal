<?php
require_once __DIR__ . '/backend/config.php';
$db = getDB();

echo "Checking 'learning_analytics' columns...\n";
$cols = $db->query("DESCRIBE learning_analytics")->fetchAll(PDO::FETCH_COLUMN);
if (in_array('user_id', $cols))
    echo " - Found 'user_id'\n";
if (in_array('student_id', $cols))
    echo " - Found 'student_id'\n";

echo "\nChecking 'exam_attempts' columns...\n";
$cols = $db->query("DESCRIBE exam_attempts")->fetchAll(PDO::FETCH_COLUMN);
if (in_array('user_id', $cols))
    echo " - Found 'user_id'\n";
if (in_array('student_id', $cols))
    echo " - Found 'student_id'\n";
