<?php
/**
 * Analytics API - Uses student_id (matching actual DB schema)
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/../backend/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'student_stats':
        getStudentStats();
        break;
    case 'subject_analytics':
        getSubjectAnalytics();
        break;
    case 'all_students':
        getAllStudentsAnalytics();
        break;
    default:
        jsonResponse(false, 'Invalid action', null, 400);
}

function getStudentStats()
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }

    try {
        $db = getDB();

        $stmt = $db->prepare("
            SELECT COUNT(*) as total_exams, AVG(score) as avg_score,
                   SUM(correct_answers) as total_correct, SUM(total_questions) as total_questions,
                   SUM(time_spent_seconds) as total_time
            FROM exam_attempts WHERE student_id = ? AND status = 'completed'
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $stats = $stmt->fetch();

        jsonResponse(true, 'Stats retrieved', [
            'total_exams' => (int) ($stats['total_exams'] ?? 0),
            'avg_score' => round($stats['avg_score'] ?? 0, 2),
            'study_time' => (int) ($stats['total_time'] ?? 0),
            'questions_answered' => (int) ($stats['total_questions'] ?? 0)
        ]);

    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function getSubjectAnalytics()
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }

    try {
        $db = getDB();
        $subjectId = $_GET['subject_id'] ?? 0;

        $stmt = $db->prepare("
            SELECT COUNT(*) as attempts, AVG(score) as avg_score, MAX(score) as best_score, SUM(time_spent_seconds) as total_time
            FROM exam_attempts WHERE student_id = ? AND subject_id = ? AND status = 'completed'
        ");
        $stmt->execute([$_SESSION['user_id'], $subjectId]);
        $stats = $stmt->fetch();

        jsonResponse(true, 'Analytics retrieved', [
            'attempts' => (int) ($stats['attempts'] ?? 0),
            'avg_score' => round($stats['avg_score'] ?? 0, 2),
            'best_score' => round($stats['best_score'] ?? 0, 2),
            'total_time' => (int) ($stats['total_time'] ?? 0)
        ]);

    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function getAllStudentsAnalytics()
{
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teacher', 'admin'])) {
        jsonResponse(false, 'Access denied', null, 403);
    }

    try {
        $db = getDB();

        $stmt = $db->query("
            SELECT u.id, u.full_name, u.email, COUNT(DISTINCT ea.id) as total_exams,
                   AVG(ea.score) as avg_score, SUM(ea.time_spent_seconds) as study_time, MAX(ea.end_time) as last_exam
            FROM users u
            LEFT JOIN exam_attempts ea ON u.id = ea.student_id AND ea.status = 'completed'
            WHERE u.role = 'student' AND u.is_active = TRUE
            GROUP BY u.id ORDER BY u.full_name
        ");

        jsonResponse(true, 'Analytics retrieved', ['students' => $stmt->fetchAll()]);

    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}
