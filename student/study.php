<?php
/**
 * Study Mode - Untimed Practice
 */
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['student']);

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

    // Fetch questions including new types
    $stmt = $db->prepare("SELECT id, question_text, question_type, answer_options, option_a, option_b, option_c, option_d, correct_answer, explanation, question_image_url FROM questions WHERE subject_id = ? AND is_active = TRUE ORDER BY RAND()");
    $stmt->execute([$subjectInfo['id']]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode JSON answer options
    foreach ($questions as &$q) {
        if ($q['answer_options'])
            $q['answer_options'] = json_decode($q['answer_options'], true);
    }

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
    <title>Study - <?php echo htmlspecialchars($subjectInfo['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .timer-badge {
            background: #e6f3ff;
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .progress-container {
            background: #e0e0e0;
            height: 6px;
            border-radius: 3px;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: var(--secondary);
            transition: width 0.3s ease;
        }

        .question-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
        }

        .q-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .q-num {
            font-weight: 600;
            color: var(--text-muted);
        }

        .q-content {
            font-size: 1.15rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .q-content img {
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 4px;
            margin-top: 1rem;
        }

        .options-list {
            display: grid;
            gap: 1rem;
        }

        .option-item {
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
            background: #fafafa;
        }

        .option-item:hover {
            background: #fff;
            border-color: #b0c4de;
        }

        .option-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-muted);
        }

        /* Interactive States */
        .option-item.correct {
            border-color: var(--secondary);
            background: #f0fdf4;
        }

        .option-item.correct .option-circle {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }

        .option-item.incorrect {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .option-item.incorrect .option-circle {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
        }

        .option-item.disabled {
            pointer-events: none;
            opacity: 0.7;
        }

        /* Explanation Box */
        .explanation {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f0f9ff;
            border-left: 5px solid var(--primary);
            border-radius: 0 8px 8px 0;
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        .explanation.visible {
            display: block;
        }

        .explanation h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-actions {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .btn-next {
            background: var(--primary);
            color: white;
        }

        .btn-next:hover {
            background: #0060a0;
        }

        .btn-prev {
            background: #e0e0e0;
            color: var(--text-muted);
        }

        .btn-exit {
            background: transparent;
            color: #dc2626;
            border: 1px solid #dc2626;
        }

        .btn-exit:hover {
            background: #fee2e2;
        }

        /* New Type Styles */
        .dropdown-placeholder {
            background: #f0f0f0;
            border-bottom: 2px solid var(--primary);
            padding: 0 0.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .ordering-list {
            list-style: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .ordering-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            background: #fff;
        }

        .ordering-item:last-child {
            border-bottom: none;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo"><span>📖</span> <?php echo htmlspecialchars($subjectInfo['name']); ?> Study</div>
        <div class="timer-badge" id="studyTimer">00:00</div>
        <button class="btn btn-exit" onclick="saveAndExit()">Exit & Save</button>
    </header>

    <div class="container">
        <div class="progress-container">
            <div class="progress-bar" id="progressFill" style="width:0%"></div>
        </div>

        <div class="question-card">
            <div class="q-header">
                <span class="q-num" id="qNum">Question 1</span>
                <span>Untimed Practice</span>
            </div>

            <div class="q-content">
                <div id="qText"></div>
                <div id="renderArea"></div>
            </div>

            <div class="explanation" id="explanationBox">
                <h4>💡 Explanation</h4>
                <div id="explanationText"></div>
            </div>

            <div class="nav-actions">
                <button class="btn btn-prev" id="btnPrev" onclick="prevQ()" disabled>Previous</button>
                <button class="btn btn-next" id="btnNext" onclick="nextQ()">Next Question →</button>
            </div>
        </div>
    </div>

    <script>
        const questions = <?php echo json_encode($questions); ?>;
        const subjectId = <?php echo $subjectInfo['id']; ?>;

        let currentQ = 0;
        let visited = new Set();
        let startTime = Date.now();
        let studySeconds = 0;

        function renderQuestion() {
            if (!questions.length) { document.getElementById('qText').textContent = "No questions available."; return; }
            const q = questions[currentQ];
            document.getElementById('qNum').textContent = `Question ${currentQ + 1} of ${questions.length}`;

            const textEl = document.getElementById('qText');
            textEl.innerHTML = q.question_text; // Render HTML/Text

            const renderArea = document.getElementById('renderArea');
            renderArea.innerHTML = '';

            // Render Image
            if (q.question_image_url) {
                const img = document.createElement('img');
                img.src = q.question_image_url;
                textEl.appendChild(img);
            }

            // Render Inputs/Options
            const type = q.question_type || 'multiple_choice';

            if (type === 'multiple_choice') {
                const list = document.createElement('div');
                list.className = 'options-list';
                ['A', 'B', 'C', 'D'].forEach(opt => {
                    const item = document.createElement('div');
                    item.className = 'option-item';
                    item.onclick = () => revealAnswer(opt);
                    item.innerHTML = `<div class="option-circle">${opt}</div><div>${q['option_' + opt.toLowerCase()]}</div>`;
                    item.dataset.val = opt;
                    list.appendChild(item);
                });
                renderArea.appendChild(list);
            }
            else if (type === 'dropdown') {
                // For study mode, just show the answers inline in the text 
                // Alternatively, show the correct answers in the explanation
                let text = textEl.innerHTML;
                if (q.answer_options) {
                    q.answer_options.forEach(g => {
                        text = text.replace(`{{${g.id}}}`, `<span class="dropdown-placeholder">[${g.correct}]</span>`);
                    });
                }
                textEl.innerHTML = text;
                renderArea.innerHTML = '<div style="margin-top:1rem;font-style:italic;color:#666">Dropdown answers are shown inline.</div><button class="btn btn-next" style="margin-top:1rem" onclick="revealAnswer()">Show Explanation</button>';
            }
            else if (type === 'drag_drop') {
                // Show Correct Order
                const ul = document.createElement('ul');
                ul.className = 'ordering-list';
                if (q.answer_options && q.answer_options.items) {
                    q.answer_options.items.forEach((item, i) => {
                        ul.innerHTML += `<li class="ordering-item"><strong>${i + 1}.</strong> ${item}</li>`;
                    });
                }
                renderArea.innerHTML = '<h5 style="margin-bottom:0.5rem">Correct Order:</h5>';
                renderArea.appendChild(ul);
                renderArea.innerHTML += '<button class="btn btn-next" style="margin-top:1rem" onclick="revealAnswer()">Show Explanation</button>';
            }

            // Hide Explanation
            document.getElementById('explanationBox').classList.remove('visible');
            document.getElementById('explanationText').innerHTML = q.explanation || 'No detailed explanation available.';

            // Update Buttons
            document.getElementById('btnPrev').disabled = currentQ === 0;
            document.getElementById('btnNext').textContent = currentQ === questions.length - 1 ? 'Finish & Exit' : 'Next Question →';

            // Progress
            visited.add(currentQ);
            document.getElementById('progressFill').style.width = (visited.size / questions.length * 100) + '%';
        }

        function revealAnswer(selectedOpt) {
            const q = questions[currentQ];
            const type = q.question_type || 'multiple_choice';

            if (type === 'multiple_choice') {
                document.querySelectorAll('.option-item').forEach(el => {
                    el.classList.add('disabled');
                    if (el.dataset.val === q.correct_answer) el.classList.add('correct');
                    else if (el.dataset.val === selectedOpt) el.classList.add('incorrect');
                });
            }
            // Show explanation
            document.getElementById('explanationBox').classList.add('visible');
        }

        function prevQ() { if (currentQ > 0) { currentQ--; renderQuestion(); } }
        function nextQ() {
            if (currentQ < questions.length - 1) { currentQ++; renderQuestion(); }
            else saveAndExit();
        }

        function updateTimer() {
            studySeconds = Math.floor((Date.now() - startTime) / 1000);
            const m = Math.floor(studySeconds / 60);
            const s = studySeconds % 60;
            document.getElementById('studyTimer').textContent = `${m}:${s.toString().padStart(2, '0')}`;
        }
        setInterval(updateTimer, 1000);

        function saveAndExit() {
            fetch('../api/study.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'log_study_time',
                    subject_id: subjectId,
                    time_spent: studySeconds,
                    questions_viewed: visited.size
                })
            }).finally(() => {
                window.location.href = 'dashboard.php';
            });
        }

        // Auto-ping
        setInterval(() => {
            fetch('../api/study.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'log_study_time', subject_id: subjectId, time_spent: 30, questions_viewed: 0 })
            });
        }, 30000);

        renderQuestion();
    </script>
</body>

</html>