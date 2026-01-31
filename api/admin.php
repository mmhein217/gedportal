<?php
require_once __DIR__ . '/../backend/config.php';
// session_start(); // handled in config.php

header('Content-Type: application/json');

// Use jsonResponse from config.php

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized', null, 401);
}

// Basic router
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_stats':
        getSystemStats();
        break;
    case 'get_questions':
        getAdminQuestions();
        break;
    case 'add_question':
        addQuestion();
        break;
    case 'update_question':
        updateQuestion();
        break;
    case 'delete_question':
        deleteQuestion();
        break;
    case 'get_users':
        getUsers();
        break;
    case 'add_user':
        addUser();
        break;
    case 'update_user':
        updateUser();
        break;
    case 'delete_user':
        deleteUser();
        break;
    case 'get_subjects':
        getSubjects();
        break;
    case 'update_subject':
        updateSubject();
        break;
    case 'get_analytics':
        getAnalytics();
        break;
    case 'get_audit_logs':
        getAuditLogs();
        break;
    case 'get_learning_time':
        getLearningTime();
        break;
    default:
        jsonResponse(false, 'Invalid action', null, 400);
}

function getSystemStats()
{
    try {
        $db = getDB();

        $stmt = $db->query("
            SELECT 
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM users WHERE role = 'student') as total_students,
                (SELECT COUNT(*) FROM users WHERE role = 'teacher') as total_teachers,
                (SELECT COUNT(*) FROM users WHERE role = 'admin') as total_admins
        ");
        $userStats = $stmt->fetch();

        $stmt = $db->query("SELECT COUNT(*) as total FROM exam_attempts WHERE status = 'completed'");
        $examStats = $stmt->fetch();

        $stmt = $db->query("SELECT COUNT(*) as total FROM questions WHERE is_active = TRUE");
        $questionStats = $stmt->fetch();

        jsonResponse(true, 'System statistics', [
            'total_users' => $userStats['total_users'],
            'total_students' => $userStats['total_students'],
            'total_teachers' => $userStats['total_teachers'],
            'total_admins' => $userStats['total_admins'],
            'total_exams' => $examStats['total'],
            'total_questions' => $questionStats['total']
        ]);
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

// ==================== QUESTIONS CRUD ====================
function getAdminQuestions()
{
    try {
        $db = getDB();
        $subject = $_GET['subject_id'] ?? '';
        $difficulty = $_GET['difficulty'] ?? '';

        $sql = "SELECT q.*, s.name as subject_name, s.code as subject_code 
                FROM questions q 
                LEFT JOIN subjects s ON q.subject_id = s.id 
                WHERE q.is_active = TRUE";
        $params = [];

        if ($subject) {
            $sql .= " AND q.subject_id = ?";
            $params[] = $subject;
        }
        if ($difficulty) {
            $sql .= " AND q.difficulty = ?";
            $params[] = $difficulty;
        }
        $sql .= " ORDER BY q.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll();

        // Decode JSON options
        foreach ($questions as &$q) {
            if (!empty($q['answer_options'])) {
                $q['answer_options'] = json_decode($q['answer_options'], true);
            }
        }

        jsonResponse(true, 'Questions retrieved', $questions);
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function addQuestion()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        $required = ['subject_id', 'question_text', 'question_type'];
        // Validate required fields
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                // Allow empty question_type default to multiple_choice if missing (though we check it)
                if ($field === 'question_type' && empty($_POST[$field])) {
                    $_POST['question_type'] = 'multiple_choice';
                } else {
                    jsonResponse(false, "$field is required", null, 400);
                }
            }
        }

        $answerOptions = isset($_POST['answer_options']) ? json_encode($_POST['answer_options']) : null;

        // Backward compatibility
        $optA = $_POST['option_a'] ?? '';
        $optB = $_POST['option_b'] ?? '';
        $optC = $_POST['option_c'] ?? '';
        $optD = $_POST['option_d'] ?? '';

        $stmt = $db->prepare("INSERT INTO questions (
            subject_id, question_text, question_type,
            option_a, option_b, option_c, option_d, 
            correct_answer, explanation, difficulty,
            answer_options, question_image_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['subject_id'],
            $_POST['question_text'],
            $_POST['question_type'],
            $optA,
            $optB,
            $optC,
            $optD,
            $_POST['correct_answer'] ?? '',
            $_POST['explanation'] ?? '',
            $_POST['difficulty'] ?? 'medium',
            $answerOptions,
            $_POST['image_url'] ?? null
        ]);
        jsonResponse(true, 'Question added');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function updateQuestion()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        if (empty($_POST['id']))
            jsonResponse(false, 'Question ID required', null, 400);

        $answerOptions = isset($_POST['answer_options']) ? json_encode($_POST['answer_options']) : null;

        $stmt = $db->prepare("UPDATE questions SET 
            subject_id=?, question_text=?, question_type=?,
            option_a=?, option_b=?, option_c=?, option_d=?, 
            correct_answer=?, explanation=?, difficulty=?,
            answer_options=?, question_image_url=?
            WHERE id=?");

        $stmt->execute([
            $_POST['subject_id'],
            $_POST['question_text'],
            $_POST['question_type'],
            $_POST['option_a'] ?? '',
            $_POST['option_b'] ?? '',
            $_POST['option_c'] ?? '',
            $_POST['option_d'] ?? '',
            $_POST['correct_answer'] ?? '',
            $_POST['explanation'] ?? '',
            $_POST['difficulty'] ?? 'medium',
            $answerOptions,
            $_POST['image_url'] ?? null,
            $_POST['id']
        ]);
        jsonResponse(true, 'Question updated');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function deleteQuestion()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        if (empty($_POST['id']))
            jsonResponse(false, 'Question ID required', null, 400);
        $stmt = $db->prepare("UPDATE questions SET is_active = FALSE WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        jsonResponse(true, 'Question deleted');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

// ==================== USERS CRUD ====================
function getUsers()
{
    try {
        $db = getDB();
        $role = $_GET['role'] ?? '';

        $sql = "SELECT id, username, full_name, email, role, is_active, created_at FROM users WHERE 1=1";
        $params = [];

        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        $sql .= " ORDER BY id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(true, 'Users retrieved', $stmt->fetchAll());
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function addUser()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        $required = ['username', 'password', 'full_name', 'email', 'role'];
        foreach ($required as $field) {
            if (empty($_POST[$field]))
                jsonResponse(false, "$field is required", null, 400);
        }

        // Check username exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        if ($stmt->fetch())
            jsonResponse(false, 'Username already exists', null, 400);

        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password_hash, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['username'], $passwordHash, $_POST['full_name'], $_POST['email'], $_POST['role']]);
        jsonResponse(true, 'User created');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function updateUser()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        if (empty($_POST['id']))
            jsonResponse(false, 'User ID required', null, 400);

        $sql = "UPDATE users SET full_name=?, email=?, role=?, is_active=? WHERE id=?";
        $params = [$_POST['full_name'], $_POST['email'], $_POST['role'], $_POST['is_active'] ?? 1, $_POST['id']];

        // Update password if provided
        if (!empty($_POST['password'])) {
            $sql = "UPDATE users SET full_name=?, email=?, role=?, is_active=?, password_hash=? WHERE id=?";
            $params = [$_POST['full_name'], $_POST['email'], $_POST['role'], $_POST['is_active'] ?? 1, password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['id']];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(true, 'User updated');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function deleteUser()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        if (empty($_POST['id']))
            jsonResponse(false, 'User ID required', null, 400);

        // Don't allow deleting yourself
        if ($_POST['id'] == $_SESSION['user_id']) {
            jsonResponse(false, 'Cannot delete your own account', null, 400);
        }

        $stmt = $db->prepare("UPDATE users SET is_active = FALSE WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        jsonResponse(true, 'User deactivated');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

// ==================== SUBJECTS/SETTINGS ====================
function getSubjects()
{
    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM subjects ORDER BY id");
        jsonResponse(true, 'Subjects retrieved', $stmt->fetchAll());
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function updateSubject()
{
    try {
        $db = getDB();
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input)
            $_POST = array_merge($_POST, $input);

        if (empty($_POST['id']))
            jsonResponse(false, 'Subject ID required', null, 400);

        $stmt = $db->prepare("UPDATE subjects SET name=?, duration_minutes=?, passing_score=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['duration_minutes'], $_POST['passing_score'], $_POST['id']]);
        jsonResponse(true, 'Subject updated');
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

// ==================== ANALYTICS ====================
function getAnalytics()
{
    try {
        $db = getDB();

        // Overall stats
        $overall = $db->query("
            SELECT 
                COUNT(*) as total_attempts,
                AVG(score) as avg_score,
                MAX(score) as highest_score,
                MIN(score) as lowest_score
            FROM exam_attempts WHERE status = 'completed'
        ")->fetch();

        // By subject
        $bySubject = $db->query("
            SELECT s.name, COUNT(ea.id) as attempts, AVG(ea.score) as avg_score
            FROM exam_attempts ea
            JOIN subjects s ON ea.subject_id = s.id
            WHERE ea.status = 'completed'
            GROUP BY s.id
        ")->fetchAll();

        // Recent attempts
        $recent = $db->query("
            SELECT ea.*, u.full_name, s.name as subject_name
            FROM exam_attempts ea
            JOIN users u ON ea.student_id = u.id
            JOIN subjects s ON ea.subject_id = s.id
            WHERE ea.status = 'completed'
            ORDER BY ea.end_time DESC LIMIT 10
        ")->fetchAll();

        jsonResponse(true, 'Analytics data', [
            'overall' => $overall,
            'by_subject' => $bySubject,
            'recent_attempts' => $recent
        ]);
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

// ==================== AUDIT LOGS ====================
function getAuditLogs()
{
    try {
        $db = getDB();
        $limit = $_GET['limit'] ?? 50;

        $stmt = $db->prepare("
            SELECT al.*, u.full_name 
            FROM audit_log al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([(int) $limit]);
        jsonResponse(true, 'Audit logs retrieved', $stmt->fetchAll());
    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

// ==================== LEARNING TIME ====================
function getLearningTime()
{
    try {
        $db = getDB();
        $period = $_GET['period'] ?? 'all';

        // Build date filter
        $dateFilter = '';
        $examDateFilter = '';
        switch ($period) {
            case 'today':
                $dateFilter = "AND DATE(la.session_start) = CURDATE()";
                $examDateFilter = "AND DATE(ea.start_time) = CURDATE()";
                break;
            case '7days':
                $dateFilter = "AND la.session_start >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                $examDateFilter = "AND ea.start_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            default:
                $dateFilter = '';
                $examDateFilter = '';
        }

        // Get learning time for each student
        $sql = "
            SELECT 
                u.id,
                u.full_name,
                u.email,
                COALESCE((SELECT SUM(la.time_spent_seconds) FROM learning_analytics la WHERE la.student_id = u.id $dateFilter), 0) as study_time,
                COALESCE((SELECT SUM(ea.time_spent_seconds) FROM exam_attempts ea WHERE ea.student_id = u.id AND ea.status = 'completed' $examDateFilter), 0) as exam_time,
                (SELECT COUNT(*) FROM exam_attempts ea WHERE ea.student_id = u.id AND ea.status = 'completed' $examDateFilter) as total_exams,
                (SELECT AVG(ea.score) FROM exam_attempts ea WHERE ea.student_id = u.id AND ea.status = 'completed' $examDateFilter) as avg_score
            FROM users u
            WHERE u.role = 'student' AND u.is_active = TRUE
            ORDER BY u.full_name
        ";
        $students = $db->query($sql)->fetchAll();

        // Get totals for period
        $totalSql = "SELECT SUM(time_spent_seconds) FROM learning_analytics WHERE 1=1 " . str_replace('la.', '', $dateFilter);
        $totalStudyTime = $db->query($totalSql)->fetchColumn() ?? 0;

        $activeCount = count(array_filter($students, fn($s) => ($s['study_time'] + $s['exam_time']) > 0));

        jsonResponse(true, 'Learning time retrieved', [
            'students' => $students,
            'total_study_time' => (int) $totalStudyTime,
            'active_count' => $activeCount,
            'period' => $period
        ]);

    } catch (Exception $e) {
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}