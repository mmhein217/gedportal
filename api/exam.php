<?php
/**
 * Exam API
 * Handles exam attempts, submissions, and violations with support for advanced question types
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/../backend/config.php';

header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'start_attempt':
        startAttempt($input);
        break;
    case 'submit_exam':
        submitExam($input);
        break;
    case 'record_violation':
        recordViolation($input);
        break;
    default:
        jsonResponse(false, 'Invalid action', null, 400);
}

function startAttempt($data)
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }

    $subject = $data['subject'] ?? '';
    if (empty($subject)) {
        jsonResponse(false, 'Subject is required', null, 400);
    }

    try {
        $db = getDB();

        // Extended mapping to match frontend slugs
        $slug = strtolower(preg_replace('/[^a-z0-9_]/', '', $subject));
        $subjectMap = [
            'math' => 'MATH',
            'mathematics' => 'MATH',
            'lang' => 'LANG',
            'language' => 'LANG',
            'rl' => 'LANG',
            'reasoning' => 'LANG',
            'language_arts' => 'LANG',
            'reasoning_language_arts' => 'LANG',
            'sci' => 'SCI',
            'science' => 'SCI',
            'soc' => 'SOC',
            'social' => 'SOC',
            'social_studies' => 'SOC'
        ];
        $subjectCode = $subjectMap[$slug] ?? strtoupper($subject);

        // Get subject info
        $stmt = $db->prepare("SELECT id, total_questions, duration_minutes FROM subjects WHERE code = ?");
        $stmt->execute([$subjectCode]);
        $subjectInfo = $stmt->fetch();

        if (!$subjectInfo) {
            // DEBUG Output for user
            jsonResponse(false, "Invalid subject. Input: '$subject', Slug: '$slug', Mapped: '$subjectCode'", null, 400);
        }

        // Count actual questions
        $stmt = $db->prepare("SELECT COUNT(*) FROM questions WHERE subject_id = ? AND is_active = TRUE");
        $stmt->execute([$subjectInfo['id']]);
        $questionCount = $stmt->fetchColumn();

        // Check if exams table has an active exam, or create one
        $stmt = $db->prepare("SELECT id FROM exams WHERE subject_id = ? AND is_active = TRUE LIMIT 1");
        $stmt->execute([$subjectInfo['id']]);
        $exam = $stmt->fetch();

        if (!$exam) {
            // Create default exam
            $stmt = $db->prepare("INSERT INTO exams (subject_id, created_by, exam_name, exam_mode, is_active) VALUES (?, ?, ?, 'timed', TRUE)");
            $stmt->execute([$subjectInfo['id'], $_SESSION['user_id'], ucfirst($subject) . ' Exam']);
            $examId = $db->lastInsertId();
        } else {
            $examId = $exam['id'];
        }

        // Create exam attempt
        $stmt = $db->prepare("
            INSERT INTO exam_attempts 
            (exam_id, student_id, subject_id, start_time, total_questions, status, ip_address, user_agent)
            VALUES (?, ?, ?, NOW(), ?, 'in_progress', ?, ?)
        ");

        $stmt->execute([
            $examId,
            $_SESSION['user_id'],
            $subjectInfo['id'],
            $questionCount,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        $attemptId = $db->lastInsertId();

        try {
            logAudit($_SESSION['user_id'], 'EXAM_STARTED', 'exam_attempts', $attemptId);
        } catch (Exception $e) {
        }

        jsonResponse(true, 'Exam started', ['attempt_id' => $attemptId, 'subject_id' => $subjectInfo['id']]);

    } catch (Exception $e) {
        error_log("Start Attempt Error: " . $e->getMessage());
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function submitExam($data)
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }

    $attemptId = $data['attempt_id'] ?? 0;
    $answers = $data['answers'] ?? []; // Expected: map of question_id => answer
    $timeSpent = $data['time_spent'] ?? 0;
    $violations = $data['violations'] ?? 0;

    try {
        $db = getDB();

        // Get attempt
        $stmt = $db->prepare("SELECT * FROM exam_attempts WHERE id = ? AND student_id = ?");
        $stmt->execute([$attemptId, $_SESSION['user_id']]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            jsonResponse(false, 'Attempt not found', null, 404);
        }

        $subjectId = $attempt['subject_id'];

        // Get questions
        $stmt = $db->prepare("SELECT id, question_type, correct_answer, answer_options FROM questions WHERE subject_id = ? AND is_active = TRUE");
        $stmt->execute([$subjectId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Index questions by ID
        $questionsById = [];
        foreach ($questions as $q) {
            $questionsById[$q['id']] = $q;
        }

        $correct = 0;
        $incorrect = 0;
        $detailedAnswers = [];

        foreach ($answers as $qId => $studentAnswer) {
            if (!isset($questionsById[$qId]))
                continue;

            $q = $questionsById[$qId];
            $type = $q['question_type'] ?? 'multiple_choice';
            $isCorrect = false;

            if ($type === 'multiple_choice') {
                $isCorrect = (strtoupper((string) $studentAnswer) === strtoupper((string) $q['correct_answer']));
            } else if ($type === 'dropdown') {
                $opts = json_decode($q['answer_options'], true);
                if (is_array($opts)) {
                    $allMatch = true;
                    if (is_array($studentAnswer)) {
                        foreach ($opts as $group) {
                            $gId = $group['id'];
                            $expected = $group['correct'];
                            $actual = $studentAnswer[$gId] ?? '';
                            if ($actual !== $expected) {
                                $allMatch = false;
                                break;
                            }
                        }
                    } else {
                        $allMatch = false;
                    }
                    $isCorrect = $allMatch;
                }
            } else if ($type === 'drag_drop') {
                $opts = json_decode($q['answer_options'], true);
                $correctItems = $opts['items'] ?? [];
                // Check if arrays are identical
                if (is_array($studentAnswer) && is_array($correctItems)) {
                    $isCorrect = ($studentAnswer === $correctItems);
                }
            }

            if ($isCorrect)
                $correct++;
            else
                $incorrect++;

            // Preserve results for display
            $detailedAnswers[$qId] = [
                'student_answer' => $studentAnswer,
                'correct_answer' => $q['correct_answer'] ?? '(Complex)',
                'is_correct' => $isCorrect
            ];
        }

        $totalQuestions = count($questions);
        $unanswered = $totalQuestions - count($answers);
        $score = $totalQuestions > 0 ? ($correct / $totalQuestions) * 100 : 0;

        // Update attempt
        $stmt = $db->prepare("
            UPDATE exam_attempts SET 
                end_time = NOW(), time_spent_seconds = ?, score = ?, 
                correct_answers = ?, incorrect_answers = ?, unanswered = ?,
                answers_json = ?, violations_count = ?, status = 'completed'
            WHERE id = ?
        ");
        $stmt->execute([$timeSpent, $score, $correct, $incorrect, $unanswered, json_encode($detailedAnswers), $violations, $attemptId]);

        // Analytics
        try {
            $stmt = $db->prepare("INSERT INTO learning_analytics (student_id, subject_id, time_spent_seconds, questions_answered, session_date) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $subjectId, $timeSpent, count($answers)]);
        } catch (Exception $e) {
        }

        try {
            logAudit($_SESSION['user_id'], 'EXAM_SUBMITTED', 'exam_attempts', $attemptId, "Score: $score%");
        } catch (Exception $e) {
        }

        jsonResponse(true, 'Exam submitted', ['score' => round($score, 2), 'correct' => $correct]);

    } catch (Exception $e) {
        error_log("Submit Error: " . $e->getMessage());
        jsonResponse(false, 'Error: ' . $e->getMessage(), null, 500);
    }
}

function recordViolation($data)
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE exam_attempts SET violations_count = violations_count + 1 WHERE id = ? AND student_id = ?");
        $stmt->execute([$data['attempt_id'] ?? 0, $_SESSION['user_id']]);
        jsonResponse(true, 'Violation recorded');
    } catch (Exception $e) {
        jsonResponse(false, 'Error', null, 500);
    }
}
