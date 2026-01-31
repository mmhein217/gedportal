<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['admin']);
$user = getCurrentUser();

try {
    $db = getDB();
    $totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE is_active = TRUE")->fetchColumn();
    $totalQuestions = $db->query("SELECT COUNT(*) FROM questions WHERE is_active = TRUE")->fetchColumn();
    $totalExams = $db->query("SELECT COUNT(*) FROM exam_attempts WHERE status = 'completed'")->fetchColumn();
    $avgScore = $db->query("SELECT AVG(score) FROM exam_attempts WHERE status = 'completed'")->fetchColumn();

    $recentActivity = $db->query("
        SELECT al.*, u.full_name FROM audit_log al 
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC LIMIT 5
    ")->fetchAll();

    $topStudents = $db->query("
        SELECT u.full_name, AVG(ea.score) as avg_score, COUNT(ea.id) as exam_count
        FROM users u
        JOIN exam_attempts ea ON u.id = ea.student_id AND ea.status = 'completed'
        WHERE u.role = 'student'
        GROUP BY u.id ORDER BY avg_score DESC LIMIT 5
    ")->fetchAll();
} catch (Exception $e) {
    $totalUsers = $totalQuestions = $totalExams = 0;
    $avgScore = 0;
    $recentActivity = $topStudents = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GED Prep</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Pearson/Academic Style - Consistent with Student Portal */
            --primary: #0077c8;
            /* Pearson Blue */
            --primary-dark: #005a9e;
            --success: #00a651;
            /* Success Green */
            --warning: #db0020;
            /* Alert Red (used for warning/danger) */
            --danger: #db0020;
            --dark: #2d2d2d;
            --darker: #1a1a1a;
            --gray: #9ca3af;
            --light: #f5f7fa;
            --white: #ffffff;
            --border: #e0e0e0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            color: var(--dark);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-header {
            padding: 2rem;
            background: white;
            border-bottom: 1px solid var(--border);
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
            font-weight: 500;
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

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .user-info .name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info .role {
            font-size: 0.75rem;
            color: var(--gray);
        }

        /* Main Content */
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

        .header-actions {
            display: flex;
            gap: 0.75rem;
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
            color: white;
            box-shadow: 0 2px 4px rgba(0, 119, 200, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--dark);
            border: 1px solid #e5e7eb;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
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

        .stat-icon.users {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .stat-icon.questions {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-icon.exams {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .stat-icon.score {
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

        .stat-info .change {
            font-size: 0.75rem;
            color: var(--success);
            margin-top: 0.25rem;
        }

        /* Cards */
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
        }

        .card-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .card-body {
            padding: 1.5rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        /* Activity List */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content .title {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.875rem;
        }

        .activity-content .time {
            font-size: 0.75rem;
            color: var(--gray);
            margin-top: 0.25rem;
        }

        /* Leaderboard */
        .leaderboard-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .leaderboard-item:last-child {
            border-bottom: none;
        }

        .rank {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .rank.gold {
            background: #fef3c7;
            color: #d97706;
        }

        .rank.silver {
            background: #f3f4f6;
            color: #6b7280;
        }

        .rank.bronze {
            background: #fed7aa;
            color: #c2410c;
        }

        .student-info {
            flex: 1;
        }

        .student-info .name {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .student-info .exams {
            font-size: 0.75rem;
            color: var(--gray);
        }

        .score-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem;
            background: #f9fafb;
            border-radius: 12px;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.2s;
        }

        .quick-action:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.02);
        }

        .quick-action .icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .quick-action .label {
            font-weight: 600;
            font-size: 0.8rem;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
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
                <a href="dashboard.php" class="nav-item active">
                    <span class="icon">📊</span>
                    <span class="label">Dashboard</span>
                </a>
                <a href="questions.php" class="nav-item">
                    <span class="icon">❓</span>
                    <span class="label">Question Bank</span>
                </a>
                <a href="users.php" class="nav-item">
                    <span class="icon">👥</span>
                    <span class="label">Users</span>
                </a>
                <a href="exams.php" class="nav-item">
                    <span class="icon">⚙️</span>
                    <span class="label">Exam Settings</span>
                </a>
                <a href="analytics.php" class="nav-item">
                    <span class="icon">📈</span>
                    <span class="label">Analytics</span>
                </a>
                <a href="audit.php" class="nav-item">
                    <span class="icon">📋</span>
                    <span class="label">Audit Logs</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-card">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                    <div class="user-info">
                        <div class="name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        <div class="role">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
                </div>
                <div class="header-actions">
                    <a href="questions.php" class="btn btn-primary">+ Add Question</a>
                    <a href="../logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon users">👥</div>
                    <div class="stat-info">
                        <h3>Total Users</h3>
                        <div class="value"><?php echo $totalUsers; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon questions">❓</div>
                    <div class="stat-info">
                        <h3>Questions</h3>
                        <div class="value"><?php echo $totalQuestions; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon exams">📝</div>
                    <div class="stat-info">
                        <h3>Exams Taken</h3>
                        <div class="value"><?php echo $totalExams; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon score">📊</div>
                    <div class="stat-info">
                        <h3>Avg Score</h3>
                        <div class="value"><?php echo round($avgScore ?? 0); ?>%</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>📋 Recent Activity</h2>
                        <a href="audit.php" class="btn btn-secondary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentActivity)): ?>
                            <p style="color:#6b7280;text-align:center;padding:2rem">No recent activity</p>
                        <?php else: ?>
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">🔔</div>
                                    <div class="activity-content">
                                        <div class="title"><?php echo htmlspecialchars($activity['action']); ?></div>
                                        <div class="time"><?php echo htmlspecialchars($activity['full_name'] ?? 'System'); ?> •
                                            <?php echo date('M j, g:i A', strtotime($activity['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>🏆 Top Students</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($topStudents)): ?>
                            <p style="color:#6b7280;text-align:center;padding:2rem">No exam data yet</p>
                        <?php else: ?>
                            <?php foreach ($topStudents as $i => $student):
                                $rankClass = $i === 0 ? 'gold' : ($i === 1 ? 'silver' : 'bronze');
                                ?>
                                <div class="leaderboard-item">
                                    <div class="rank <?php echo $rankClass; ?>"><?php echo $i + 1; ?></div>
                                    <div class="student-info">
                                        <div class="name"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                        <div class="exams"><?php echo $student['exam_count']; ?> exams</div>
                                    </div>
                                    <div class="score-badge"><?php echo round($student['avg_score']); ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="quick-actions">
                            <a href="users.php" class="quick-action">
                                <span class="icon">👥</span>
                                <span class="label">Manage Users</span>
                            </a>
                            <a href="analytics.php" class="quick-action">
                                <span class="icon">📊</span>
                                <span class="label">View Analytics</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>