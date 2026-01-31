<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GED Exam Management System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fa;
            padding: 2rem;
        }

        .login-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .login-logo h1 {
            font-size: 1.75rem;
            color: #1f2937;
            margin: 0;
        }

        .login-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .role-btn {
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            color: #4b5563;
        }

        .role-btn:hover {
            border-color: #0077c8;
            background: #f0f9ff;
        }

        .role-btn.active {
            border-color: #0077c8;
            background: #e6f3ff;
            color: #0077c8;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #0077c8;
            box-shadow: 0 0 0 3px rgba(0, 119, 200, 0.1);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .form-checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-checkbox label {
            font-size: 0.9rem;
            color: #4b5563;
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            padding: 0.875rem;
            background: #0077c8;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 0.875rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .hidden {
            display: none;
        }

        .demo-credentials {
            margin-top: 2rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .demo-credentials h4 {
            margin: 0 0 0.5rem 0;
            color: #374151;
            font-size: 0.9rem;
        }

        .demo-credentials p {
            margin: 0.25rem 0;
            color: #6b7280;
        }

        .demo-credentials strong {
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <div style="font-size:3rem;">🎓</div>
                    <h1 style="color:#0077c8">Pearson GED</h1>
                </div>
                <p class="login-subtitle">Sign in to continue</p>
            </div>

            <div id="alertBox" class="alert hidden"></div>

            <form id="loginForm">
                <div class="role-selector">
                    <button type="button" class="role-btn active" data-role="student">
                        👨‍🎓 Student
                    </button>
                    <button type="button" class="role-btn" data-role="teacher">
                        👨‍🏫 Teacher
                    </button>
                    <button type="button" class="role-btn" data-role="admin">
                        👨‍💼 Admin
                    </button>
                </div>

                <input type="hidden" id="role" name="role" value="student">

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input"
                        placeholder="Enter your username" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                        placeholder="Enter your password" required>
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    Sign In
                </button>
            </form>


        </div>
    </div>

    <script>
        // Role selection
        const roleButtons = document.querySelectorAll('.role-btn');
        const roleInput = document.getElementById('role');

        roleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                roleButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                roleInput.value = btn.dataset.role;
            });
        });

        // Form submission
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const alertBox = document.getElementById('alertBox');

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(loginForm);
            loginBtn.disabled = true;
            loginBtn.textContent = 'Signing in...';
            hideAlert();

            try {
                const response = await fetch('backend/auth.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('Login successful! Redirecting...', 'success');

                    // Redirect based on role
                    setTimeout(() => {
                        switch (data.data.role) {
                            case 'admin':
                                window.location.href = 'admin/dashboard.php';
                                break;
                            case 'teacher':
                                window.location.href = 'teacher/dashboard.php';
                                break;
                            case 'student':
                                window.location.href = 'student/dashboard.php';
                                break;
                        }
                    }, 1000);
                } else {
                    showAlert(data.message, 'error');
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'Sign In';
                }
            } catch (error) {
                showAlert('Connection error. Please ensure XAMPP is running.', 'error');
                loginBtn.disabled = false;
                loginBtn.textContent = 'Sign In';
            }
        });

        function showAlert(message, type) {
            alertBox.textContent = message;
            alertBox.className = `alert alert-${type}`;
            alertBox.classList.remove('hidden');
        }

        function hideAlert() {
            alertBox.classList.add('hidden');
        }
    </script>
</body>

</html>