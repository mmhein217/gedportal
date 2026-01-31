# GED Exam Management System - Setup Guide

## 📋 Prerequisites

- **XAMPP** (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, or Edge recommended)
- Text editor (optional, for configuration)

## 🚀 Installation Steps

### Step 1: Start XAMPP

1. Open XAMPP Control Panel
2. Start **Apache** server
3. Start **MySQL** server
4. Verify both services are running (green indicators)

### Step 2: Create Database

1. Open your web browser
2. Navigate to: `http://localhost/phpmyadmin`
3. Click on "SQL" tab
4. Copy and paste the contents of `database/database_schema.sql`
5. Click "Go" to execute
6. The database `exam_management` will be created with all tables

### Step 3: Load Sample Data

1. In phpMyAdmin, select the `exam_management` database
2. Click on "SQL" tab
3. Copy and paste the contents of `database/seed_data.sql`
4. Click "Go" to execute
5. Sample users, questions, and settings will be loaded

### Step 4: Configure Application

The default configuration should work with standard XAMPP installation:
- **Database Host**: localhost
- **Database User**: root
- **Database Password**: (blank)
- **Database Name**: exam_management

If your XAMPP uses different credentials, edit `backend/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your password if different
define('DB_NAME', 'exam_management');
```

### Step 5: Access the Application

1. Place the `Pearson` folder in your XAMPP `htdocs` directory
   - Default path: `C:\xampp\htdocs\Pearson`
2. Open your browser and navigate to: `http://localhost/Pearson/login.php`

## 👥 Default Login Credentials

### Administrator
- **Username**: `admin`
- **Password**: `password123`
- **Access**: Full system control, user management, question editing

### Teacher
- **Username**: `teacher1`
- **Password**: `password123`
- **Access**: Exam mode control, question editing, student monitoring

### Student
- **Username**: `student1`
- **Password**: `password123`
- **Access**: Take exams, view progress

> **Note**: Change these passwords after first login for security!

## 🎯 Features Overview

### For Students
- Take exams in secure fullscreen mode
- View personal progress and analytics
- Review exam results with explanations
- Track study time across subjects

### For Teachers
- Enable/disable exam mode for subjects
- Toggle question shuffling
- Edit questions and answers
- Monitor student progress and time spent
- View detailed analytics

### For Administrators
- Manage all users (create, edit, delete)
- Full question bank management
- Create historical question sets (by year)
- View system-wide analytics
- Access audit logs
- Configure system settings

## 🔒 Secure Exam Mode Features

When students take an exam, the system enforces:

✅ **Fullscreen Mode**: Mandatory fullscreen, cannot exit
✅ **Keyboard Blocking**: Disabled Alt+Tab, Ctrl+W, F11, Windows key, etc.
✅ **Tab Detection**: Alerts when switching tabs/windows
✅ **Violation Tracking**: Records all security violations
✅ **Auto-Submit**: Exam auto-submits after 5 violations
✅ **No Copy/Paste**: Clipboard operations disabled
✅ **No Right-Click**: Context menu disabled

## 📊 Database Structure

### Main Tables
- `users` - User accounts (students, teachers, admins)
- `subjects` - Exam subjects (Math, Language Arts, Science, Social Studies)
- `questions` - Question bank with answers and explanations
- `exams` - Exam configurations
- `exam_attempts` - Student exam submissions and scores
- `learning_analytics` - Study time and progress tracking
- `exam_violations` - Security violation logs
- `question_sets` - Historical question sets by year
- `audit_log` - System activity tracking

## 🛠️ Troubleshooting

### Database Connection Error
- Ensure XAMPP MySQL is running
- Check database credentials in `backend/config.php`
- Verify database `exam_management` exists in phpMyAdmin

### Login Not Working
- Clear browser cache and cookies
- Check that seed data was loaded successfully
- Verify users exist in phpMyAdmin: `SELECT * FROM users`

### Fullscreen Mode Not Working
- Use a modern browser (Chrome, Firefox, Edge)
- Allow fullscreen permission when prompted
- Some browsers may require HTTPS in production

### Questions Not Loading
- Check browser console for errors (F12)
- Verify questions exist: `SELECT COUNT(*) FROM questions`
- Ensure subject IDs match in database

## 📝 Adding Custom Questions

### Via Admin Panel
1. Login as admin
2. Go to "Question Bank"
3. Click "Add New Question"
4. Fill in question details
5. Save

### Via Database
```sql
INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, year)
VALUES (1, 'Your question here?', 'Option A', 'Option B', 'Option C', 'Option D', 'A', 'Explanation here', 2025);
```

## 🔐 Security Recommendations

1. **Change Default Passwords**: Update all default user passwords
2. **Use HTTPS**: In production, enable SSL/TLS
3. **Regular Backups**: Backup database regularly
4. **Update PHP**: Keep PHP version updated
5. **Secure Config**: Protect `config.php` from direct access

## 📞 Support

For issues or questions:
- Check the troubleshooting section above
- Review browser console for errors (F12)
- Verify XAMPP services are running
- Check database connection and data

## 🎓 System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Browser**: Chrome 90+, Firefox 88+, Edge 90+
- **RAM**: 2GB minimum
- **Storage**: 100MB for application + database

---

**Ready to start? Follow the installation steps above and login with the default credentials!**
