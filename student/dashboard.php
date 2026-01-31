<?php
require_once __DIR__ . '/../middleware/auth_check.php';
requireAuth(['student']);

// Force No-Cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$user = getCurrentUser();

$subjects = [];
$stats = ['total_exams' => 0, 'avg_score' => 0, 'total_time' => 0];
$studyStats = ['study_time' => 0];
$errorMsg = '';

$db = null;
try {
    $db = getDB();
} catch (Exception $e) {
    $errorMsg = "DB Connection Error: " . $e->getMessage();
}

if ($db) {
    // 1. Subjects
    try {
        $stmt = $db->query("SELECT * FROM subjects ORDER BY id");
        if ($stmt) {
            $subjects = $stmt->fetchAll();
        }
    } catch (Exception $e) {
        $errorMsg = "Subjects Query Error: " . $e->getMessage();
    }

    // 2. Exam Attempts
    try {
        $sql = "SELECT COUNT(*) as total_exams, AVG(score) as avg_score, SUM(time_spent_seconds) as total_time FROM exam_attempts WHERE student_id = ? AND status = 'completed'";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user['id']]);
        $stats = $stmt->fetch();
    } catch (Exception $e) {
        // Silent fail for stats, simple default
    }

    // 3. Learning Analytics
    try {
        // Corrected query (removed invalid activity_type)
        $sql = "SELECT SUM(time_spent_seconds) as study_time FROM learning_analytics WHERE student_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user['id']]);
        $studyStats = $stmt->fetch();
    } catch (Exception $e) {
        // Silent fail
    }
}

// Map Subject Codes to User-Preferred Slugs
function getSubjectSlug($code)
{
    switch ($code) {
        case 'MATH':
            return 'math';
        case 'SCI':
            return 'science';
        case 'SOC':
            return 'social_studies';
        case 'LANG':
            return 'reasoning_language_arts';
        default:
            return strtolower($code);
    }
}

$subjectIcons = ['MATH' => '∑', 'LANG' => '📖', 'SCI' => '🔬', 'SOC' => '🌍'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GED Prep</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Pearson/Academic Style - Clean & Accessible */
        :root {
            --primary: #0077c8;
            --secondary: #00a651;
            --bg: #f5f7fa;
            --surface: #ffffff;
            --text-main: #2d2d2d;
            --text-muted: #595959;
            --border: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .header {
            background: var(--surface);
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .brand-icon {
            font-size: 1.5rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #e6f3ff;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 1px solid var(--primary);
        }

        .logout-btn {
            color: #dc2626;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #fee2e2;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--surface);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Section Title */
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 24px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* Error Box */
        .error-box {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        /* Subjects Grid */
        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }

        .subject-card {
            background: var(--surface);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
        }

        .subject-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
            border-color: #b0c4de;
        }

        .subject-header {
            padding: 1.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: start;
            border-bottom: 1px solid #f0f0f0;
            background: linear-gradient(to right, #ffffff, #f9fafb);
        }

        .subject-icon {
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 119, 200, 0.2);
        }

        .subject-info h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--text-main);
        }

        .subject-meta {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .subject-actions {
            padding: 1.25rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            background: #fff;
            margin-top: auto;
        }

        .action-btn {
            text-decoration: none;
            padding: 0.8rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-study {
            background: #f0f9ff;
            color: var(--primary);
            border-color: rgba(0, 119, 200, 0.1);
        }

        .btn-study:hover {
            background: #e0f2fe;
            border-color: var(--primary);
        }

        .btn-review {
            background: #fff7ed;
            color: #c2410c;
            border-color: rgba(194, 65, 12, 0.1);
        }

        .btn-review:hover {
            background: #ffedd5;
            border-color: #c2410c;
        }

        .btn-exam {
            background: var(--secondary);
            color: white;
            box-shadow: 0 2px 4px rgba(0, 166, 81, 0.2);
        }

        .btn-exam:hover {
            background: #008f45;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 166, 81, 0.3);
        }

        .btn-subtext {
            font-size: 0.7rem;
            font-weight: 400;
            opacity: 0.8;
        }

        @media(max-width:768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .subjects-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="brand">
                <div class="brand-icon">📚</div>
                <h1>GED Prep Portal</h1>
            </div>
            <div class="user-profile">
                <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                <div style="text-align:left">
                    <div style="font-weight:700;font-size:0.95rem"><?php echo htmlspecialchars($user['full_name']); ?>
                    </div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">Student</div>
                </div>
                <a href="../logout.php" class="logout-btn">Log Out</a>
            </div>
        </header>

        <?php if ($errorMsg): ?>
            <div class="error-box">⚠️ <?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Exams Completed</div>
                <div class="stat-value"><?php echo $stats['total_exams'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg. Score</div>
                <div class="stat-value"><?php echo round($stats['avg_score'] ?? 0); ?><span
                        style="font-size:1rem">%</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Study Time</div>
                <div class="stat-value"><?php echo floor(($studyStats['study_time'] ?? 0) / 60); ?><span
                        style="font-size:1rem">m</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Progress</div>
                <div class="stat-value">--</div>
            </div>
        </div>

        <h2 class="section-title">Your Learning Path</h2>

        <?php if (empty($subjects) && !$errorMsg): ?>
            <div style="text-align:center;padding:4rem;background:white;border-radius:12px;color:var(--text-muted)">
                <h3>No Subjects Available</h3>
                <p>Please contact your administrator.</p>
            </div>
        <?php endif; ?>

        <div class="subjects-grid">
            <?php foreach ($subjects as $subject):
                $code = $subject['code'];
                $slug = getSubjectSlug($code);
                $icon = $subjectIcons[$code] ?? '📝';
                ?>
                <div class="subject-card">
                    <div class="subject-header">
                        <div class="subject-icon"><?php echo $icon; ?></div>
                        <div class="subject-info">
                            <h3><?php echo htmlspecialchars($subject['name']); ?></h3>
                            <div class="subject-meta">
                                <?php echo $subject['duration_minutes']; ?> min exam •
                                <?php echo $subject['total_questions']; ?> questions
                            </div>
                        </div>
                    </div>
                    <div class="subject-actions">
                        <a href="study.php?subject=<?php echo $slug; ?>" class="action-btn btn-study">
                            <span>📖 Study</span>
                            <span class="btn-subtext">Untimed Practice</span>
                        </a>
                        <a href="review.php?subject=<?php echo $slug; ?>" class="action-btn btn-review">
                            <span>🔍 Review</span>
                            <span class="btn-subtext">View Answers</span>
                        </a>
                        <a href="exam.php?subject=<?php echo $slug; ?>" class="action-btn btn-exam">
                            <span>📝 Start Exam</span>
                            <span class="btn-subtext">Timed & Secure</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div style="text-align:center;padding:1rem;color:#9ca3af;font-size:0.8rem">
        System Version 2.1 (Updated)
    </div>
</body>

</html>