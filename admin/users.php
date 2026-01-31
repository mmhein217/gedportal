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
    <title>Users - Admin</title>
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

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
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
            letter-spacing: 0.5px;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
        }

        .user-avatar.student {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .user-avatar.teacher {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .user-name {
            font-weight: 600;
        }

        .user-email {
            font-size: 0.8rem;
            color: var(--gray);
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-admin {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-teacher {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-student {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
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
            font-size: 0.875rem;
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
                <a href="users.php" class="nav-item active"><span class="icon">👥</span><span
                        class="label">Users</span></a>
                <a href="exams.php" class="nav-item"><span class="icon">⚙️</span><span class="label">Exam
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
                    <h1>👥 User Management</h1>
                    <p>Manage students, teachers, and administrators</p>
                </div>
                <button class="btn btn-primary" onclick="openModal()">+ Add User</button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>All Users</h2>
                    <div class="filters">
                        <select id="filterRole" onchange="loadUsers()">
                            <option value="">All Roles</option>
                            <option value="student">Students</option>
                            <option value="teacher">Teachers</option>
                            <option value="admin">Admins</option>
                        </select>
                        <input type="text" id="searchInput" placeholder="Search..." onkeyup="loadUsers()">
                    </div>
                </div>
                <div style="overflow-x:auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- User Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add User</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="userForm" onsubmit="saveUser(event)">
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="fullName" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email" required placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="username" required placeholder="johndoe">
                    </div>
                    <div class="form-group">
                        <label>Password <span id="pwdHint" style="color:#6b7280;font-weight:400"></span></label>
                        <input type="password" id="password" placeholder="Enter password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select id="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        async function loadUsers() {
            const role = document.getElementById('filterRole').value;
            const search = document.getElementById('searchInput').value;

            let url = '../api/admin.php?action=get_users';
            if (role) url += '&role=' + role;

            const res = await fetch(url);
            const data = await res.json();

            const tbody = document.getElementById('usersTable');
            if (!data.success) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:2rem;color:#db0020">Error: ${data.message}</td></tr>`;
                return;
            }
            if (!data.data.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:2rem;color:#6b7280">No users found</td></tr>';
                return;
            }

            let filtered = data.data;
            if (search) {
                filtered = filtered.filter(u =>
                    u.full_name.toLowerCase().includes(search.toLowerCase()) ||
                    u.email.toLowerCase().includes(search.toLowerCase())
                );
            }

            tbody.innerHTML = filtered.map(u => `
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar ${u.role}">${u.full_name.charAt(0).toUpperCase()}</div>
                            <div>
                                <div class="user-name">${u.full_name}</div>
                                <div class="user-email">${u.email}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-${u.role}">${u.role}</span></td>
                    <td><span class="badge ${u.is_active ? 'badge-active' : 'badge-inactive'}">${u.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>${new Date(u.created_at).toLocaleDateString()}</td>
                    <td class="actions">
                        <button class="btn btn-secondary btn-sm" onclick='editUser(${JSON.stringify(u)})'>Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('pwdHint').textContent = '';
            document.getElementById('userModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('userModal').classList.remove('show');
        }

        function editUser(u) {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = u.id;
            document.getElementById('fullName').value = u.full_name;
            document.getElementById('email').value = u.email;
            document.getElementById('username').value = u.username;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('pwdHint').textContent = '(leave blank to keep current)';
            document.getElementById('role').value = u.role;
            document.getElementById('userModal').classList.add('show');
        }

        async function saveUser(e) {
            e.preventDefault();
            const id = document.getElementById('userId').value;
            const action = id ? 'update_user' : 'add_user';

            const payload = {
                action,
                id: id || undefined,
                full_name: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                username: document.getElementById('username').value,
                role: document.getElementById('role').value
            };

            const pwd = document.getElementById('password').value;
            if (pwd) payload.password = pwd;

            const res = await fetch('../api/admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) { closeModal(); loadUsers(); }
            else alert('Error: ' + data.message);
        }

        async function deleteUser(id) {
            if (!confirm('Delete this user?')) return;

            const res = await fetch('../api/admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_user', id })
            });
            const data = await res.json();

            if (data.success) loadUsers();
            else alert('Error: ' + data.message);
        }

        loadUsers();
    </script>
</body>

</html>