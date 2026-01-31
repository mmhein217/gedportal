<?php
session_start();
// Simulate logged in student
$_SESSION['user_id'] = 4;
$_SESSION['role'] = 'student';
$_SESSION['username'] = 'student1';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Simple Exam Test</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .question {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .option {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }

        .option:hover {
            border-color: #6366f1;
        }

        .option.selected {
            background: #eef2ff;
            border-color: #6366f1;
        }

        button {
            padding: 10px 20px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        #status {
            padding: 15px;
            background: #fef3c7;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <h1>Simple Exam Test</h1>
    <div id="status">Loading questions...</div>
    <div id="questionContainer"></div>
    <div id="navigation">
        <button onclick="prevQuestion()">← Previous</button>
        <button onclick="nextQuestion()">Next →</button>
        <button onclick="submitExam()" style="background:#10b981">Submit Exam</button>
    </div>

    <script>
        let questions = [];
        let currentIndex = 0;
        let answers = {};

        async function loadQuestions() {
            try {
                const response = await fetch('api/questions.php?subject=math');
                const data = await response.json();

                console.log('API Response:', data);

                if (data.success) {
                    questions = data.data.questions;
                    document.getElementById('status').innerHTML =
                        `<strong>✅ Loaded ${questions.length} questions!</strong> Time: ${data.data.time_limit} minutes`;
                    document.getElementById('status').style.background = '#d1fae5';
                    showQuestion(0);
                } else {
                    document.getElementById('status').innerHTML =
                        `<strong>❌ Error:</strong> ${data.message}`;
                    document.getElementById('status').style.background = '#fee2e2';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('status').innerHTML =
                    `<strong>❌ Failed to load:</strong> ${error.message}`;
                document.getElementById('status').style.background = '#fee2e2';
            }
        }

        function showQuestion(index) {
            if (index < 0 || index >= questions.length) return;

            currentIndex = index;
            const q = questions[index];

            const html = `
                <div class="question">
                    <h3>Question ${index + 1} of ${questions.length}</h3>
                    <p><strong>${q.question_text}</strong></p>
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

            document.getElementById('questionContainer').innerHTML = html;
        }

        function selectAnswer(questionId, answer) {
            answers[questionId] = answer;
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

        function submitExam() {
            const answered = Object.keys(answers).length;
            alert(`You answered ${answered} out of ${questions.length} questions.\n\nIn the real exam, this would submit your answers.`);
        }

        // Load questions on page load
        loadQuestions();
    </script>
</body>

</html>