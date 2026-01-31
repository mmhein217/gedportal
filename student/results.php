<?php
/**
 * Exam Results - Detailed breakdown
 * Updates: Supports new question types, HTML rendering, and correct URL slugs
 */
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['student']);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$user = getCurrentUser();
$attemptId = $_GET['attempt_id'] ?? 0;

try {
    $db = getDB();

    // Fetch Attempt
    $stmt = $db->prepare("
        SELECT ea.*, s.name as subject_name, s.code as subject_code, s.passing_score
        FROM exam_attempts ea
        JOIN subjects s ON ea.subject_id = s.id
        WHERE ea.id = ? AND ea.student_id = ?
    ");
    $stmt->execute([$attemptId, $user['id']]);
    $attempt = $stmt->fetch();

    if (!$attempt) {
        header('Location: dashboard.php');
        exit;
    }

    $recordedAnswers = json_decode($attempt['answers_json'], true) ?? [];

    // Fetch Questions
    // Note: We select * to get question_type and everything
    $stmt = $db->prepare("SELECT * FROM questions WHERE subject_id = ? AND is_active = TRUE ORDER BY id");
    $stmt->execute([$attempt['subject_id']]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Re-map questions by ID for easy lookup
    $qMap = [];
    foreach ($questions as $q) {
        $qMap[$q['id']] = $q;
    }

} catch (Exception $e) {
    header('Location: dashboard.php');
    exit;
}

$passingScore = $attempt['passing_score'] ?? 70;
$isPassing = $attempt['score'] >= $passingScore;

// Helper for URL slugs
function getSubjectSlug($code)
{
    switch ($code) {
        case 'MATH':
            return 'math';
        case 'SCI':
            return 'science';
        case 'SOC':
            return 'social_studies';
        case 'LANG':
            return 'reasoning_language_arts';
        default:
            return strtolower($code);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - <?php echo htmlspecialchars($attempt['subject_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Pearson/Academic Style */
        :root {
            --primary: #0077c8;
            --secondary: #00a651;
            --fail: #d946ef;
            /* Changed from red to something less aggressive or keep red? Standard is red. */
            --fail-bg: #fee2e2;
            --fail-text: #b91c1c;
            --bg: #f5f7fa;
            --surface: #ffffff;
            --text-main: #2d2d2d;
            --text-muted: #595959;
            --border: #d4d4d4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Summary Card */
        .summary-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border-top: 6px solid var(--primary);
        }

        .summary-header h1 {
            font-size: 1.75rem;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .summary-header p {
            color: var(--text-muted);
        }

        .score-display {
            margin: 2rem auto;
            position: relative;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 8px solid #f0f0f0;
        }

        .score-display.pass {
            border-color: var(--secondary);
            color: var(--secondary);
        }

        .score-display.fail {
            border-color: #ef4444;
            color: #ef4444;
        }

        .score-val {
            font-size: 2.5rem;
            font-weight: 800;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 2rem;
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .stat-item h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .stat-item div {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .result-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            margin-top: 1rem;
            font-size: 1.1rem;
        }

        .result-badge.pass {
            background: #d1fae5;
            color: #065f46;
        }

        .result-badge.fail {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Question Review */
        .review-section {
            background: var(--surface);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .q-item {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #fff;
        }

        .q-item.correct {
            border-left: 5px solid var(--secondary);
        }

        .q-item.incorrect {
            border-left: 5px solid #ef4444;
        }

        .q-item.skipped {
            border-left: 5px solid #f59e0b;
        }

        .q-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .q-status {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
        }

        .q-status.correct {
            background: #d1fae5;
            color: #065f46;
        }

        .q-status.incorrect {
            background: #fee2e2;
            color: #991b1b;
        }

        .q-prompt {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .q-prompt img {
            max-width: 100%;
            margin-top: 1rem;
            border-radius: 4px;
            border: 1px solid var(--border);
        }

        .q-prompt .dropdown-placeholder {
            background: #e0f2fe;
            color: var(--primary);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            border-bottom: 2px solid var(--primary);
            font-size: 0.9em;
        }

        .ans-box {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .ans-line {
            margin-bottom: 0.5rem;
            display: flex;
            gap: 0.5rem;
        }

        .label {
            font-weight: 600;
            min-width: 80px;
        }

        .val {
            color: var(--text-main);
        }

        .val.correct {
            color: var(--secondary);
            font-weight: 600;
        }

        .val.wrong {
            color: #dc2626;
            text-decoration: line-through;
        }

        .explanation {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .explanation strong {
            color: var(--primary);
            display: block;
            margin-bottom: 0.25rem;
        }

        .action-bar {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 2rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.1s;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        /* List Styles for Drag/Drop */
        .list-group {
            list-style: decimal inside;
            margin-top: 0.5rem;
        }

        .list-group li {
            margin-bottom: 0.25rem;
            padding-left: 0.5rem;
        }

        @media(max-width:600px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Summary Header -->
        <div class="summary-card">
            <div class="summary-header">
                <h1><?php echo htmlspecialchars($attempt['subject_name']); ?></h1>
                <p>Completed on <?php echo date('F j, Y \a\t g:i A', strtotime($attempt['end_time'])); ?></p>
            </div>

            <div class="score-display <?php echo $isPassing ? 'pass' : 'fail'; ?>">
                <div class="score-val"><?php echo round($attempt['score']); ?>%</div>
            </div>

            <div class="result-badge <?php echo $isPassing ? 'pass' : 'fail'; ?>">
                <?php echo $isPassing ? '✅ EXAM PASSED' : '❌ NOT PASSED'; ?>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <h4>Correct</h4>
                    <div style="color:var(--secondary)"><?php echo $attempt['correct_answers']; ?></div>
                </div>
                <div class="stat-item">
                    <h4>Incorrect</h4>
                    <div style="color:#ef4444"><?php echo $attempt['incorrect_answers']; ?></div>
                </div>
                <div class="stat-item">
                    <h4>Skipped</h4>
                    <div style="color:#f59e0b"><?php echo $attempt['unanswered']; ?></div>
                </div>
                <div class="stat-item">
                    <h4>Duration</h4>
                    <div><?php echo floor(($attempt['time_spent_seconds'] ?? 0) / 60); ?>m</div>
                </div>
            </div>
        </div>

        <!-- Detailed Review -->
        <div class="review-section">
            <h2 style="margin-bottom:1.5rem;color:var(--text-main)">Question Analysis</h2>

            <?php foreach ($recordedAnswers as $qId => $record):
            // Note: recordedAnswers likely keyed by ID if we updated exam.php correctly?
            // Actually exam.php sends `answers: { qId: val }`. 
            // But `results.php` (old) expected array index? NO, the previous results.php assumed index.
            // The DB stores `answers_json`. `exam.php` sends `answers` as Object { qID: val }.
            // So $recordedAnswers is { "12": "A", "15": {...} }.
        
            // We better loop through the original Questions List to maintain order
            // and lookup the answer in $recordedAnswers.
        endforeach; ?>

            <?php foreach ($questions as $index => $q):
                $qId = $q['id'];
                $userAns = $recordedAnswers[$qId] ?? null;
                $qType = $q['question_type'] ?? 'multiple_choice';

                // Determine Correctness
                // Logic duplicates backend grading essentially, but valid for display
                $isCorrect = false;
                $isSkipped = ($userAns === null);

                // Re-grade for display purposes (simplified)
                if (!$isSkipped) {
                    if ($qType === 'multiple_choice') {
                        $isCorrect = ($userAns === $q['correct_answer']);
                    } elseif ($qType === 'dropdown') {
                        $opts = json_decode($q['answer_options'], true);
                        $allMatch = true;
                        // userAns is { groupId: val }
                        if (is_array($userAns) && $opts) {
                            foreach ($opts as $grp) {
                                if (($userAns[$grp['id']] ?? '') !== $grp['correct'])
                                    $allMatch = false;
                            }
                            $isCorrect = $allMatch;
                        } else
                            $isCorrect = false;
                    } elseif ($qType === 'drag_drop') {
                        $opts = json_decode($q['answer_options'], true);
                        $correctOrder = $opts['items'] ?? [];
                        $isCorrect = ($userAns === $correctOrder);
                    }
                }

                $statusClass = $isSkipped ? 'skipped' : ($isCorrect ? 'correct' : 'incorrect');
                $statusLabel = $isSkipped ? 'Skipped' : ($isCorrect ? 'Correct' : 'Incorrect');

                // Render Text with Placeholders
                $text = $q['question_text'];
                if ($qType === 'dropdown' && $q['answer_options']) {
                    $opts = json_decode($q['answer_options'], true);
                    foreach ($opts as $grp) {
                        $text = str_replace("{{{$grp['id']}}}", "<span class='dropdown-placeholder'>[{$grp['correct']}]</span>", $text);
                    }
                }
                ?>
                <div class="q-item <?php echo $statusClass; ?>">
                    <div class="q-meta">
                        <span>Question <?php echo $index + 1; ?></span>
                        <span class="q-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                    </div>

                    <div class="q-prompt">
                        <?php echo $text; ?>
                        <?php if ($q['question_image_url']): ?>
                            <br><img src="<?php echo htmlspecialchars($q['question_image_url']); ?>">
                        <?php endif; ?>
                    </div>

                    <div class="ans-box">
                        <?php if ($qType === 'multiple_choice'): ?>
                            <div class="ans-line">
                                <span class="label">Your Answer:</span>
                                <span class="val <?php echo $isCorrect ? 'correct' : 'wrong'; ?>">
                                    <?php echo $userAns ? htmlspecialchars($userAns) : '(None)'; ?>
                                    (<?php echo htmlspecialchars($q['option_' . strtolower($userAns ?? '')] ?? ''); ?>)
                                </span>
                            </div>
                            <?php if (!$isCorrect): ?>
                                <div class="ans-line">
                                    <span class="label">Correct:</span>
                                    <span class="val correct">
                                        <?php echo $q['correct_answer']; ?>
                                        (<?php echo htmlspecialchars($q['option_' . strtolower($q['correct_answer'])] ?? ''); ?>)
                                    </span>
                                </div>
                            <?php endif; ?>

                        <?php elseif ($qType === 'dropdown'): ?>
                            <div class="ans-line"><span class="label">Your Selection:</span></div>
                            <ul class="list-group">
                                <?php
                                $opts = json_decode($q['answer_options'], true);
                                foreach ($opts as $grp):
                                    $uVal = $userAns[$grp['id']] ?? '(None)';
                                    $cVal = $grp['correct'];
                                    $match = $uVal === $cVal;
                                    ?>
                                    <li class="<?php echo $match ? '' : 'wrong'; ?>">
                                        <?php echo htmlspecialchars($uVal); ?>
                                        <?php if (!$match)
                                            echo " ➝ <strong style='color:var(--secondary)'>" . htmlspecialchars($cVal) . "</strong>"; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                        <?php elseif ($qType === 'drag_drop'): ?>
                            <div class="ans-line"><span class="label">Your Order:</span></div>
                            <ol class="list-group">
                                <?php if (is_array($userAns)):
                                    foreach ($userAns as $item): ?>
                                        <li><?php echo htmlspecialchars($item); ?></li>
                                    <?php endforeach; else:
                                    echo "<li>(None)</li>";
                                endif; ?>
                            </ol>
                            <?php if (!$isCorrect): ?>
                                <div class="ans-line" style="margin-top:0.5rem"><span class="label">Correct Order:</span></div>
                                <ol class="list-group" style="color:var(--secondary);font-weight:600">
                                    <?php
                                    $opts = json_decode($q['answer_options'], true);
                                    foreach ($opts['items'] as $item):
                                        ?>
                                        <li><?php echo htmlspecialchars($item); ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php if ($q['explanation']): ?>
                        <div class="explanation">
                            <strong>Explanation:</strong>
                            <?php echo htmlspecialchars($q['explanation']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="action-bar">
            <a href="dashboard.php" class="btn btn-outline">Return to Dashboard</a>
            <a href="exam.php?subject=<?php echo getSubjectSlug($attempt['subject_code']); ?>"
                class="btn btn-primary">Try Again</a>
        </div>
    </div>
</body>

</html>