<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['admin']);
$user = getCurrentUser();

try {
    $db = getDB();
    $subjects = $db->query("SELECT * FROM subjects ORDER BY id")->fetchAll();
    if (empty($subjects))
        throw new Exception("No subjects in DB");
} catch (Exception $e) {
    // Fallback if DB fails
    $subjects = [
        ['id' => 1, 'name' => 'Math'],
        ['id' => 2, 'name' => 'Language Arts'],
        ['id' => 3, 'name' => 'Science'],
        ['id' => 4, 'name' => 'Social Studies']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Bank - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Quill.js -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <!-- KaTeX -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <style>
        :root {
            /* Pearson Theme */
            --primary: #0077c8;
            --primary-dark: #005a9e;
            --success: #00a651;
            --warning: #d97706;
            --danger: #db0020;
            --dark: #2d2d2d;
            --darker: #1a1a1a;
            --gray: #9ca3af;
            --light: #f5f7fa;
            --white: #ffffff;
            --border: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            min-height: 100vh;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: white;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
            border-right: 1px solid var(--border);
        }

        .sidebar-header {
            padding: 2rem;
            background: white;
            text-align: left;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: var(--primary);
        }

        .sidebar-header span {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-menu {
            padding: 1rem 0;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            color: var(--gray);
            text-decoration: none;
            transition: all 0.2s;
            border-right: 3px solid transparent;
        }

        .nav-item:hover {
            background: #f0f9ff;
            color: var(--primary);
        }

        .nav-item.active {
            background: #e6f3ff;
            color: var(--primary);
            border-right-color: var(--primary);
            font-weight: 700;
        }

        .nav-item .icon {
            font-size: 1.25rem;
        }

        .nav-item .label {
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title h1 {
            font-size: 1.75rem;
            color: var(--dark);
            font-weight: 700;
        }

        .page-title p {
            color: var(--gray);
            margin-top: 0.25rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 4px rgba(0, 119, 200, 0.2);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--dark);
            border: 1px solid #e5e7eb;
        }

        .btn-danger {
            background: var(--danger);
            color: var(--white);
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
        }

        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .filters {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filters select,
        .filters input {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            background: var(--white);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
        }

        .data-table th {
            background: #f9fafb;
            font-weight: 600;
            color: var(--gray);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-math {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-rla {
            background: #fce7f3;
            color: #be185d;
        }

        .badge-sci {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-ss {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-easy {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-hard {
            background: #fee2e2;
            color: #991b1b;
        }

        .editor-container {
            display: none;
        }

        .editor-container.active {
            display: block;
        }

        .list-container.hidden {
            display: none;
        }

        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .editor-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .editor-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .quill-wrapper {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .quill-wrapper .ql-toolbar {
            border: none;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .quill-wrapper .ql-container {
            border: none;
            min-height: 150px;
            font-size: 1rem;
        }

        .quill-wrapper .ql-editor {
            min-height: 150px;
        }

        /* Multiple Choice Options */
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .option-card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.2s;
        }

        .option-card.correct {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .option-card label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .option-card textarea {
            width: 100%;
            min-height: 70px;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            resize: vertical;
        }

        /* Dropdown Editor */
        .dropdown-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .dropdown-item h4 {
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: var(--primary);
        }

        .dropdown-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .dropdown-option-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Drag & Drop Editor */
        .drag-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .drag-handle {
            cursor: move;
            color: var(--gray);
            font-size: 1.25rem;
        }

        .type-section {
            display: none;
        }

        .type-section.active {
            display: block;
        }

        .image-upload-area {
            border: 2px dashed #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 1rem;
        }

        .image-upload-area:hover {
            border-color: var(--primary);
            background: #f9fafb;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-top: 0.5rem;
            border-radius: 8px;
            display: none;
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            z-index: 1000;
        }

        .toast.success {
            background: var(--success);
        }

        .toast.error {
            background: var(--danger);
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>🎓 GED Exam Admin</h1><span>Control Panel</span>
            </div>
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item"><span class="icon">📊</span><span
                        class="label">Dashboard</span></a>
                <a href="questions.php" class="nav-item active"><span class="icon">❓</span><span class="label">Question
                        Bank</span></a>
                <a href="users.php" class="nav-item"><span class="icon">👥</span><span class="label">Users</span></a>
                <a href="exams.php" class="nav-item"><span class="icon">⚙️</span><span class="label">Exam
                        Settings</span></a>
                <a href="analytics.php" class="nav-item"><span class="icon">📈</span><span
                        class="label">Analytics</span></a>
                <a href="audit.php" class="nav-item"><span class="icon">📋</span><span class="label">Audit
                        Logs</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Questions List -->
            <div class="list-container" id="listContainer">
                <div class="page-header">
                    <div class="page-title">
                        <h1>❓ Question Bank</h1>
                        <p>Manage multiple choice, dropdown, and drag & drop questions</p>
                    </div>
                    <button class="btn btn-primary" onclick="openEditor()">+ Add Question</button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>All Questions</h2>
                        <div class="filters">
                            <select id="filterSubject" onchange="loadQuestions()">
                                <option value="">All Subjects</option>
                                <?php if (empty($subjects)): ?>
                                    <option value="">Error: No Subjects Found</option>
                                <?php else: ?>
                                    <?php foreach ($subjects as $s): ?>
                                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <input type="text" id="searchInput" placeholder="Search..." onkeyup="loadQuestions()">
                        </div>
                    </div>
                    <table class="data-table">
                        <tbody id="questionsTable"></tbody>
                    </table>
                </div>
            </div>

            <!-- Question Editor -->
            <div class="editor-container" id="editorContainer">
                <div class="editor-header">
                    <h2 id="editorTitle">Add New Question</h2>
                    <button class="btn btn-secondary" onclick="closeEditor()">Cancel</button>
                </div>

                <div class="editor-card">
                    <input type="hidden" id="questionId">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Subject</label>
                            <select id="subjectId">
                                <?php if (empty($subjects)): ?>
                                    <option value="">Error: No Subjects Found</option>
                                <?php else: ?>
                                    <?php foreach ($subjects as $s): ?>
                                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Question Type</label>
                            <select id="questionType" onchange="switchType(this.value)">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="dropdown">Dropdown (Listdown)</option>
                                <option value="drag_drop">Drag & Drop (Ordering)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Difficulty</label>
                            <select id="difficulty">
                                <option value="easy">Easy</option>
                                <option value="medium" selected>Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Question Text <span id="textHint"
                                style="font-weight:400;color:var(--gray);font-size:0.8rem"></span></label>
                        <div class="quill-wrapper tall">
                            <div id="questionEditor"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="image-upload-area" onclick="document.getElementById('imageUpload').click()">
                            <input type="file" id="imageUpload" accept="image/*" onchange="handleImageUpload(this)">
                            <p>📷 Upload Image (Optional)</p>
                            <img id="imagePreview" class="image-preview">
                        </div>
                        <input type="hidden" id="questionImage">
                    </div>

                    <!-- Type Specific Sections -->

                    <!-- 1. Multiple Choice -->
                    <div id="type_multiple_choice" class="type-section active">
                        <div class="form-group"><label>Answer Options</label></div>
                        <div class="options-grid">
                            <div class="option-card correct" id="cardA">
                                <label><input type="radio" name="correctOpt" value="A" checked> Option A</label>
                                <textarea id="optA"></textarea>
                            </div>
                            <div class="option-card" id="cardB">
                                <label><input type="radio" name="correctOpt" value="B"> Option B</label>
                                <textarea id="optB"></textarea>
                            </div>
                            <div class="option-card" id="cardC">
                                <label><input type="radio" name="correctOpt" value="C"> Option C</label>
                                <textarea id="optC"></textarea>
                            </div>
                            <div class="option-card" id="cardD">
                                <label><input type="radio" name="correctOpt" value="D"> Option D</label>
                                <textarea id="optD"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Dropdown -->
                    <div id="type_dropdown" class="type-section">
                        <div class="form-group">
                            <label>Dropdown Configuration</label>
                            <p style="font-size:0.85rem;color:var(--gray);margin-bottom:1rem">
                                In Question Text, use <code>{{1}}</code>, <code>{{2}}</code> for placeholder positions.
                                Add options below.
                            </p>
                            <div id="dropdownList"></div>
                            <button class="btn btn-secondary btn-sm" onclick="addDropdownItem()">+ Add Dropdown
                                Group</button>
                        </div>
                    </div>

                    <!-- 3. Drag Drop -->
                    <div id="type_drag_drop" class="type-section">
                        <div class="form-group">
                            <label>Ordering Items (Correct Order)</label>
                            <p style="font-size:0.85rem;color:var(--gray);margin-bottom:1rem">
                                Add items in the <strong>CORRECT</strong> order. They will be shuffled for the student.
                            </p>
                            <div id="dragList"></div>
                            <button class="btn btn-secondary btn-sm" onclick="addDragItem()">+ Add Item</button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:2rem">
                        <label>Explanation</label>
                        <div class="quill-wrapper">
                            <div id="explanationEditor"></div>
                        </div>
                    </div>

                    <div class="editor-actions" style="margin-top:2rem;text-align:right">
                        <button class="btn btn-primary" onclick="saveQuestion()">💾 Save Question</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let qEditor, eEditor;
        let dropdowns = [];
        let dragItems = [];

        document.addEventListener('DOMContentLoaded', () => {
            qEditor = new Quill('#questionEditor', { theme: 'snow', modules: { toolbar: true } });
            eEditor = new Quill('#explanationEditor', { theme: 'snow', modules: { toolbar: true } });
            loadQuestions();

            // Radio listener
            document.querySelectorAll('input[name="correctOpt"]').forEach(r => {
                r.addEventListener('change', e => {
                    document.querySelectorAll('.option-card').forEach(c => c.classList.remove('correct'));
                    document.getElementById('card' + e.target.value).classList.add('correct');
                });
            });
        });

        function switchType(type) {
            document.querySelectorAll('.type-section').forEach(el => el.classList.remove('active'));
            document.getElementById('type_' + type).classList.add('active');

            if (type === 'dropdown') document.getElementById('textHint').textContent = 'Use {{1}} for first dropdown, {{2}} for second...';
            else if (type === 'drag_drop') document.getElementById('textHint').textContent = 'Describe the process to be ordered.';
            else document.getElementById('textHint').textContent = '';
        }

        // --- Dropdown Logic ---
        function addDropdownItem(data = null) {
            const id = dropdowns.length + 1;
            const container = document.getElementById('dropdownList');
            const div = document.createElement('div');
            div.className = 'dropdown-item';
            div.innerHTML = `
                <h4>Dropdown {{${id}}}</h4>
                <div class="dropdown-options" id="dd_opts_${id}"></div>
                <button class="btn btn-sm btn-secondary" style="margin-top:0.5rem" onclick="addOptionRow(${id})">+ Add Option</button>
            `;
            container.appendChild(div);
            dropdowns.push({ id, options: [] });
            if (data) data.options.forEach(opt => addOptionRow(id, opt, data.correct));
            else { addOptionRow(id); addOptionRow(id); }
        }

        function addOptionRow(ddId, val = '', correct = '') {
            const container = document.getElementById(`dd_opts_${ddId}`);
            const row = document.createElement('div');
            row.className = 'dropdown-option-row';
            row.innerHTML = `
                <input type="radio" name="dd_correct_${ddId}" ${val && val === correct ? 'checked' : ''}>
                <input type="text" value="${val}" placeholder="Option text">
                <button class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">×</button>
            `;
            container.appendChild(row);
        }

        // --- Drag Drop Logic ---
        function addDragItem(val = '') {
            const container = document.getElementById('dragList');
            const row = document.createElement('div');
            row.className = 'drag-item';
            row.innerHTML = `
                <span class="drag-handle">☰</span>
                <input type="text" value="${val}" placeholder="Item content" style="flex:1;padding:0.5rem;border:1px solid #e5e7eb;border-radius:6px">
                <button class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">×</button>
            `;
            container.appendChild(row);
        }

        function handleImageUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('questionImage').value = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openEditor(q = null) {
            document.getElementById('listContainer').classList.add('hidden');
            document.getElementById('editorContainer').classList.add('active');

            // Reset
            dropdowns = [];
            document.getElementById('dropdownList').innerHTML = '';
            document.getElementById('dragList').innerHTML = '';

            if (q) {
                document.getElementById('editorTitle').textContent = 'Edit Question';
                document.getElementById('questionId').value = q.id;
                document.getElementById('subjectId').value = q.subject_id;
                document.getElementById('questionType').value = q.question_type || 'multiple_choice';
                document.getElementById('difficulty').value = q.difficulty || 'medium';
                qEditor.root.innerHTML = q.question_text;
                eEditor.root.innerHTML = q.explanation || '';

                document.getElementById('questionImage').value = q.question_image_url || '';
                document.getElementById('imagePreview').src = q.question_image_url || '';
                document.getElementById('imagePreview').style.display = q.question_image_url ? 'block' : 'none';

                switchType(q.question_type || 'multiple_choice');

                if (q.question_type === 'dropdown' && q.answer_options) {
                    q.answer_options.forEach(dd => addDropdownItem(dd));
                } else if (q.question_type === 'drag_drop' && q.answer_options) {
                    q.answer_options.items.forEach(item => addDragItem(item));
                } else {
                    // Multiple choice
                    document.getElementById('optA').value = q.option_a;
                    document.getElementById('optB').value = q.option_b;
                    document.getElementById('optC').value = q.option_c;
                    document.getElementById('optD').value = q.option_d;
                    document.querySelector(`input[name="correctOpt"][value="${q.correct_answer}"]`).checked = true;
                }
            } else {
                document.getElementById('editorTitle').textContent = 'Add Question';
                document.getElementById('questionId').value = '';
                qEditor.root.innerHTML = '';
                eEditor.root.innerHTML = '';
                document.getElementById('questionType').value = 'multiple_choice';
                switchType('multiple_choice');

                // Defaults
                document.getElementById('optA').value = '';
                document.getElementById('optB').value = '';
                document.getElementById('optC').value = '';
                document.getElementById('optC').value = '';
                document.getElementById('optD').value = '';

                // FORCE Select first subject if available
                const subjSelect = document.getElementById('subjectId');
                if (subjSelect.options.length > 0 && subjSelect.value === "") {
                    subjSelect.selectedIndex = 0;
                }
            }
        }

        async function saveQuestion() {
            const type = document.getElementById('questionType').value;
            let payload = {
                id: document.getElementById('questionId').value || undefined,
                subject_id: document.getElementById('subjectId').value,
                difficulty: document.getElementById('difficulty').value,
                question_text: qEditor.root.innerHTML,
                explanation: eEditor.root.innerHTML,
                question_type: type,
                image_url: document.getElementById('questionImage').value
            };

            if (!payload.subject_id) {
                // Try to recover by selecting the first one
                const select = document.getElementById('subjectId');
                if (select.options.length > 0 && select.options[0].value) {
                    payload.subject_id = select.options[0].value;
                    select.value = payload.subject_id;
                } else {
                    showToast('Please select a subject (or create one in Settings)', 'error');
                    return;
                }
            }

            if (type === 'multiple_choice') {
                payload.option_a = document.getElementById('optA').value;
                payload.option_b = document.getElementById('optB').value;
                payload.option_c = document.getElementById('optC').value;
                payload.option_d = document.getElementById('optD').value;
                payload.correct_answer = document.querySelector('input[name="correctOpt"]:checked').value;
            } else if (type === 'dropdown') {
                const groups = [];
                document.querySelectorAll('.dropdown-item').forEach((div, i) => {
                    const options = [];
                    let correct = '';
                    div.querySelectorAll('.dropdown-option-row').forEach(row => {
                        const val = row.querySelector('input[type="text"]').value;
                        if (val) {
                            options.push(val);
                            if (row.querySelector('input[type="radio"]').checked) correct = val;
                        }
                    });
                    if (options.length) groups.push({ id: i + 1, options, correct });
                });
                payload.answer_options = groups;
            } else if (type === 'drag_drop') {
                const items = [];
                document.querySelectorAll('.drag-item input').forEach(input => {
                    if (input.value) items.push(input.value);
                });
                payload.answer_options = { type: 'ordering', items };
            }

            const action = payload.id ? 'update_question' : 'add_question';
            const res = await fetch('../api/admin.php?action=' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                closeEditor();
                loadQuestions();
                showToast('Saved successfully', 'success');
            } else {
                showToast(data.message, 'error');
            }
        }

        function closeEditor() {
            document.getElementById('editorContainer').classList.remove('active');
            document.getElementById('listContainer').classList.remove('hidden');
        }

        function showToast(msg, type) {
            const t = document.createElement('div');
            t.className = 'toast ' + type; t.textContent = msg;
            document.body.appendChild(t); setTimeout(() => t.remove(), 3000);
        }

        async function loadQuestions() {
            const subject = document.getElementById('filterSubject').value;
            let url = '../api/admin.php?action=get_questions';
            if (subject) url += '&subject_id=' + subject;

            const res = await fetch(url);
            const data = await res.json();

            document.getElementById('questionsTable').innerHTML = data.data.map(q => `
                <tr>
                    <td>${q.question_text.replace(/<[^>]*>/g, '').substring(0, 60)}...</td>
                    <td><span class="badge badge-easy">${q.question_type}</span></td>
                    <td>${q.difficulty}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick='openEditor(${JSON.stringify(q)})'>Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteQuestion(${q.id})">Del</button>
                    </td>
                </tr>
            `).join('');
        }

        async function deleteQuestion(id) {
            if (!confirm('Delete?')) return;
            await fetch('../api/admin.php?action=delete_question', {
                method: 'POST', body: JSON.stringify({ id })
            });
            loadQuestions();
        }
    </script>
</body>

</html>