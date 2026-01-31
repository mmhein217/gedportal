# GED Exam Management System

A comprehensive, database-driven exam management platform with multi-role authentication, secure exam mode, and complete analytics tracking.

## 🎯 Features

### **Multi-Role System**
- **Students**: Take exams, track progress, view analytics
- **Teachers**: Control exam mode, edit questions, monitor students
- **Admins**: Manage users, questions, and system settings

### **Secure Exam Mode (GED-Style)**
- ✅ Fullscreen enforcement (cannot exit until completion)
- ✅ Keyboard blocking (Alt+Tab, Ctrl+W, F11, etc.)
- ✅ Tab/window switch detection
- ✅ Violation tracking (max 5 violations)
- ✅ Right-click and copy/paste disabled
- ✅ Auto-submit on timeout or violations
- ✅ Clear warning modals

### **Complete Database Integration**
- MySQL database with 11 comprehensive tables
- User management (students, teachers, admins)
- Question bank with 120+ questions
- Exam attempts with auto-grading
- Learning analytics and time tracking
- Violation logging
- Historical question sets (by year)
- Audit trail

### **4 GED Subjects**
- **Mathematical Reasoning** (115 min, 30 questions)
- **Reasoning Through Language Arts** (150 min, 30 questions)
- **Science** (90 min, 30 questions)
- **Social Studies** (70 min, 30 questions)

### **Teacher Controls**
- Enable/disable exam mode per subject
- Toggle question shuffling
- Edit questions and answers
- Monitor student progress
- View time spent learning
- Access detailed analytics

### **Admin Panel**
- User management (CRUD operations)
- Question bank editor
- Historical question sets (2023, 2024, 2025)
- System-wide analytics
- Audit log access
- System settings configuration

## 🚀 Quick Start

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Modern web browser (Chrome, Firefox, Edge)

### Installation

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Run `database/database_schema.sql`
   - Run `database/seed_data.sql`

3. **Access Application**
   - Navigate to: `http://localhost/Pearson/login.php`

### Default Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password123 |
| Teacher | teacher1 | password123 |
| Student | student1 | password123 |

> **⚠️ Important**: Change these passwords after first login!

## 📖 Documentation

- **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Detailed installation and configuration
- **[Walkthrough](walkthrough.md)** - Complete feature documentation
- **[Implementation Plan](implementation_plan.md)** - Technical architecture

## 🗄️ Database Schema

### Core Tables
- `users` - User accounts with role-based access
- `subjects` - GED exam subjects
- `questions` - Question bank with answers
- `exams` - Exam configurations
- `exam_attempts` - Student submissions
- `learning_analytics` - Study time tracking
- `exam_violations` - Security breach logs
- `question_sets` - Historical questions by year
- `audit_log` - System activity tracking

## 🔒 Security Features

### Exam Integrity
- Fullscreen mode enforcement
- Keyboard shortcut blocking
- Tab/window switch detection
- Violation tracking and logging
- Auto-submit on security breaches

### Data Security
- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- Session management with timeout
- Role-based access control
- Audit logging

## 📊 Analytics & Tracking

### Student Analytics
- Total exams taken
- Average scores
- Study time (hours)
- Questions answered
- Subject-specific performance

### Teacher View
- All students' progress
- Time spent per student
- Exam completion rates
- Violation reports

### Admin View
- System-wide statistics
- User counts by role
- Total exams taken
- Audit trail

## 🎓 Educational Value

- Realistic GED exam practice
- Timed exams with auto-submit
- Immediate feedback and explanations
- Progress tracking
- Historical question sets
- Secure testing environment

## 📁 Project Structure

```
d:/Pearson/
├── database/          # SQL schema and seed data
├── backend/           # PHP configuration and auth
├── middleware/        # Session validation
├── api/              # REST API endpoints
├── student/          # Student portal
├── teacher/          # Teacher dashboard
├── admin/            # Admin panel
├── login.php         # Multi-role login
└── styles.css        # Enhanced styling
```

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+, MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Database**: MySQL with PDO
- **Security**: Bcrypt, Prepared Statements, CSRF Protection
- **UI/UX**: Modern gradient design, responsive layout

## 🔧 Configuration

Default configuration in `backend/config.php`:
```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (blank)
DB_NAME: exam_management
```

## 📝 API Endpoints

- `api/questions.php` - Question retrieval with shuffling
- `api/exam.php` - Exam submission and grading
- `api/analytics.php` - Student analytics
- `api/admin.php` - System statistics

## 🐛 Troubleshooting

### Database Connection Error
- Ensure XAMPP MySQL is running
- Verify database credentials in `backend/config.php`
- Check that `exam_management` database exists

### Login Issues
- Clear browser cache and cookies
- Verify seed data was loaded
- Check users table in phpMyAdmin

### Fullscreen Not Working
- Use modern browser (Chrome/Firefox/Edge)
- Allow fullscreen permission when prompted
- Check browser console for errors

## 🎯 Future Enhancements

- [ ] Results page with detailed breakdown
- [ ] Question editing UI
- [ ] User management interface
- [ ] Exam history viewer
- [ ] Email notifications
- [ ] Password reset functionality
- [ ] PDF report export
- [ ] Mobile app version

## 📞 Support

For issues:
1. Check SETUP_GUIDE.md
2. Review browser console (F12)
3. Verify XAMPP services are running
4. Check database connection

## 📄 License

Educational use - GED Exam Preparation Platform

---

**Ready to start? Follow the Quick Start guide above and login with the default credentials!**
