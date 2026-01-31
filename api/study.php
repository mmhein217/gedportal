<?php
/**
 * Study API - Logs study time and activity
 * Uses student_id column (matching actual DB schema)
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/../backend/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Not authenticated', null, 401);
}

switch ($action) {
    case 'log_study_time':
        logStudyTime($input);
        break;
    case 'get_study_stats':
        getStudyStats();
        break;
    default:
        jsonResponse(false, 'Invalid action', null, 400);
}

function logStudyTime($data)
{
    $subjectId = $data['subject_id'] ?? 0;
    $timeSpent = $data['time_spent'] ?? 0;
    $questionsViewed = $data['questions_viewed'] ?? 0;

    if ($timeSpent <= 0) {
        jsonResponse(true, 'No time to log');
        return;
    }

    try {
        $db = getDB();

        // Insert learning analytics record (uses student_id)
        $stmt = $db->prepare("
            INSERT INTO learning_analytics 
            (student_id, subject_id, time_spent_seconds, questions_viewed, session_start, session_end)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $subjectId,
            $timeSpent,
            $questionsViewed
        ]);

        jsonResponse(true, 'Study time logged', ['logged_seconds' => $timeSpent]);

    } catch (Exception $e) {
        error_log("Log Study Time Error: " . $e->getMessage());
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function getStudyStats()
{
    try {
        $db = getDB();

        // Get study time by subject (uses student_id)
        $stmt = $db->prepare("
            SELECT s.name as subject_name, s.code,
                   SUM(la.time_spent_seconds) as total_time,
                   SUM(la.questions_viewed) as total_questions
            FROM learning_analytics la
            JOIN subjects s ON la.subject_id = s.id
            WHERE la.student_id = ?
            GROUP BY s.id
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $bySubject = $stmt->fetchAll();

        // Get total study time
        $stmt = $db->prepare("
            SELECT SUM(time_spent_seconds) as total_time
            FROM learning_analytics
            WHERE student_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $total = $stmt->fetch();

        jsonResponse(true, 'Study stats retrieved', [
            'total_study_time' => (int) ($total['total_time'] ?? 0),
            'by_subject' => $bySubject
        ]);

    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}
