<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['admin']);
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .sidebar-header span {
            font-size: 0.75rem;
            opacity: 0.8;
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

        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .tab {
            padding: 0.75rem 1.5rem;
            background: var(--white);
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray);
            transition: all 0.2s;
        }

        .tab:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .tab.active {
            background: var(--primary);
            color: white;
            border-color: transparent;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.exams {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .stat-icon.score {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-icon.time {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .stat-icon.students {
            background: linear-gradient(135deg, #ec4899, #db2777);
        }

        .stat-info h3 {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info .value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark);
        }

        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .card-body {
            padding: 1.5rem;
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

        .btn-success {
            background: var(--success);
            color: var(--white);
        }

        .btn-success:hover {
            transform: translateY(-2px);
        }

        .period-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .period-tab {
            padding: 0.5rem 1rem;
            background: #f3f4f6;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .period-tab.active {
            background: var(--primary);
            color: white;
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

        .time-badge {
            padding: 0.3rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .time-badge.study {
            background: #d1fae5;
            color: #065f46;
        }

        .time-badge.exam {
            background: #fef3c7;
            color: #92400e;
        }

        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient);
        }

        .filter-info {
            color: var(--gray);
            font-size: 0.875rem;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }
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
                <a href="questions.php" class="nav-item"><span class="icon">❓</span><span class="label">Question
                        Bank</span></a>
                <a href="users.php" class="nav-item"><span class="icon">👥</span><span class="label">Users</span></a>
                <a href="exams.php" class="nav-item"><span class="icon">⚙️</span><span class="label">Exam
                        Settings</span></a>
                <a href="analytics.php" class="nav-item active"><span class="icon">📈</span><span
                        class="label">Analytics</span></a>
                <a href="audit.php" class="nav-item"><span class="icon">📋</span><span class="label">Audit
                        Logs</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>📈 Analytics Dashboard</h1>
                    <p>Track performance and learning progress</p>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" onclick="showTab('overview')">📊 Overview</button>
                <button class="tab" onclick="showTab('students')">⏱️ Learning Time</button>
                <button class="tab" onclick="showTab('exams')">📝 Exam Results</button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon exams">📝</div>
                    <div class="stat-info">
                        <h3>Total Exams</h3>
                        <div class="value" id="totalExams">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon score">📊</div>
                    <div class="stat-info">
                        <h3>Average Score</h3>
                        <div class="value" id="avgScore">0%</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon time">⏱️</div>
                    <div class="stat-info">
                        <h3>Study Time</h3>
                        <div class="value" id="totalStudyTime">0h</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon students">👥</div>
                    <div class="stat-info">
                        <h3>Active Learners</h3>
                        <div class="value" id="activeStudents">0</div>
                    </div>
                </div>
            </div>

            <div id="overviewTab">
                <div class="card">
                    <div class="card-header">
                        <h2>📚 Performance by Subject</h2><button class="btn btn-success"
                            onclick="downloadSubjectData()">📥 Download</button>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Attempts</th>
                                    <th>Avg Score</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody id="subjectStats"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="studentsTab" style="display:none">
                <div class="card">
                    <div class="card-header">
                        <h2>⏱️ Student Learning Time</h2><button class="btn btn-success"
                            onclick="downloadLearningTime()">📥 Download</button>
                    </div>
                    <div class="card-body" style="padding-top:0">
                        <div class="period-tabs">
                            <button class="period-tab active" onclick="loadLearningTime('today')">📅 Today</button>
                            <button class="period-tab" onclick="loadLearningTime('7days')">📆 7 Days</button>
                            <button class="period-tab" onclick="loadLearningTime('all')">📊 All Time</button>
                        </div>
                        <p class="filter-info" id="periodInfo" style="margin-bottom:1rem">Showing: Today</p>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Study Time</th>
                                    <th>Exam Time</th>
                                    <th>Total</th>
                                    <th>Exams</th>
                                    <th>Avg Score</th>
                                </tr>
                            </thead>
                            <tbody id="studentLearningTime"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="examsTab" style="display:none">
                <div class="card">
                    <div class="card-header">
                        <h2>📝 Recent Exam Results</h2><button class="btn btn-success"
                            onclick="downloadExamResults()">📥 Download</button>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Score</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentExams"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentPeriod = 'today';
        let learningData = [], subjectData = [], examData = [];

        function showTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById('overviewTab').style.display = tab === 'overview' ? 'block' : 'none';
            document.getElementById('studentsTab').style.display = tab === 'students' ? 'block' : 'none';
            document.getElementById('examsTab').style.display = tab === 'exams' ? 'block' : 'none';
            if (tab === 'students') loadLearningTime(currentPeriod);
        }

        async function loadAnalytics() {
            const res = await fetch('../api/admin.php?action=get_analytics');
            const data = await res.json();
            if (data.success) {
                const { overall, by_subject, recent_attempts } = data.data;
                subjectData = by_subject;
                examData = recent_attempts;

                document.getElementById('totalExams').textContent = overall.total_attempts || 0;
                document.getElementById('avgScore').textContent = Math.round(overall.avg_score || 0) + '%';

                const tbody = document.getElementById('subjectStats');
                tbody.innerHTML = by_subject.map(s => {
                    const avg = Math.round(s.avg_score || 0);
                    return `<tr><td><strong>${s.name}</strong></td><td>${s.attempts}</td><td>${avg}%</td>
                        <td><div class="progress-bar"><div class="progress-fill" style="width:${avg}%"></div></div></td></tr>`;
                }).join('') || '<tr><td colspan="4" style="text-align:center;color:#6b7280">No data</td></tr>';

                const examTbody = document.getElementById('recentExams');
                examTbody.innerHTML = recent_attempts.map(a => {
                    const mins = Math.floor((a.time_spent_seconds || 0) / 60);
                    return `<tr><td><strong>${a.full_name}</strong></td><td>${a.subject_name}</td><td>${Math.round(a.score)}%</td>
                        <td><span class="time-badge exam">${mins}m</span></td><td>${new Date(a.end_time).toLocaleDateString()}</td></tr>`;
                }).join('') || '<tr><td colspan="5" style="text-align:center;color:#6b7280">No data</td></tr>';
            } else {
                console.error('Analytics Error:', data.message);
                document.getElementById('subjectStats').innerHTML = `<tr><td colspan="4" style="text-align:center;color:#db0020">Error: ${data.message}</td></tr>`;
                document.getElementById('recentExams').innerHTML = `<tr><td colspan="5" style="text-align:center;color:#db0020">Error: ${data.message}</td></tr>`;
            }
        }

        async function loadLearningTime(period = 'today') {
            currentPeriod = period;
            document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
            event?.target?.classList.add('active');
            document.getElementById('periodInfo').textContent = 'Showing: ' + { today: 'Today', '7days': 'Last 7 Days', all: 'All Time' }[period];

            const res = await fetch('../api/admin.php?action=get_learning_time&period=' + period);
            const data = await res.json();
            if (data.success) {
                learningData = data.data.students;
                const { students, total_study_time, active_count } = data.data;

                document.getElementById('totalStudyTime').textContent = Math.floor((total_study_time || 0) / 3600) + 'h ' + Math.floor(((total_study_time || 0) % 3600) / 60) + 'm';
                document.getElementById('activeStudents').textContent = active_count || 0;

                const tbody = document.getElementById('studentLearningTime');
                tbody.innerHTML = students.length ? students.map(s => {
                    const sm = Math.floor((s.study_time || 0) / 60), em = Math.floor((s.exam_time || 0) / 60);
                    return `<tr><td><strong>${s.full_name}</strong></td><td>${s.email}</td>
                        <td><span class="time-badge study">📖 ${sm}m</span></td>
                        <td><span class="time-badge exam">📝 ${em}m</span></td>
                        <td><strong>${sm + em}m</strong></td><td>${s.total_exams || 0}</td><td>${Math.round(s.avg_score || 0)}%</td></tr>`;
                }).join('') : '<tr><td colspan="7" style="text-align:center;color:#6b7280">No data for this period</td></tr>';
            } else {
                document.getElementById('studentLearningTime').innerHTML = `<tr><td colspan="7" style="text-align:center;color:#db0020">Error: ${data.message}</td></tr>`;
            }
        }

        function downloadCSV(data, filename) {
            const blob = new Blob([data], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
        }

        function downloadLearningTime() {
            let csv = 'Student,Email,Study Time (min),Exam Time (min),Total (min),Exams,Avg Score\n';
            learningData.forEach(s => {
                csv += `"${s.full_name}","${s.email}",${Math.floor((s.study_time || 0) / 60)},${Math.floor((s.exam_time || 0) / 60)},${Math.floor(((s.study_time || 0) + (s.exam_time || 0)) / 60)},${s.total_exams || 0},${Math.round(s.avg_score || 0)}\n`;
            });
            downloadCSV(csv, `Learning_Time_${new Date().toISOString().split('T')[0]}.csv`);
        }

        function downloadSubjectData() {
            let csv = 'Subject,Attempts,Avg Score\n';
            subjectData.forEach(s => csv += `"${s.name}",${s.attempts},${Math.round(s.avg_score || 0)}\n`);
            downloadCSV(csv, `Subject_Performance_${new Date().toISOString().split('T')[0]}.csv`);
        }

        function downloadExamResults() {
            let csv = 'Student,Subject,Score,Time (min),Date\n';
            examData.forEach(a => csv += `"${a.full_name}","${a.subject_name}",${Math.round(a.score)},${Math.floor((a.time_spent_seconds || 0) / 60)},"${new Date(a.end_time).toLocaleDateString()}"\n`);
            downloadCSV(csv, `Exam_Results_${new Date().toISOString().split('T')[0]}.csv`);
        }

        loadAnalytics();
        loadLearningTime('today');
    </script>
</body>

</html>