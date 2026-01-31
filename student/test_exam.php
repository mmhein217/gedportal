<?php
session_start();
$_SESSION['user_id'] = 4;
$_SESSION['role'] = 'student';
$_SESSION['username'] = 'student1';

// Get subject from URL
$subject = $_GET['subject'] ?? 'math';
$subjectMap = [
    'math' => 'Mathematical Reasoning',
    'language' => 'Language Arts',
    'science' => 'Science',
    'social' => 'Social Studies'
];
$subjectName = $subjectMap[$subject] ?? 'Unknown';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Exam Test -
        <?php echo $subjectName; ?>
    </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        .question {
            margin: 30px 0;
        }

        .question-text {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .option {
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
        }

        .option:hover {
            border-color: #6366f1;
            background: #f0f0f0;
        }

        .option.selected {
            background: #eef2ff;
            border-color: #6366f1;
            font-weight: bold;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        button {
            padding: 12px 24px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #4f46e5;
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .info {
            background: #d1fae5;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            color: #991b1b;
        }

        .progress {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>
            <?php echo $subjectName; ?> Exam
        </h1>
        <div id="status" class="info">Loading questions...</div>
        <div id="progress" class="progress"></div>
        <div id="questionArea"></div>
        <div class="nav">
            <button id="prevBtn" onclick="prevQuestion()" disabled>← Previous</button>
            <button id="nextBtn" onclick="nextQuestion()">Next →</button>
            <button onclick="finishExam()" style="background:#10b981">Finish Exam</button>
        </div>
    </div>

    <script>
        const subject = '<?php echo $subject; ?>';
        let questions = [];
        let currentIndex = 0;
        let answers = {};

        // Load questions immediately
        loadQuestions();

        async function loadQuestions() {
            try {
                console.log('Fetching questions for:', subject);
                const response = await fetch(`../api/questions.php?subject=${subject}`);
                const data = await response.json();

                console.log('Response:', data);

                if (!data.success) {
                    document.getElementById('status').className = 'error';
                    document.getElementById('status').innerHTML = `<strong>Error:</strong> ${data.message}`;
                    return;
                }

                questions = data.data.questions;
                console.log('Loaded questions:', questions.length);

                document.getElementById('status').innerHTML = `<strong>✅ Ready!</strong> ${questions.length} questions loaded. Time: ${data.data.time_limit} minutes`;

                showQuestion(0);

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('status').className = 'error';
                document.getElementById('status').innerHTML = `<strong>Error:</strong> ${error.message}`;
            }
        }

        function showQuestion(index) {
            if (index < 0 || index >= questions.length) return;

            currentIndex = index;
            const q = questions[index];

            console.log('Showing question', index + 1, ':', q);

            const html = `
                <div class="question">
                    <div class="progress">Question ${index + 1} of ${questions.length}</div>
                    <div class="question-text">${q.question_text}</div>
                    <div class="option ${answers[q.id] === 'A' ? 'selected' : ''}" onclick="selectAnswer(${q.id}, 'A')">
                        A) ${q.option_a}
                    </div>
                    <div class="option ${answers[q.id] === 'B' ? 'selected' : ''}" onclick="selectAnswer(${q.id}, 'B')">
                        B) ${q.option_b}
                    </div>
                    <div class="option ${answers[q.id] === 'C' ? 'selected' : ''}" onclick="selectAnswer(${q.id}, 'C')">
                        C) ${q.option_c}
                    </div>
                    <div class="option ${answers[q.id] === 'D' ? 'selected' : ''}" onclick="selectAnswer(${q.id}, 'D')">
                        D) ${q.option_d}
                    </div>
                </div>
            `;

            document.getElementById('questionArea').innerHTML = html;

            // Update buttons
            document.getElementById('prevBtn').disabled = (index === 0);
            document.getElementById('nextBtn').disabled = (index === questions.length - 1);

            // Update progress
            const answered = Object.keys(answers).length;
            document.getElementById('progress').textContent = `Answered: ${answered}/${questions.length}`;
        }

        function selectAnswer(questionId, answer) {
            answers[questionId] = answer;
            console.log('Selected answer:', questionId, '=', answer);
            showQuestion(currentIndex);
        }

        function prevQuestion() {
            if (currentIndex > 0) {
                showQuestion(currentIndex - 1);
            }
        }

        function nextQuestion() {
            if (currentIndex < questions.length - 1) {
                showQuestion(currentIndex + 1);
            }
        }

        function finishExam() {
            const answered = Object.keys(answers).length;
            const unanswered = questions.length - answered;

            if (unanswered > 0) {
                if (!confirm(`You have ${unanswered} unanswered questions. Submit anyway?`)) {
                    return;
                }
            }

            alert(`Exam submitted!\n\nAnswered: ${answered}/${questions.length}\n\nIn the real exam, this would calculate your score and show results.`);
            window.location.href = 'dashboard.php';
        }
    </script>
</body>

</html>