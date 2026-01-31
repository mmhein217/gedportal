<?php
require_once 'backend/config.php';

try {
    $db = getDB();

    // Add columns if they don't exist
    $columns = [
        "payment_status" => "ALTER TABLE users ADD COLUMN payment_status ENUM('free', 'paid') DEFAULT 'free'",
        "question_type" => "ALTER TABLE questions ADD COLUMN question_type ENUM('multiple_choice', 'true_false', 'dropdown', 'drag_drop') DEFAULT 'multiple_choice'",
        "answer_options" => "ALTER TABLE questions ADD COLUMN answer_options TEXT DEFAULT NULL", // Using TEXT for compatibility, will store JSON
        "question_image_url" => "ALTER TABLE questions ADD COLUMN question_image_url VARCHAR(255) DEFAULT NULL",
        "explanation_image_url" => "ALTER TABLE questions ADD COLUMN explanation_image_url VARCHAR(255) DEFAULT NULL"
    ];

    foreach ($columns as $col => $sql) {
        try {
            $db->query("SELECT $col FROM questions LIMIT 1");
        } catch (Exception $e) {
            // Column doesn't exist, create it
            $db->exec($sql);
            echo "Added column $col to questions table.\n";
        }
    }

    // Modify correct_answer to be TEXT (remove ENUM constraint)
    try {
        $db->exec("ALTER TABLE questions MODIFY COLUMN correct_answer TEXT");
        echo "Modified correct_answer column to TEXT.\n";
    } catch (Exception $e) {
        echo "Error modifying correct_answer: " . $e->getMessage() . "\n";
    }

    // Force update question_type to multiple_choice for existing questions
    $db->exec("UPDATE questions SET question_type = 'multiple_choice' WHERE question_type IS NULL");

    echo "Database schema updated successfully.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
