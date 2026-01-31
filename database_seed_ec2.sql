-- ============================================
-- GED Exam Management System - Database Seed
-- Generated for AWS EC2 Deployment
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing database if exists
DROP DATABASE IF EXISTS exam_management;
CREATE DATABASE exam_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_management;

-- ============================================
-- Users Table
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================
-- Subjects Table
-- ============================================
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    duration_minutes INT NOT NULL,
    total_questions INT NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code)
) ENGINE=InnoDB;

-- ============================================
-- Questions Table (Updated Schema)
-- ============================================
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type VARCHAR(50) DEFAULT 'multiple_choice',
    option_a TEXT NULL,
    option_b TEXT NULL,
    option_c TEXT NULL,
    option_d TEXT NULL,
    correct_answer TEXT NULL,
    answer_options JSON NULL,
    question_image_url VARCHAR(255) NULL,
    explanation TEXT,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    year INT DEFAULT 2025,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_subject (subject_id),
    INDEX idx_year (year),
    INDEX idx_difficulty (difficulty)
) ENGINE=InnoDB;

-- ============================================
-- Question Sets
-- ============================================
CREATE TABLE question_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    subject_id INT NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    INDEX idx_year (year),
    INDEX idx_subject (subject_id)
) ENGINE=InnoDB;

-- ============================================
-- Question Set Items
-- ============================================
CREATE TABLE question_set_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_set_id INT NOT NULL,
    question_id INT NOT NULL,
    display_order INT DEFAULT 0,
    FOREIGN KEY (question_set_id) REFERENCES question_sets(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_set_question (question_set_id, question_id),
    INDEX idx_set (question_set_id),
    INDEX idx_question (question_id)
) ENGINE=InnoDB;

-- ============================================
-- Exams Table
-- ============================================
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    created_by INT NOT NULL,
    exam_name VARCHAR(100) NOT NULL,
    exam_mode ENUM('practice', 'timed', 'review') DEFAULT 'practice',
    shuffle_questions BOOLEAN DEFAULT FALSE,
    shuffle_options BOOLEAN DEFAULT FALSE,
    time_limit_minutes INT,
    question_set_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    is_locked BOOLEAN DEFAULT FALSE,
    start_time DATETIME,
    end_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_set_id) REFERENCES question_sets(id) ON DELETE SET NULL,
    INDEX idx_subject (subject_id),
    INDEX idx_active (is_active),
    INDEX idx_creator (created_by)
) ENGINE=InnoDB;

-- ============================================
-- Exam Attempts Table
-- ============================================
CREATE TABLE exam_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    time_spent_seconds INT DEFAULT 0,
    score DECIMAL(5,2),
    total_questions INT NOT NULL,
    correct_answers INT DEFAULT 0,
    incorrect_answers INT DEFAULT 0,
    unanswered INT DEFAULT 0,
    answers_json JSON,
    violations_count INT DEFAULT 0,
    violation_details JSON,
    status ENUM('in_progress', 'completed', 'abandoned') DEFAULT 'in_progress',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_exam (exam_id),
    INDEX idx_student (student_id),
    INDEX idx_status (status),
    INDEX idx_start_time (start_time)
) ENGINE=InnoDB;

-- ============================================
-- Learning Analytics Table
-- ============================================
CREATE TABLE learning_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    session_date DATE NOT NULL,
    time_spent_seconds INT DEFAULT 0,
    questions_viewed INT DEFAULT 0,
    questions_answered INT DEFAULT 0,
    questions_flagged INT DEFAULT 0,
    session_start DATETIME,
    session_end DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_subject (subject_id),
    INDEX idx_date (session_date)
) ENGINE=InnoDB;

-- ============================================
-- Exam Violations Table
-- ============================================
CREATE TABLE exam_violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    violation_type VARCHAR(50) NOT NULL,
    violation_details TEXT,
    timestamp DATETIME NOT NULL,
    FOREIGN KEY (attempt_id) REFERENCES exam_attempts(id) ON DELETE CASCADE,
    INDEX idx_attempt (attempt_id),
    INDEX idx_type (violation_type),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB;

-- ============================================
-- System Settings Table
-- ============================================
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- ============================================
-- Audit Log Table
-- ============================================
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_table (table_name),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA
-- ============================================

-- Users (Password: password123)
INSERT INTO users (username, password_hash, role, email, full_name) VALUES
('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'admin', 'admin@gedexam.com', 'System Administrator'),
('teacher1', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'teacher', 'teacher1@gedexam.com', 'John Smith'),
('student1', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'student', 'student1@gedexam.com', 'Michael Brown');

-- Subjects
INSERT INTO subjects (id, name, code, duration_minutes, total_questions, description) VALUES
(1, 'Mathematical Reasoning', 'MATH', 115, 46, 'Covers algebra, geometry, statistics, and quantitative problem solving'),
(2, 'Reasoning Through Language Arts', 'LANG', 150, 51, 'Includes reading comprehension, grammar, and writing skills'),
(3, 'Science', 'SCI', 90, 34, 'Life science, physical science, and earth & space science'),
(4, 'Social Studies', 'SOC', 70, 35, 'Civics, government, U.S. history, economics, and geography');

-- System Settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('exam_mode_enabled', 'true', 'boolean', 'Global exam mode toggle'),
('max_violations_allowed', '5', 'number', 'Maximum violations before exam termination'),
('require_fullscreen', 'true', 'boolean', 'Require fullscreen mode for exams'),
('allow_question_flagging', 'true', 'boolean', 'Allow students to flag questions'),
('show_explanations', 'true', 'boolean', 'Show answer explanations after exam'),
('maintenance_mode', 'false', 'boolean', 'System maintenance mode');

SET FOREIGN_KEY_CHECKS = 1;
