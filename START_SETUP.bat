@echo off
echo ========================================
echo GED Exam Management System - Quick Setup
echo ========================================
echo.
echo Your files are in: D:\Pearson
echo.
echo Opening setup pages in your browser...
echo.

REM Open phpMyAdmin for database setup
start http://localhost/phpmyadmin

REM Wait 2 seconds
timeout /t 2 /nobreak >nul

REM Open setup verification page
start http://localhost/Pearson/setup.html

echo.
echo ========================================
echo Pages opened in your browser:
echo 1. phpMyAdmin - for database setup
echo 2. Setup Guide - step-by-step instructions
echo ========================================
echo.
echo NEXT STEPS:
echo 1. In phpMyAdmin, click "SQL" tab
echo 2. Copy content from: D:\Pearson\database\database_schema.sql
echo 3. Paste and click "Go"
echo 4. Then copy content from: D:\Pearson\database\seed_data.sql
echo 5. Paste and click "Go"
echo 6. Go to: http://localhost/Pearson/login.php
echo 7. Login with: student1 / password123
echo.
echo Press any key to exit...
pause >nul
