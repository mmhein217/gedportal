# 🚀 Quick Start - Next Steps

## ✅ System Status
- **Apache**: Running ✓
- **MySQL**: Running ✓
- **Files**: All created ✓

## 📋 Complete These 3 Steps:

### Step 1: Open Setup Page
Open your browser and go to:
```
http://localhost/Pearson/setup.html
```
This page has an interactive guide with all instructions.

**OR** follow the manual steps below:

---

### Step 2: Create Database (Manual)

1. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`

2. **Run Schema SQL**
   - Click the **"SQL"** tab at the top
   - Open file: `d:\Pearson\database\database_schema.sql`
   - Copy ALL content (Ctrl+A, Ctrl+C)
   - Paste into the SQL box in phpMyAdmin
   - Click **"Go"** button
   - ✅ You should see: "Database exam_management created successfully"

3. **Load Sample Data**
   - Make sure `exam_management` database is selected (left sidebar)
   - Click **"SQL"** tab again
   - Open file: `d:\Pearson\database\seed_data.sql`
   - Copy ALL content (Ctrl+A, Ctrl+C)
   - Paste into the SQL box
   - Click **"Go"** button
   - ✅ You should see: "X rows inserted" messages

---

### Step 3: Test Login

1. **Open Login Page**
   - Go to: `http://localhost/Pearson/login.php`

2. **Try Each Role**

   **Admin Login:**
   - Username: `admin`
   - Password: `password123`
   - Should see: Admin Dashboard with system statistics

   **Teacher Login:**
   - Username: `teacher1`
   - Password: `password123`
   - Should see: Teacher Dashboard with exam controls

   **Student Login:**
   - Username: `student1`
   - Password: `password123`
   - Should see: Student Dashboard with subject cards

---

## 🎯 Test Secure Exam Mode

1. Login as **student1**
2. Click on any subject (e.g., "Mathematical Reasoning")
3. Read the warning modal about secure exam mode
4. Click "I Understand - Start Exam"
5. **Verify these security features work:**
   - ✅ Screen goes fullscreen automatically
   - ✅ Try pressing Alt+Tab (should be blocked)
   - ✅ Try pressing Escape (should show violation)
   - ✅ Try right-clicking (should be disabled)
   - ✅ Violation counter increases
   - ✅ Timer counts down
   - ✅ Can answer questions and navigate

---

## 📚 Documentation Files

- **`SETUP_GUIDE.md`** - Complete installation guide
- **`README.md`** - System overview and features
- **`walkthrough.md`** - Detailed technical documentation

---

## 🐛 Troubleshooting

**Database connection error?**
- Check XAMPP MySQL is running (green in control panel)
- Verify you ran BOTH SQL files (schema + seed data)

**Login not working?**
- Clear browser cache (Ctrl+Shift+Delete)
- Check that seed data was loaded successfully
- Try: `SELECT * FROM users` in phpMyAdmin SQL tab

**Fullscreen not working?**
- Use Chrome, Firefox, or Edge (not IE)
- Allow fullscreen permission when browser asks

---

## ✨ You're All Set!

Once you complete the 3 steps above, your exam management system is ready to use!

**Quick Links:**
- Setup Page: http://localhost/Pearson/setup.html
- phpMyAdmin: http://localhost/phpmyadmin
- Login Page: http://localhost/Pearson/login.php
