<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['admin']);
$user = getCurrentUser();
$pageName = $_GET['page'] ?? 'Admin Feature';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo htmlspecialchars($pageName); ?> - Admin
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 2rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        p {
            color: #6b7280;
            font-size: 1.125rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .feature-list {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .feature-list h3 {
            color: #374151;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .feature-list ul {
            list-style: none;
            color: #6b7280;
        }

        .feature-list li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .feature-list li::before {
            content: "⏳";
            position: absolute;
            left: 0;
        }

        .btn {
            display: inline-block;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .admin-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e5e7eb;
            color: #9ca3af;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">🚧</div>
        <h1>
            <?php echo htmlspecialchars($pageName); ?>
        </h1>
        <p>This feature is currently under development and will be available soon.</p>

        <div class="feature-list">
            <h3>Planned Features:</h3>
            <ul>
                <li>Full CRUD operations</li>
                <li>Advanced search and filtering</li>
                <li>Bulk actions support</li>
                <li>Export functionality</li>
                <li>Real-time updates</li>
            </ul>
        </div>

        <a href="dashboard.php" class="btn">← Back to Dashboard</a>

        <div class="admin-info">
            <p>Logged in as: <strong>
                    <?php echo htmlspecialchars($user['full_name']); ?>
                </strong> (Administrator)</p>
        </div>
    </div>
</body>

</html>