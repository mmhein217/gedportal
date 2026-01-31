<?php
/**
 * Questions API
 * Handles question retrieval with shuffling and secure option handling
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/../backend/config.php';

header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

$action = $_GET['action'] ?? 'get_questions';

switch ($action) {
    case 'get_questions':
        getQuestions();
        break;
    default:
        jsonResponse(false, 'Invalid action', null, 400);
}

function getQuestions()
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated', null, 401);
    }

    $subject = $_GET['subject'] ?? '';
    $shuffle = isset($_GET['shuffle']) && $_GET['shuffle'] === 'true';
    $year = $_GET['year'] ?? null;

    if (empty($subject)) {
        jsonResponse(false, 'Subject is required', null, 400);
    }

    try {
        $db = getDB();

        // Get subject info
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

        $stmt = $db->prepare("SELECT id, duration_minutes FROM subjects WHERE code = ?");
        $stmt->execute([$subjectCode]);
        $subjectInfo = $stmt->fetch();

        if (!$subjectInfo) {
            jsonResponse(false, 'Invalid subject', null, 400);
        }

        // Get questions
        $query = "
            SELECT id, question_text, question_type, answer_options, question_image_url,
                   option_a, option_b, option_c, option_d
            FROM questions
            WHERE subject_id = ? AND is_active = TRUE
        ";

        $params = [$subjectInfo['id']];
        if ($year) {
            $query .= " AND year = ?";
            $params[] = $year;
        }

        if ($shuffle)
            $query .= " ORDER BY RAND()";
        else
            $query .= " ORDER BY id";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $questions = $stmt->fetchAll();

        // Process questions for exam mode (Sanitize answers)
        $questionsForExam = array_map(function ($q) {
            $processed = [
                'id' => $q['id'],
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'] ?? 'multiple_choice',
                'question_image_url' => $q['question_image_url'],
            ];

            // Handle Answer Options based on Type
            if ($q['question_type'] === 'dropdown') {
                $opts = json_decode($q['answer_options'], true);
                // Strip 'correct' field
                if (is_array($opts)) {
                    foreach ($opts as &$group) {
                        unset($group['correct']);
                    }
                }
                $processed['answer_options'] = $opts;
            } else if ($q['question_type'] === 'drag_drop') {
                $opts = json_decode($q['answer_options'], true);
                if (is_array($opts) && isset($opts['items'])) {
                    shuffle($opts['items']); // Shuffle items for sorting
                    $processed['answer_options'] = ['items' => $opts['items'], 'type' => 'ordering'];
                } else {
                    $processed['answer_options'] = ['items' => [], 'type' => 'ordering'];
                }
            } else {
                // Multiple Choice (Default)
                $processed['option_a'] = $q['option_a'];
                $processed['option_b'] = $q['option_b'];
                $processed['option_c'] = $q['option_c'];
                $processed['option_d'] = $q['option_d'];
            }

            return $processed;
        }, $questions);

        jsonResponse(true, 'Questions retrieved successfully', [
            'questions' => $questionsForExam,
            'time_limit' => $subjectInfo['duration_minutes'],
            'total_questions' => count($questions)
        ]);

    } catch (Exception $e) {
        error_log("Questions API Error: " . $e->getMessage());
        jsonResponse(false, 'Error retrieving questions', null, 500);
    }
}
