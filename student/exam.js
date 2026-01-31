/**
 * Simplified Exam Mode JavaScript - WORKING VERSION
 * GED Exam Management System
 */

// Global state
let examState = {
    questions: [],
    currentQuestion: 0,
    answers: {},
    flagged: new Set(),
    violations: 0,
    startTime: null,
    attemptId: null,
    timerInterval: null,
    timeRemaining: 0,
    isExamMode: false
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    showExamWarningModal();
});

function showExamWarningModal() {
    document.getElementById('examWarningModal').classList.remove('hidden');
}

async function startExam() {
    try {
        console.log('Starting exam for subject:', subject);

        // Hide warning modal
        document.getElementById('examWarningModal').classList.add('hidden');

        // Load questions from API
        const response = await fetch(`../api/questions.php?subject=${subject}`);
        const data = await response.json();

        console.log('API Response:', data);

        if (!data.success) {
            alert('Error loading questions: ' + data.message);
            window.location.href = 'dashboard.php';
            return;
        }

        examState.questions = data.data.questions;
        examState.timeRemaining = data.data.time_limit * 60;

        console.log('Loaded', examState.questions.length, 'questions');

        // Create exam attempt
        const attemptResponse = await fetch('../api/exam.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'start_attempt',
                subject: subject
            })
        });

        const attemptData = await attemptResponse.json();
        if (attemptData.success) {
            examState.attemptId = attemptData.data.attempt_id;
            console.log('Created attempt:', examState.attemptId);
        }

        // Enter fullscreen and start
        await enterFullscreen();
        initializeExam();

    } catch (error) {
        console.error('Error:', error);
        alert('Failed to start exam: ' + error.message);
        window.location.href = 'dashboard.php';
    }
}

async function enterFullscreen() {
    const elem = document.documentElement;
    try {
        if (elem.requestFullscreen) {
            await elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            await elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            await elem.msRequestFullscreen();
        }
        examState.isExamMode = true;
        setupSecurityMonitoring();
    } catch (error) {
        console.warn('Fullscreen not available:', error);
        // Continue anyway for testing
        examState.isExamMode = true;
        setupSecurityMonitoring();
    }
}

function setupSecurityMonitoring() {
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('visibilitychange', handleVisibilityChange);
    window.addEventListener('blur', handleWindowBlur);
    document.addEventListener('keydown', blockKeyboardShortcuts);
    document.addEventListener('contextmenu', blockRightClick);
}

function handleFullscreenChange() {
    if (!document.fullscreenElement && examState.isExamMode) {
        recordViolation('fullscreen_exit', 'Attempted to exit fullscreen');
    }
}

function handleVisibilityChange() {
    if (document.hidden && examState.isExamMode) {
        recordViolation('tab_switch', 'Switched tabs or minimized window');
    }
}

function handleWindowBlur() {
    if (examState.isExamMode) {
        recordViolation('window_blur', 'Switched to another application');
    }
}

function blockKeyboardShortcuts(e) {
    if (examState.isExamMode) {
        const blocked = [
            (e.altKey && e.key === 'Tab'),
            (e.ctrlKey && e.key === 'w'),
            (e.key === 'F11'),
            (e.key === 'Escape')
        ];
        if (blocked.some(b => b)) {
            e.preventDefault();
            recordViolation('keyboard_shortcut', `Blocked: ${e.key}`);
            return false;
        }
    }
}

function blockRightClick(e) {
    if (examState.isExamMode) {
        e.preventDefault();
        recordViolation('right_click', 'Right-click blocked');
        return false;
    }
}

async function recordViolation(type, details) {
    examState.violations++;

    try {
        await fetch('../api/exam.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'record_violation',
                attempt_id: examState.attemptId,
                violation_type: type,
                details: details
            })
        });
    } catch (error) {
        console.error('Error recording violation:', error);
    }

    showViolationWarning(type);

    if (examState.violations >= 5) {
        alert('Maximum violations reached. Exam will be submitted.');
        await submitExam(true);
    }
}

function showViolationWarning(type) {
    const modal = document.getElementById('violationModal');
    const message = document.getElementById('violationMessage');
    const count = document.getElementById('violationCount');

    const messages = {
        'tab_switch': '⚠️ You switched tabs. This is not allowed.',
        'fullscreen_exit': '⚠️ You attempted to exit fullscreen.',
        'keyboard_shortcut': '⚠️ Blocked keyboard shortcut.',
        'right_click': '⚠️ Right-clicking is disabled.',
        'window_blur': '⚠️ You switched applications.'
    };

    message.textContent = messages[type] || type;
    count.textContent = examState.violations;
    modal.classList.remove('hidden');
}

function closeViolationModal() {
    document.getElementById('violationModal').classList.add('hidden');
}

function initializeExam() {
    console.log('Initializing exam...');

    // Show exam screen
    document.getElementById('examScreen').classList.remove('hidden');

    // Set start time
    examState.startTime = new Date();

    // Render UI
    renderQuestionGrid();
    renderQuestion();
    startTimer();

    // Setup buttons
    document.getElementById('btnNext').addEventListener('click', nextQuestion);
    document.getElementById('btnPrevious').addEventListener('click', previousQuestion);
    document.getElementById('btnFlag').addEventListener('click', toggleFlag);
    document.getElementById('btnEndExam').addEventListener('click', confirmEndExam);

    console.log('Exam initialized!');
}

