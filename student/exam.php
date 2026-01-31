<?php
/**
 * Exam Mode - Timed, Secure, with Calculator and Updated UI
 */
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['student']);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$user = getCurrentUser();
$subject = $_GET['subject'] ?? '';

try {
    $db = getDB();
    // Expanded mapping to handle various user-friendly URL parameters
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
        die("Invalid Subject Error.<br>
             Received: " . htmlspecialchars($subject) . "<br>
             Slug: " . htmlspecialchars($slug) . "<br>
             Mapped Code: " . htmlspecialchars($subjectCode) . "<br>
             <a href='dashboard.php'>Return to Dashboard</a>");
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
    <title>Exam - <?php echo htmlspecialchars($subjectInfo['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <style>
        /* Pearson/Academic Style - Clean & Accessible */
        :root {
            --primary: #0077c8;
            /* Pearson Blue */
            --secondary: #00a651;
            /* Success Green */
            --accent: #db0020;
            /* Alert Red */
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
            overflow-x: hidden;
        }

        /* Modal Overlay */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(4px);
        }

        .modal.hidden {
            display: none
        }

        .modal-content {
            background: var(--surface);
            border-radius: 12px;
            padding: 2.5rem;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-top: 6px solid var(--primary);
        }

        .modal-content h2 {
            color: var(--text-main);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .modal-content ul {
            text-align: left;
            margin: 1.5rem 0;
            padding-left: 1.5rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background: var(--accent);
            color: white;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: var(--text-main);
            margin-right: 0.5rem;
        }

        /* Exam Screen */
        .exam-screen {
            display: none;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .exam-screen.active {
            display: flex;
        }

        /* Header */
        .exam-header {
            background: var(--surface);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .exam-title-badge {
            background: #e6f3ff;
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .timer-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .timer {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-main);
            background: #f0f0f0;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
        }

        .timer.warning {
            color: var(--accent);
            background: #fff0f0;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-calc {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }

        .btn-calc:hover {
            background: #e6f3ff;
        }

        /* Layout */
        .exam-body {
            display: flex;
            flex: 1;
            padding: 2rem;
            gap: 2rem;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
            position: relative;
        }

        .question-area {
            flex: 1;
            min-width: 0;
        }

        .sidebar {
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Question Card */
        .question-card {
            background: var(--surface);
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            min-height: 600px;
            display: flex;
            flex-direction: column;
        }

        .q-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
        }

        .q-num {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .flag-toggle {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .flag-toggle:hover,
        .flag-toggle.active {
            opacity: 1;
            color: #f59e0b;
        }

        .q-content {
            flex: 1;
            font-size: 1.15rem;
            line-height: 1.7;
            color: var(--text-main);
        }

        .q-content img {
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 4px;
            margin-top: 1rem;
        }

        /* Inputs & Options */
        .options-list {
            display: grid;
            gap: 1rem;
            margin-top: 2rem;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fafafa;
        }

        .option-item:hover {
            border-color: #b0c4de;
            background: #fff;
        }

        .option-item.selected {
            border-color: var(--primary);
            background: #e6f3ff;
        }

        .option-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--text-muted);
            background: #fff;
        }

        .option-item.selected .option-circle {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .dropdown-inline {
            border: none;
            border-bottom: 2px solid var(--primary);
            background: #f0f7ff;
            color: var(--primary);
            font-weight: 600;
            padding: 0 0.5rem;
            font-size: 1.1rem;
            cursor: pointer;
            outline: none;
        }

        /* Drag & Drop */
        .sortable-list {
            list-style: none;
            margin-top: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .sortable-item {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem;
            cursor: grab;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sortable-item:last-child {
            border-bottom: none;
        }

        .sortable-item:hover {
            background: #f9f9f9;
        }

        .sortable-item.dragging {
            background: #e6f3ff;
            opacity: 0.8;
        }

        .drag-handle {
            color: #ccc;
            font-size: 1.2rem;
            cursor: grab;
        }

        /* Navigation */
        .nav-bar {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 2rem;
        }

        .btn-nav {
            padding: 0.75rem 2rem;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-main);
            font-weight: 600;
            cursor: pointer;
        }

        .btn-nav:hover {
            background: #f0f0f0;
        }

        .btn-next {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-next:hover {
            background: #005a9e;
        }

        /* Sidebar Elements */
        .progress-card {
            background: var(--surface);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .progress-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .grid-btn {
            aspect-ratio: 1;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .grid-btn:hover {
            border-color: var(--primary);
        }

        .grid-btn.answered {
            background: #e6f7ef;
            border-color: var(--secondary);
            color: var(--secondary);
            font-weight: 700;
        }

        .grid-btn.current {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 119, 200, 0.2);
            z-index: 2;
            color: var(--primary);
        }

        .grid-btn.flagged {
            border-color: #f59e0b;
            color: #f59e0b;
            position: relative;
        }

        .grid-btn.flagged::after {
            content: '•';
            position: absolute;
            top: -5px;
            right: 2px;
            font-size: 1.2rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #008f45;
        }

        /* Calculator Styles */
        .calculator {
            position: fixed;
            top: 100px;
            right: 20px;
            width: 280px;
            background: #2d2d2d;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            z-index: 500;
            overflow: hidden;
            display: none;
        }

        .calculator.visible {
            display: block;
        }

        .calc-header {
            background: #404040;
            padding: 0.5rem 1rem;
            color: #fff;
            cursor: grab;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }

        .calc-display {
            background: #99c24d;
            color: #2d2d2d;
            padding: 1rem;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 4px solid #333;
            height: 60px;
            overflow: hidden;
            white-space: nowrap;
        }

        .calc-keys {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: #333;
        }

        .key {
            padding: 1rem 0;
            border: none;
            background: #e0e0e0;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            color: #333;
        }

        .key:hover {
            background: #fff;
        }

        .key.op {
            background: #ff9f1c;
            color: white;
        }

        .key.op:hover {
            background: #ffbf69;
        }

        .key.eq {
            background: #2ec4b6;
            color: white;
            grid-column: span 2;
        }

        .key.clear {
            background: #e71d36;
            color: white;
        }
    </style>
</head>

<body>

    <!-- Warning Modal -->
    <div class="modal" id="warningModal">
        <div class="modal-content">
            <h2>⚠️ Start Exam</h2>
            <div style="font-size:3rem;margin:1rem">📝</div>
            <p><strong><?php echo htmlspecialchars($subjectInfo['name']); ?></strong></p>
            <ul>
                <li>Timed: <strong><?php echo $subjectInfo['duration_minutes']; ?> minutes</strong></li>
                <li>Your screen will be monitored.</li>
                <li>Do not switch tabs or exit fullscreen.</li>
            </ul>
            <div style="margin-top:2rem">
                <button class="btn btn-secondary" onclick="window.location='dashboard.php'">Return to Dashboard</button>
                <button class="btn btn-danger" onclick="startExam()">Launch Exam</button>
            </div>
        </div>
    </div>

    <!-- Exam Interface -->
    <div class="exam-screen" id="examScreen">
        <!-- Header -->
        <header class="exam-header">
            <div class="logo">
                <span>📚</span> GED Prep
                <span class="exam-title-badge"><?php echo htmlspecialchars($subjectInfo['name']); ?></span>
            </div>

            <div class="timer-container">
                <span style="font-size:0.9rem;color:var(--text-muted)">TIME REMAINING:</span>
                <div class="timer" id="timer">00:00</div>
            </div>

            <div class="header-actions">
                <button class="btn-calc" onclick="toggleCalculator()">🧮 Calculator</button>
                <button class="btn btn-danger" style="padding:0.5rem 1rem;font-size:0.9rem" onclick="endExam()">Exit
                    Exam</button>
            </div>
        </header>

        <!-- Main Body -->
        <div class="exam-body">
            <!-- Question Area -->
            <main class="question-area">
                <div class="question-card">
                    <div class="q-meta">
                        <span class="q-num" id="qNumber">Question 1</span>
                        <button class="flag-toggle" id="flagBtn" onclick="toggleFlag()">
                            <span id="flagIcon">⚐</span> Mark for Review
                        </button>
                    </div>

                    <div class="q-content">
                        <div id="qText">Loading question...</div>
                        <div id="answerArea"></div>
                    </div>

                    <div class="nav-bar">
                        <button class="btn-nav" onclick="prevQuestion()" id="btnPrev">Previous</button>
                        <button class="btn-nav btn-next" onclick="nextQuestion()" id="btnNext">Next Question →</button>
                    </div>
                </div>
            </main>

            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="progress-card">
                    <div class="progress-title">Exam Progress</div>
                    <div style="margin-bottom:1rem;font-size:0.9rem;color:var(--text-muted)">
                        Answered: <strong style="color:var(--secondary)"><span id="answeredCount">0</span></strong> /
                        <span id="totalCount">--</span>
                    </div>
                    <div class="grid-container" id="questionGrid"></div>
                </div>

                <button class="btn-submit" onclick="submitExam()">Submit All & Finish</button>
            </aside>
        </div>
    </div>

    <!-- Draggable Calculator -->
    <div class="calculator" id="calculator">
        <div class="calc-header" onmousedown="dragStart(event)">
            <span>Scientific Calculator</span>
            <span style="cursor:pointer" onclick="toggleCalculator()">✕</span>
        </div>
        <div class="calc-display" id="calcDisplay">0</div>
        <div class="calc-keys">
            <button class="key clear" onclick="calcClear()">C</button>
            <button class="key" onclick="calcAppend('(')">(</button>
            <button class="key" onclick="calcAppend(')')">)</button>
            <button class="key op" onclick="calcAppend('/')">÷</button>

            <button class="key" onclick="calcAppend('7')">7</button>
            <button class="key" onclick="calcAppend('8')">8</button>
            <button class="key" onclick="calcAppend('9')">9</button>
            <button class="key op" onclick="calcAppend('*')">×</button>

            <button class="key" onclick="calcAppend('4')">4</button>
            <button class="key" onclick="calcAppend('5')">5</button>
            <button class="key" onclick="calcAppend('6')">6</button>
            <button class="key op" onclick="calcAppend('-')">-</button>

            <button class="key" onclick="calcAppend('1')">1</button>
            <button class="key" onclick="calcAppend('2')">2</button>
            <button class="key" onclick="calcAppend('3')">3</button>
            <button class="key op" onclick="calcAppend('+')">+</button>

            <button class="key" onclick="calcAppend('0')">0</button>
            <button class="key" onclick="calcAppend('.')">.</button>
            <button class="key eq" onclick="calcEval()">=</button>
        </div>
    </div>

    <script>
        const subject = '<?php echo $slug; ?>'; // Use the sanitized slug
        const duration = <?php echo $subjectInfo['duration_minutes']; ?>;

        let questions = [];
        let currentQ = 0;
        let answers = {};
        let flagged = new Set();
        let attemptId = null;
        let startTime = null;
        let timerInterval = null;
        let violations = 0;
        let dragSrcEl = null;

        // --- Core Exam Logic ---
        async function startExam() {
            try {
                // Initialize Attempt
                const res = await fetch('../api/exam.php?t=' + Date.now(), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'start_attempt', subject: subject })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                attemptId = data.data.attempt_id;

                // Load Questions
                const qRes = await fetch(`../api/questions.php?subject=${subject}&shuffle=true`);
                const qData = await qRes.json();
                if (!qData.success) throw new Error(qData.message);
                questions = qData.data.questions;

                // Enter Secure Mode
                try { await document.documentElement.requestFullscreen(); } catch (e) { }
                setupSecurity();
                startTime = Date.now();
                timerInterval = setInterval(updateTimer, 1000);

                // UI Transition
                document.getElementById('warningModal').classList.add('hidden');
                document.getElementById('examScreen').classList.add('active');
                document.getElementById('totalCount').textContent = questions.length;

                renderQuestionGrid();
                renderQuestion();
            } catch (error) { alert('Error starting exam: ' + error.message); }
        }

        // --- Question Rendering ---
        function renderQuestion() {
            const q = questions[currentQ];
            document.getElementById('qNumber').textContent = `Question ${currentQ + 1}`;

            const textEl = document.getElementById('qText');
            textEl.innerHTML = q.question_text;

            const answerArea = document.getElementById('answerArea');
            answerArea.innerHTML = '';

            const type = q.question_type || 'multiple_choice';
            const savedAnswer = answers[q.id];

            // Render Images if any
            if (q.question_image_url) {
                const img = document.createElement('img');
                img.src = q.question_image_url;
                textEl.appendChild(img);
            }

            // Render Options based on Type
            if (type === 'multiple_choice') {
                const list = document.createElement('div');
                list.className = 'options-list';
                ['A', 'B', 'C', 'D'].forEach(opt => {
                    const item = document.createElement('div');
                    item.className = 'option-item ' + (savedAnswer === opt ? 'selected' : '');
                    item.onclick = () => selectMCAnswer(opt);
                    item.innerHTML = `
                        <div class="option-circle">${opt}</div>
                        <div style="font-weight:500">${q['option_' + opt.toLowerCase()] || ''}</div>
                    `;
                    list.appendChild(item);
                });
                answerArea.appendChild(list);
            }
            else if (type === 'dropdown') {
                // Dropdown Logic (Inline replacement)
                let text = textEl.innerHTML;
                if (q.answer_options) {
                    q.answer_options.forEach(group => {
                        const val = (savedAnswer && savedAnswer[group.id]) ? savedAnswer[group.id] : '';
                        let options = `<option value="" disabled ${!val ? 'selected' : ''}>?</option>` +
                            group.options.map(o => `<option value="${o}" ${val === o ? 'selected' : ''}>${o}</option>`).join('');
                        const selectHtml = `<select class="dropdown-inline" onchange="selectDropdownAnswer(${group.id}, this.value)">${options}</select>`;
                        text = text.replace(`{{${group.id}}}`, selectHtml);
                    });
                    textEl.innerHTML = text; // Update text with dropdowns
                }
            }
            else if (type === 'drag_drop') {
                // Drag and Drop Logic
                let items = [];
                if (savedAnswer && Array.isArray(savedAnswer)) items = savedAnswer;
                else if (q.answer_options && q.answer_options.items) {
                    items = q.answer_options.items;
                    // Init answer if first view
                    if (!savedAnswer) answers[q.id] = [...items];
                }

                const list = document.createElement('ul');
                list.className = 'sortable-list';
                list.id = 'sortableList';

                items.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.className = 'sortable-item';
                    li.draggable = true;
                    li.innerHTML = `<span class="drag-handle">☰</span> ${item}`;
                    li.dataset.index = index;
                    // Drag Events
                    li.addEventListener('dragstart', handleDragStart);
                    li.addEventListener('dragover', handleDragOver);
                    li.addEventListener('drop', handleDrop);
                    list.appendChild(li);
                });
                answerArea.appendChild(list);
            }

            // Update UI State
            document.getElementById('flagBtn').className = 'flag-toggle ' + (flagged.has(currentQ) ? 'active' : '');
            document.getElementById('flagIcon').textContent = flagged.has(currentQ) ? '★' : '⚐';
            document.getElementById('btnPrev').style.visibility = currentQ === 0 ? 'hidden' : 'visible';
            document.getElementById('btnNext').textContent = currentQ === questions.length - 1 ? 'Review All' : 'Next Question →';

            updateQuestionGrid();
            updateProgress();
        }

        // --- Interaction Handlers ---
        function selectMCAnswer(opt) {
            answers[questions[currentQ].id] = opt;
            renderQuestion();
        }

        function selectDropdownAnswer(groupId, value) {
            const qId = questions[currentQ].id;
            if (!answers[qId]) answers[qId] = {};
            answers[qId][groupId] = value;
            updateProgress();
            updateQuestionGrid();
        }

        // --- Drag & Drop Helpers ---
        function handleDragStart(e) {
            dragSrcEl = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
            this.classList.add('dragging');
        }
        function handleDragOver(e) { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; return false; }
        function handleDrop(e) {
            e.stopPropagation();
            if (dragSrcEl !== this) {
                const list = document.getElementById('sortableList');
                const fromIdx = [...list.children].indexOf(dragSrcEl);
                const toIdx = [...list.children].indexOf(this);

                const qId = questions[currentQ].id;
                if (!answers[qId]) answers[qId] = [...questions[currentQ].answer_options.items];
                const currentItems = [...answers[qId]];
                const [movedItem] = currentItems.splice(fromIdx, 1);
                currentItems.splice(toIdx, 0, movedItem);
                answers[qId] = currentItems;
                renderQuestion();
            }
            return false;
        }

        // --- Navigation & Grid ---
        function prevQuestion() { if (currentQ > 0) { currentQ--; renderQuestion(); } }
        function nextQuestion() { if (currentQ < questions.length - 1) { currentQ++; renderQuestion(); } }
        function goToQuestion(i) { currentQ = i; renderQuestion(); }
        function toggleFlag() { flagged.has(currentQ) ? flagged.delete(currentQ) : flagged.add(currentQ); renderQuestion(); }

        function updateQuestionGrid() {
            document.querySelectorAll('.grid-btn').forEach((btn, i) => {
                btn.className = 'grid-btn';
                if (i === currentQ) btn.classList.add('current');

                const q = questions[i];
                const ans = answers[q.id];
                let isAnswered = false;

                if (q.question_type === 'dropdown') {
                    const req = (q.answer_options || []).length;
                    const val = ans || {};
                    isAnswered = Object.keys(val).filter(k => val[k]).length === req;
                } else {
                    if (ans) isAnswered = true;
                }

                if (isAnswered) btn.classList.add('answered');
                if (flagged.has(i)) btn.classList.add('flagged');
            });
        }

        function renderQuestionGrid() {
            document.getElementById('questionGrid').innerHTML = questions.map((_, i) =>
                `<button class="grid-btn" onclick="goToQuestion(${i})">${i + 1}</button>`
            ).join('');
        }

        function updateProgress() {
            let count = 0;
            questions.forEach((q) => {
                const ans = answers[q.id];
                if (q.question_type === 'dropdown') {
                    const req = (q.answer_options || []).length;
                    const val = ans || {};
                    if (Object.keys(val).filter(k => val[k]).length === req) count++;
                } else if (ans) count++;
            });
            document.getElementById('answeredCount').textContent = count;
        }

        // --- Timer & Submit ---
        function updateTimer() {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const remaining = (duration * 60) - elapsed;
            if (remaining <= 0) { clearInterval(timerInterval); submitExam(); return; }

            const mins = Math.floor(remaining / 60);
            const secs = remaining % 60;
            const timerEl = document.getElementById('timer');
            timerEl.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            if (remaining < 60) timerEl.classList.add('warning');
        }

        async function submitExam() {
            clearInterval(timerInterval);
            const timeSpent = Math.floor((Date.now() - startTime) / 1000);
            try {
                const res = await fetch('../api/exam.php', {
                    method: 'POST', body: JSON.stringify({
                        action: 'submit_exam', attempt_id: attemptId, answers: answers, time_spent: timeSpent, violations: violations
                    })
                });
                const data = await res.json();
                if (document.fullscreenElement) document.exitFullscreen().catch(() => { });
                if (data.success) window.location.href = `results.php?attempt_id=${attemptId}`;
                else alert('Error: ' + data.message);
            } catch (error) { alert('Submission error: ' + error.message); }
        }

        function endExam() { if (confirm('Are you sure you want to end?')) submitExam(); }

        // --- Security ---
        function setupSecurity() {
            document.addEventListener('visibilitychange', () => { if (document.hidden) recordViolation('tab_switch'); });
            document.addEventListener('fullscreenchange', () => { if (!document.fullscreenElement) recordViolation('fullscreen_exit'); });
            document.addEventListener('contextmenu', e => e.preventDefault());
        }
        function recordViolation(type) {
            violations++;
            fetch('../api/exam.php', { method: 'POST', body: JSON.stringify({ action: 'record_violation', attempt_id: attemptId, violation_type: type }) });
        }

        // --- Calculator ---
        function toggleCalculator() {
            const calc = document.getElementById('calculator');
            calc.classList.toggle('visible');
        }
        let calcVal = '0';
        function calcAppend(val) {
            if (calcVal === '0' && val !== '.') calcVal = val;
            else calcVal += val;
            updateCalc();
        }
        function calcClear() { calcVal = '0'; updateCalc(); }
        function calcEval() {
            try { calcVal = eval(calcVal).toString(); } catch { calcVal = 'Error'; }
            updateCalc();
        }
        function updateCalc() { document.getElementById('calcDisplay').textContent = calcVal; }

        // Calculator Dragging
        let isDragging = false;
        let startX, startY, initialLeft, initialTop;
        function dragStart(e) {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            const style = window.getComputedStyle(document.getElementById('calculator'));
            initialLeft = parseInt(style.left);
            initialTop = parseInt(style.top);
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', dragEnd);
        }
        function drag(e) {
            if (!isDragging) return;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            const el = document.getElementById('calculator');
            el.style.left = `${initialLeft + dx}px`;
            el.style.top = `${initialTop + dy}px`;
            el.style.right = 'auto'; // Reset right
        }
        function dragEnd() {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', dragEnd);
        }

    </script>
</body>

</html>