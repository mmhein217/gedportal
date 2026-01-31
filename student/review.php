<?php
/**
 * Review Mode - Shows all questions with answers and explanations
 * Supports Multiple Choice, Dropdown, and Drag & Drop
 */
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['student']);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$user = getCurrentUser();
$subject = $_GET['subject'] ?? '';

try {
    $db = getDB();
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
    // Sanitize and map
    $slug = strtolower(preg_replace('/[^a-z0-9_]/', '', $subject));
    $subjectCode = $subjectMap[$slug] ?? strtoupper($subject);

    $stmt = $db->prepare("SELECT * FROM subjects WHERE code = ?");
    $stmt->execute([$subjectCode]);
    $subjectInfo = $stmt->fetch();

    if (!$subjectInfo) {
        die("Invalid Subject. <a href='dashboard.php'>Return to Dashboard</a>");
    }

    $stmt = $db->prepare("SELECT * FROM questions WHERE subject_id = ? AND is_active = TRUE ORDER BY id");
    $stmt->execute([$subjectInfo['id']]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Mode - <?php echo htmlspecialchars($subjectInfo['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <style>
        /* Pearson/Academic Style */
        :root {
            --primary: #0077c8;
            --secondary: #00a651;
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
        }

        .header {
            background: var(--surface);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-exit {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-exit:hover {
            background: #f0f0f0;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .filter-bar {
            background: var(--surface);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            border: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .filter-bar input {
            flex: 1;
            padding: 0.6rem;
            border: 1px solid var(--border);
            border-radius: 5px;
            min-width: 200px;
        }

        .filter-bar select {
            padding: 0.6rem;
            border: 1px solid var(--border);
            border-radius: 5px;
        }

        .question-card {
            background: var(--surface);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .q-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .q-number {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1.1rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-type {
            background: #e6f3ff;
            color: var(--primary);
        }

        .badge-diff {
            background: #f0fdf4;
            color: var(--secondary);
            margin-left: 0.5rem;
        }

        .q-text {
            font-size: 1.1rem;
            color: var(--text-main);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .q-text img {
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 4px;
            margin-top: 1rem;
        }

        .q-text .dropdown-placeholder {
            background: #f0f9ff;
            color: var(--primary);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            border-bottom: 2px solid var(--primary);
            font-weight: 600;
            font-size: 0.95em;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .option {
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fafafa;
        }

        .option-label {
            width: 28px;
            height: 28px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Correct State */
        .option.correct {
            border-color: var(--secondary);
            background: #f0fdf4;
        }

        .option.correct .option-label {
            background: var(--secondary);
            color: white;
            border-color: var(--secondary);
        }

        .ordering-list {
            list-style: none;
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .ordering-item {
            padding: 0.75rem 1rem;
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ordering-item:last-child {
            border-bottom: none;
        }

        .order-num {
            width: 24px;
            height: 24px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .explanation {
            background: #f0f9ff;
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: 0 4px 4px 0;
        }

        .explanation h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .explanation p {
            color: var(--text-main);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        @media(max-width:700px) {
            .options-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo"><span>🔍</span> <?php echo htmlspecialchars($subjectInfo['name']); ?> Review</div>
        <a href="dashboard.php" class="btn-exit">Close Review</a>
    </header>

    <div class="container">
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search questions..." onkeyup="filterQuestions()">
            <select id="difficultyFilter" onchange="filterQuestions()">
                <option value="">All Difficulties</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
            <div style="font-size:0.9rem;color:var(--text-muted);margin-left:auto" id="countDisplay">
                <?php echo count($questions); ?> questions
            </div>
        </div>

        <div id="questionsList">
            <?php foreach ($questions as $i => $q):
                $type = $q['question_type'] ?? 'multiple_choice';
                $answers = !empty($q['answer_options']) ? json_decode($q['answer_options'], true) : null;
                ?>
                <div class="question-card"
                    data-question="<?php echo htmlspecialchars(strtolower(strip_tags($q['question_text']))); ?>"
                    data-difficulty="<?php echo $q['difficulty'] ?? 'medium'; ?>">

                    <div class="q-header">
                        <span class="q-number">Question <?php echo $i + 1; ?></span>
                        <div>
                            <span class="badge badge-type"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></span>
                            <span class="badge badge-diff"><?php echo ucfirst($q['difficulty'] ?? 'medium'); ?></span>
                        </div>
                    </div>

                    <div class="q-text">
                        <?php
                        $text = $q['question_text'];
                        if ($type === 'dropdown' && $answers) {
                            foreach ($answers as $grp) {
                                $text = str_replace("{{{$grp['id']}}}", "<span class='dropdown-placeholder'>[{$grp['correct']}]</span>", $text);
                            }
                        }
                        echo $text;
                        ?>
                        <?php if (!empty($q['question_image_url'])): ?>
                            <br><img src="<?php echo htmlspecialchars($q['question_image_url']); ?>" alt="Question Image">
                        <?php endif; ?>
                    </div>

                    <!-- Answers -->
                    <?php if ($type === 'multiple_choice'): ?>
                        <div class="options-grid">
                            <?php foreach (['A', 'B', 'C', 'D'] as $opt):
                                $isCorrect = ($q['correct_answer'] === $opt); ?>
                                <div class="option <?php echo $isCorrect ? 'correct' : ''; ?>">
                                    <span class="option-label"><?php echo $opt; ?></span>
                                    <span><?php echo htmlspecialchars($q['option_' . strtolower($opt)]); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($type === 'drag_drop' && $answers): ?>
                        <div style="margin-bottom:0.5rem;font-weight:600;color:var(--secondary)">Correct Order:</div>
                        <ul class="ordering-list">
                            <?php
                            $items = $answers['items'] ?? [];
                            foreach ($items as $idx => $item): ?>
                                <li class="ordering-item">
                                    <span class="order-num"><?php echo $idx + 1; ?></span>
                                    <?php echo htmlspecialchars($item); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Explanation -->
                    <?php if (!empty($q['explanation'])): ?>
                        <div class="explanation">
                            <h4>Explanation</h4>
                            <p><?php echo $q['explanation']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function filterQuestions() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const difficulty = document.getElementById('difficultyFilter').value;
            const cards = document.querySelectorAll('.question-card');
            let visible = 0;

            cards.forEach(card => {
                const text = card.dataset.question;
                const diff = card.dataset.difficulty;
                const matchSearch = text.includes(search);
                const matchDiff = !difficulty || diff === difficulty;

                if (matchSearch && matchDiff) {
                    card.style.display = 'block';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            document.getElementById('countDisplay').textContent = visible + ' questions';
        }
    </script>
</body>

</html>