function renderQuestionGrid() {
    const grid = document.getElementById('questionGrid');
    grid.innerHTML = '';

    examState.questions.forEach((q, index) => {
        const btn = document.createElement('button');
        btn.className = 'question-nav-btn';
        btn.textContent = index + 1;
        btn.onclick = () => goToQuestion(index);
        grid.appendChild(btn);
    });

    updateQuestionGrid();
}

function updateQuestionGrid() {
    const buttons = document.querySelectorAll('.question-nav-btn');
    buttons.forEach((btn, index) => {
        btn.classList.remove('current', 'answered', 'flagged');

        if (index === examState.currentQuestion) {
            btn.classList.add('current');
        } else if (examState.answers[index] !== undefined) {
            btn.classList.add('answered');
        }

        if (examState.flagged.has(index)) {
            btn.classList.add('flagged');
        }
    });

    // Update progress
    const answered = Object.keys(examState.answers).length;
    const total = examState.questions.length;
    document.getElementById('progressText').textContent = `${answered}/${total}`;
    document.getElementById('progressFill').style.width = `${(answered / total) * 100}%`;
}

function renderQuestion() {
    const question = examState.questions[examState.currentQuestion];
    if (!question) {
        console.error('No question at index:', examState.currentQuestion);
        return;
    }

    console.log('Rendering question', examState.currentQuestion + 1);

    // Update question number
    document.getElementById('currentQuestionNum').textContent = examState.currentQuestion + 1;
    document.getElementById('totalQuestions').textContent = examState.questions.length;

    // Render question text
    document.getElementById('questionText').textContent = question.question_text;

    // Render answer options
    const container = document.getElementById('answerOptions');
    container.innerHTML = '';

    const options = ['A', 'B', 'C', 'D'];
    const texts = [question.option_a, question.option_b, question.option_c, question.option_d];

    options.forEach((label, index) => {
        const div = document.createElement('div');
        div.className = 'answer-option';
        if (examState.answers[examState.currentQuestion] === label) {
            div.classList.add('selected');
        }

        div.innerHTML = `
            <div class="answer-label">${label}</div>
            <div class="answer-text">${texts[index]}</div>
        `;

        div.onclick = () => selectAnswer(label);
        container.appendChild(div);
    });

    // Update flag button
    const flagBtn = document.getElementById('btnFlag');
    if (examState.flagged.has(examState.currentQuestion)) {
        flagBtn.classList.add('flagged');
    } else {
        flagBtn.classList.remove('flagged');
    }

    // Update navigation buttons
    document.getElementById('btnPrevious').disabled = (examState.currentQuestion === 0);
    document.getElementById('btnNext').disabled = (examState.currentQuestion === examState.questions.length - 1);

    updateQuestionGrid();
}

function selectAnswer(answer) {
    examState.answers[examState.currentQuestion] = answer;
    renderQuestion();
}

function nextQuestion() {
    if (examState.currentQuestion < examState.questions.length - 1) {
        examState.currentQuestion++;
        renderQuestion();
    }
}

function previousQuestion() {
    if (examState.currentQuestion > 0) {
        examState.currentQuestion--;
        renderQuestion();
    }
}

function goToQuestion(index) {
    examState.currentQuestion = index;
    renderQuestion();
}

function toggleFlag() {
    if (examState.flagged.has(examState.currentQuestion)) {
        examState.flagged.delete(examState.currentQuestion);
    } else {
        examState.flagged.add(examState.currentQuestion);
    }
    renderQuestion();
}

function startTimer() {
    updateTimerDisplay();
    examState.timerInterval = setInterval(() => {
        examState.timeRemaining--;
        updateTimerDisplay();

        if (examState.timeRemaining <= 0) {
            clearInterval(examState.timerInterval);
            alert('Time is up! Exam will be submitted.');
            submitExam(true);
        }
    }, 1000);
}

function updateTimerDisplay() {
    const hours = Math.floor(examState.timeRemaining / 3600);
    const minutes = Math.floor((examState.timeRemaining % 3600) / 60);
    const seconds = examState.timeRemaining % 60;

    const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    const timerEl = document.getElementById('timeRemaining');
    if (timerEl) {
        timerEl.textContent = display;

        if (examState.timeRemaining < 300) {
            timerEl.parentElement.classList.add('danger');
        } else if (examState.timeRemaining < 600) {
            timerEl.parentElement.classList.add('warning');
        }
    }
}

function confirmEndExam() {
    const answered = Object.keys(examState.answers).length;
    const unanswered = examState.questions.length - answered;

    if (unanswered > 0) {
        if (!confirm(`You have ${unanswered} unanswered questions. Submit anyway?`)) {
            return;
        }
    }

    submitExam(false);
}

async function submitExam(autoSubmit) {
    try {
        clearInterval(examState.timerInterval);

        const timeSpent = Math.floor((new Date() - examState.startTime) / 1000);

        const response = await fetch('../api/exam.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'submit_exam',
                attempt_id: examState.attemptId,
                answers: examState.answers,
                time_spent: timeSpent,
                violations: examState.violations
            })
        });

        const data = await response.json();

        if (data.success) {
            // Exit fullscreen
            if (document.exitFullscreen) {
                await document.exitFullscreen();
            }

            // Redirect to results
            window.location.href = `results.php?attempt_id=${examState.attemptId}`;
        } else {
            alert('Error submitting exam: ' + data.message);
        }

    } catch (error) {
        console.error('Submit error:', error);
        alert('Failed to submit exam. Please try again.');
    }
}
