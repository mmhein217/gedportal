<?php
require_once 'backend/config.php';
try {
    $db = getDB();
    $stmt = $db->query("DESCRIBE questions");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
