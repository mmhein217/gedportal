<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['teacher']);

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Portal - Pearson GED</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Pearson/Academic Style */
            --primary: #0077c8;
            --primary-dark: #005a9e;
            --success: #00a651;
            --warning: #db0020;
            --dark: #2d2d2d;
            --gray: #9ca3af;
            --light: #f5f7fa;
            --white: #ffffff;
            --border: #e0e0e0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar similar to Admin */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-menu {
            padding: 1rem;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            color: var(--dark);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #e6f3ff;
            color: var(--primary);
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: var(--primary);
            margin: 0;
            font-size: 1.75rem;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
        }

        .card h2 {
            margin-top: 0;
            color: var(--dark);
            font-size: 1.25rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        /* Exam Controls */
        .exam-controls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .subject-control {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            background: #fafafa;
            transition: transform 0.2s;
        }

        .subject-control:hover {
            transform: translateY(-2px);
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .subject-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .subject-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .control-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        /* Filters/Toggles */
        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: var(--success);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(20px);
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th {
            text-align: left;
            padding: 1rem;
            background: #f9fafb;
            color: var(--gray);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            background: #d1fae5;
            color: var(--success);
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .logout-btn {
            color: var(--warning);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: block;
            text-align: center;
            padding: 0.75rem;
            border: 1px solid var(--warning);
            border-radius: 6px;
            margin-top: 1rem;
        }

        .logout-btn:hover {
            background: #fff1f2;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div style="font-size:1.8rem;">👨‍🏫</div>
                <div>
                    <div style="font-weight:800;color:var(--primary);font-size:1.1rem">Instructor</div>
                    <div style="font-size:0.8rem;color:var(--gray)">Portal</div>
                </div>
            </div>

            <nav class="nav-menu">
                <a href="#" class="nav-item active">
                    <span>📊</span>
                    Dashboard
                </a>
                <a href="#" class="nav-item">
                    <span>📝</span>
                    Assignments
                </a>
                <a href="#" class="nav-item">
                    <span>👥</span>
                    Students
                </a>
                <a href="#" class="nav-item">
                    <span>⚙️</span>
                    Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <div style="font-weight:600;margin-bottom:0.25rem"><?php echo htmlspecialchars($user['full_name']); ?>
                </div>
                <div style="font-size:0.85rem;color:var(--gray)">Teacher</div>
                <button onclick="logout()" class="logout-btn" style="width:100%;background:none;cursor:pointer">Log
                    Out</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Class Overview</h1>
                    <p style="color:var(--gray)">Manage exams and monitor student progress.</p>
                </div>
                <div
                    style="background:white;padding:0.5rem 1rem;border-radius:8px;border:1px solid var(--border);font-weight:600">
                    <?php echo date('F j, Y'); ?>
                </div>
            </div>

            <div class="card">
                <h2>🎛️ Exam Controls</h2>
                <div class="exam-controls-grid">
                    <?php
                    $subjects = [
                        ['id' => 'math', 'name' => 'Math', 'icon' => '∑'],
                        ['id' => 'language', 'name' => 'Language Arts', 'icon' => '📖'],
                        ['id' => 'science', 'name' => 'Science', 'icon' => '🔬'],
                        ['id' => 'social', 'name' => 'Social Studies', 'icon' => '🌍']
                    ];
                    foreach ($subjects as $sub): ?>
                        <div class="subject-control">
                            <div class="subject-header">
                                <div class="subject-icon"><?php echo $sub['icon']; ?></div>
                                <strong><?php echo $sub['name']; ?></strong>
                            </div>
                            <div class="control-row">
                                <span>Enable Exam</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="control-row">
                                <span>Shuffle Questions</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <button class="btn-primary" onclick="editQuestions('<?php echo $sub['id']; ?>')">Manage
                                Questions</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <h2>👩‍🎓 Student Progress</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Exams Taken</th>
                            <th>Avg Score</th>
                            <th>Study Time</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <tr>
                            <td colspan="7" style="text-align:center;padding:2rem">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        async function logout() {
            const formData = new FormData();
            formData.append('action', 'logout');
            await fetch('../backend/auth.php', { method: 'POST', body: formData });
            window.location.href = '../login.php';
        }

        async function loadStudents() {
            try {
                // Using timestamp to avoid cache
                const response = await fetch('../api/analytics.php?action=all_students&t=' + Date.now());
                const data = await response.json();

                if (data.success) {
                    const tbody = document.getElementById('studentsTableBody');
                    tbody.innerHTML = '';

                    data.data.students.forEach(student => {
                        const row = document.createElement('tr');
                        const studyHours = Math.round((student.study_time || 0) / 3600);
                        const lastExam = student.last_exam_date ?
                            new Date(student.last_exam_date).toLocaleDateString() : 'Never';

                        row.innerHTML = `
                            <td>
                                <div style="font-weight:600">${student.full_name}</div>
                            </td>
                            <td style="color:#666">${student.email}</td>
                            <td>${student.total_exams || 0}</td>
                            <td>
                                <span style="font-weight:700;color:${student.avg_score >= 70 ? 'var(--success)' : 'var(--warning)'}">
                                    ${Math.round(student.avg_score || 0)}%
                                </span>
                            </td>
                            <td>${studyHours} hrs</td>
                            <td>${lastExam}</td>
                            <td><span class="status-badge">Active</span></td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            } catch (error) {
                console.error('Error loading students:', error);
            }
        }

        function editQuestions(subject) {
            // Mapping for legacy question page if needed, or redirect
            window.location.href = `../admin/questions.php?subject=${subject}`; // Assuming teachers access same question bank editor
        }

        loadStudents();
    </script>
</body>

</html>