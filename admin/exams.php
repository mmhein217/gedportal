<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['admin']);
$user = getCurrentUser();

try {
    $db = getDB();
    $subjects = $db->query("SELECT * FROM subjects ORDER BY id")->fetchAll();
} catch (Exception $e) {
    $subjects = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Settings - Admin</title>
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

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .subject-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .subject-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .subject-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .subject-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .subject-icon.math {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .subject-icon.lang {
            background: linear-gradient(135deg, #ec4899, #db2777);
        }

        .subject-icon.sci {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .subject-icon.soc {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .subject-icon.default {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }

        .subject-name {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--dark);
        }

        .subject-code {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
        }

        .setting-group {
            margin-bottom: 1.25rem;
        }

        .setting-label {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .setting-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            transition: border-color 0.2s;
        }

        .setting-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .input-suffix {
            color: var(--gray);
            font-size: 0.875rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
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

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.saved {
            background: #d1fae5;
            color: #065f46;
        }

        .info-card {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .info-card h3 {
            font-size: 0.875rem;
            color: #1e40af;
            margin-bottom: 0.5rem;
        }

        .info-card p {
            font-size: 0.8rem;
            color: #3b82f6;
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
                <a href="exams.php" class="nav-item active"><span class="icon">⚙️</span><span class="label">Exam
                        Settings</span></a>
                <a href="analytics.php" class="nav-item"><span class="icon">📈</span><span
                        class="label">Analytics</span></a>
                <a href="audit.php" class="nav-item"><span class="icon">📋</span><span class="label">Audit
                        Logs</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>⚙️ Exam Settings</h1>
                    <p>Configure exam durations and passing scores by subject</p>
                </div>
            </div>

            <div class="info-card">
                <h3>💡 How Settings Work</h3>
                <p>Set the exam duration in minutes and the minimum passing score percentage for each subject. Changes
                    are applied immediately.</p>
            </div>

            <div class="settings-grid">
                <?php foreach ($subjects as $s):
                    $iconClass = strtolower($s['code'] ?? 'default');
                    $icons = ['math' => '🔢', 'rla' => '📖', 'sci' => '🔬', 'ss' => '🌍'];
                    $icon = $icons[$iconClass] ?? '📚';
                    ?>
                    <div class="subject-card">
                        <div class="subject-header">
                            <div class="subject-icon <?php echo $iconClass; ?>"><?php echo $icon; ?></div>
                            <div>
                                <div class="subject-name"><?php echo htmlspecialchars($s['name']); ?></div>
                                <div class="subject-code"><?php echo htmlspecialchars($s['code'] ?? 'N/A'); ?></div>
                            </div>
                        </div>

                        <div class="setting-group">
                            <div class="setting-label">Exam Duration</div>
                            <div class="input-group">
                                <input type="number" class="setting-input" id="duration-<?php echo $s['id']; ?>"
                                    value="<?php echo $s['duration_minutes'] ?? 45; ?>" min="5" max="180">
                                <span class="input-suffix">minutes</span>
                            </div>
                        </div>

                        <div class="setting-group">
                            <div class="setting-label">Passing Score</div>
                            <div class="input-group">
                                <input type="number" class="setting-input" id="passing-<?php echo $s['id']; ?>"
                                    value="<?php echo $s['passing_score'] ?? 70; ?>" min="0" max="100">
                                <span class="input-suffix">%</span>
                            </div>
                        </div>

                        <button class="btn btn-primary" onclick="saveSettings(<?php echo $s['id']; ?>)">
                            Save Changes
                        </button>
                        <div id="status-<?php echo $s['id']; ?>" style="text-align:center;margin-top:0.75rem"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        async function saveSettings(subjectId) {
            const duration = document.getElementById('duration-' + subjectId).value;
            const passing = document.getElementById('passing-' + subjectId).value;
            const statusEl = document.getElementById('status-' + subjectId);

            const res = await fetch('../api/admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_subject',
                    id: subjectId,
                    duration_minutes: parseInt(duration),
                    passing_score: parseInt(passing)
                })
            });

            const data = await res.json();

            if (data.success) {
                statusEl.innerHTML = '<span class="status-badge saved">✓ Saved</span>';
                setTimeout(() => statusEl.innerHTML = '', 2000);
            } else {
                statusEl.innerHTML = '<span style="color:#ef4444;font-size:0.8rem">Error: ' + data.message + '</span>';
            }
        }
    </script>
</body>

</html>