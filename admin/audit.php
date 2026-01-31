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
    <title>Audit Logs - Admin</title>
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

        .btn-secondary {
            background: var(--white);
            color: var(--dark);
            border: 1px solid #e5e7eb;
        }

        .btn-success {
            background: var(--success);
            color: var(--white);
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
        }

        .audit-list {
            padding: 0;
        }

        .audit-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s;
        }

        .audit-item:hover {
            background: #f9fafb;
        }

        .audit-item:last-child {
            border-bottom: none;
        }

        .audit-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .audit-icon.login {
            background: #dbeafe;
        }

        .audit-icon.exam {
            background: #d1fae5;
        }

        .audit-icon.user {
            background: #fce7f3;
        }

        .audit-icon.security {
            background: #fee2e2;
        }

        .audit-icon.default {
            background: #f3f4f6;
        }

        .audit-content {
            flex: 1;
            min-width: 0;
        }

        .audit-action {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .audit-details {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .audit-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
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
                <a href="analytics.php" class="nav-item"><span class="icon">📈</span><span
                        class="label">Analytics</span></a>
                <a href="audit.php" class="nav-item active"><span class="icon">📋</span><span class="label">Audit
                        Logs</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>📋 Audit Logs</h1>
                    <p>Track system activity and security events</p>
                </div>
                <button class="btn btn-success" onclick="downloadLogs()">📥 Export CSV</button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Activity Log</h2>
                    <div class="filters">
                        <select id="filterAction" onchange="loadLogs()">
                            <option value="">All Actions</option>
                            <option value="LOGIN">Login</option>
                            <option value="LOGOUT">Logout</option>
                            <option value="EXAM_STARTED">Exam Started</option>
                            <option value="EXAM_SUBMITTED">Exam Submitted</option>
                            <option value="EXAM_VIOLATION">Violations</option>
                        </select>
                        <select id="filterLimit" onchange="loadLogs()">
                            <option value="50">Last 50</option>
                            <option value="100">Last 100</option>
                            <option value="500">Last 500</option>
                        </select>
                    </div>
                </div>
                <div class="audit-list" id="auditList"></div>
            </div>
        </main>
    </div>

    <script>
        let allLogs = [];

        async function loadLogs() {
            const limit = document.getElementById('filterLimit').value;
            const actionFilter = document.getElementById('filterAction').value;

            const res = await fetch('../api/admin.php?action=get_audit_logs&limit=' + limit);
            const data = await res.json();

            const container = document.getElementById('auditList');

            if (!data.success) {
                container.innerHTML = `<div class="empty-state" style="color:#db0020"><p>Error: ${data.message}</p></div>`;
                return;
            }

            if (!data.data.length) {
                container.innerHTML = '<div class="empty-state"><p>No audit logs found</p></div>';
                return;
            }

            allLogs = data.data;
            let logs = allLogs;

            if (actionFilter) {
                logs = logs.filter(l => l.action.includes(actionFilter));
            }

            container.innerHTML = logs.map(log => {
                const iconClass = getIconClass(log.action);
                const icon = getIcon(log.action);
                const badgeClass = getBadgeClass(log.action);
                const time = new Date(log.created_at).toLocaleString();

                return `
                    <div class="audit-item">
                        <div class="audit-icon ${iconClass}">${icon}</div>
                        <div class="audit-content">
                            <div class="audit-action">${formatAction(log.action)}</div>
                            <div class="audit-details">${log.details || 'No additional details'}</div>
                            <div class="audit-meta">
                                <span>👤 ${log.full_name || 'System'}</span>
                                <span>🕐 ${time}</span>
                                ${log.ip_address ? `<span>🌐 ${log.ip_address}</span>` : ''}
                            </div>
                        </div>
                        <span class="badge ${badgeClass}">${log.action}</span>
                    </div>
                `;
            }).join('');
        }

        function getIconClass(action) {
            if (action.includes('LOGIN') || action.includes('LOGOUT')) return 'login';
            if (action.includes('EXAM')) return 'exam';
            if (action.includes('VIOLATION')) return 'security';
            if (action.includes('USER')) return 'user';
            return 'default';
        }

        function getIcon(action) {
            if (action.includes('LOGIN')) return '🔑';
            if (action.includes('LOGOUT')) return '🚪';
            if (action.includes('EXAM_STARTED')) return '📝';
            if (action.includes('EXAM_SUBMITTED')) return '✓';
            if (action.includes('VIOLATION')) return '⚠️';
            if (action.includes('USER')) return '👤';
            return '📌';
        }

        function getBadgeClass(action) {
            if (action.includes('VIOLATION')) return 'badge-danger';
            if (action.includes('SUBMITTED')) return 'badge-success';
            if (action.includes('STARTED')) return 'badge-info';
            if (action.includes('LOGIN')) return 'badge-info';
            return 'badge-warning';
        }

        function formatAction(action) {
            return action.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
        }

        function downloadLogs() {
            if (!allLogs.length) return alert('No logs to export');

            let csv = 'Date,User,Action,Details,IP Address\n';
            allLogs.forEach(l => {
                csv += `"${l.created_at}","${l.full_name || 'System'}","${l.action}","${(l.details || '').replace(/"/g, '""')}","${l.ip_address || ''}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Audit_Logs_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
        }

        loadLogs();
    </script>
</body>

</html>