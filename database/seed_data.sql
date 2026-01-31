-- ============================================
-- Seed Data for Exam Management System
-- ============================================

USE exam_management;

-- ============================================
-- Insert Default Users
-- ============================================
-- Password for all default users: "password123"
-- Hash generated with bcrypt cost 12

INSERT INTO users (username, password_hash, role, email, full_name) VALUES
('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'admin', 'admin@gedexam.com', 'System Administrator'),
('teacher1', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'teacher', 'teacher1@gedexam.com', 'John Smith'),
('teacher2', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'teacher', 'teacher2@gedexam.com', 'Sarah Johnson'),
('student1', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'student', 'student1@gedexam.com', 'Michael Brown'),
('student2', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'student', 'student2@gedexam.com', 'Emily Davis'),
('student3', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYqR3p0W0EW', 'student', 'student3@gedexam.com', 'James Wilson');

-- ============================================
-- Insert Subjects
-- ============================================
INSERT INTO subjects (name, code, duration_minutes, total_questions, description) VALUES
('Mathematical Reasoning', 'MATH', 115, 46, 'Covers algebra, geometry, statistics, and quantitative problem solving'),
('Reasoning Through Language Arts', 'LANG', 150, 51, 'Includes reading comprehension, grammar, and writing skills'),
('Science', 'SCI', 90, 34, 'Life science, physical science, and earth & space science'),
('Social Studies', 'SOC', 70, 35, 'Civics, government, U.S. history, economics, and geography');

-- ============================================
-- Insert Math Questions (from existing app.js)
-- ============================================
INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, difficulty, year, created_by) VALUES
(1, 'If 3x + 7 = 22, what is the value of x?', 'x = 3', 'x = 5', 'x = 7', 'x = 9', 'B', 'Subtract 7 from both sides: 3x = 15, then divide by 3: x = 5', 'medium', 2025, 1),
(1, 'What is 25% of 80?', '15', '20', '25', '30', 'B', '25% = 0.25, so 0.25 × 80 = 20', 'easy', 2025, 1),
(1, 'A rectangle has a length of 12 cm and a width of 5 cm. What is its area?', '17 cm²', '34 cm²', '60 cm²', '120 cm²', 'C', 'Area = length × width = 12 × 5 = 60 cm²', 'easy', 2025, 1),
(1, 'What is the value of 2³ + 4²?', '20', '24', '28', '32', 'B', '2³ = 8 and 4² = 16, so 8 + 16 = 24', 'medium', 2025, 1),
(1, 'If a shirt costs $45 and is on sale for 20% off, what is the sale price?', '$25', '$30', '$36', '$40', 'C', '20% of $45 = $9, so $45 - $9 = $36', 'medium', 2025, 1),
(1, 'What is the slope of a line passing through points (2, 3) and (6, 11)?', '1', '2', '3', '4', 'B', 'Slope = (y₂ - y₁)/(x₂ - x₁) = (11 - 3)/(6 - 2) = 8/4 = 2', 'medium', 2025, 1),
(1, 'A circle has a radius of 7 cm. What is its circumference? (Use π ≈ 3.14)', '21.98 cm', '43.96 cm', '153.86 cm', '307.72 cm', 'B', 'Circumference = 2πr = 2 × 3.14 × 7 = 43.96 cm', 'medium', 2025, 1),
(1, 'What is the median of the following set: 5, 12, 8, 15, 9?', '8', '9', '10', '12', 'B', 'Arrange in order: 5, 8, 9, 12, 15. The middle value is 9', 'easy', 2025, 1),
(1, 'If y = 2x - 3, what is y when x = 5?', '5', '7', '9', '11', 'B', 'y = 2(5) - 3 = 10 - 3 = 7', 'easy', 2025, 1),
(1, 'What is 3/4 + 2/3?', '5/7', '17/12', '5/12', '1', 'B', 'Find common denominator 12: 9/12 + 8/12 = 17/12', 'medium', 2025, 1),
(1, 'A box contains 5 red balls, 3 blue balls, and 2 green balls. What is the probability of randomly selecting a blue ball?', '1/10', '3/10', '1/3', '1/2', 'B', 'Total balls = 10, blue balls = 3, so probability = 3/10', 'medium', 2024, 1),
(1, 'What is the volume of a cube with side length 4 cm?', '16 cm³', '48 cm³', '64 cm³', '256 cm³', 'C', 'Volume = side³ = 4³ = 64 cm³', 'easy', 2024, 1),
(1, 'Solve for x: 2(x + 3) = 18', 'x = 3', 'x = 6', 'x = 9', 'x = 12', 'B', '2x + 6 = 18, so 2x = 12, therefore x = 6', 'medium', 2024, 1),
(1, 'What is 15% of 200?', '20', '25', '30', '35', 'C', '15% = 0.15, so 0.15 × 200 = 30', 'easy', 2024, 1),
(1, 'The mean of 5 numbers is 12. What is their sum?', '17', '48', '60', '72', 'C', 'Mean = Sum/Count, so Sum = Mean × Count = 12 × 5 = 60', 'medium', 2024, 1),
(1, 'What is the value of √144?', '10', '11', '12', '13', 'C', '12 × 12 = 144, so √144 = 12', 'easy', 2024, 1),
(1, 'If a car travels 240 miles in 4 hours, what is its average speed?', '50 mph', '55 mph', '60 mph', '65 mph', 'C', 'Speed = Distance/Time = 240/4 = 60 mph', 'easy', 2024, 1),
(1, 'What is the perimeter of a square with side length 9 cm?', '18 cm', '27 cm', '36 cm', '81 cm', 'C', 'Perimeter = 4 × side = 4 × 9 = 36 cm', 'easy', 2024, 1),
(1, 'Simplify: 5² - 3²', '4', '8', '16', '32', 'C', '5² = 25 and 3² = 9, so 25 - 9 = 16', 'easy', 2024, 1),
(1, 'What is 0.75 expressed as a fraction in simplest form?', '3/5', '2/3', '3/4', '4/5', 'C', '0.75 = 75/100 = 3/4 when simplified', 'easy', 2024, 1),
(1, 'If the ratio of boys to girls in a class is 3:2 and there are 15 boys, how many girls are there?', '8', '10', '12', '15', 'B', '3:2 = 15:x, so 3x = 30, therefore x = 10', 'medium', 2023, 1),
(1, 'What is the area of a triangle with base 8 cm and height 6 cm?', '14 cm²', '24 cm²', '28 cm²', '48 cm²', 'B', 'Area = (1/2) × base × height = (1/2) × 8 × 6 = 24 cm²', 'easy', 2023, 1),
(1, 'Evaluate: |−7|', '−7', '0', '7', '14', 'C', 'The absolute value of -7 is 7', 'easy', 2023, 1),
(1, 'What is 2/5 of 50?', '10', '15', '20', '25', 'C', '(2/5) × 50 = 100/5 = 20', 'easy', 2023, 1),
(1, 'If x² = 49, what are the possible values of x?', '7 only', '−7 only', '7 and −7', '49 and −49', 'C', 'Both 7² and (−7)² equal 49', 'medium', 2023, 1),
(1, 'What is the next number in the sequence: 2, 6, 12, 20, ...?', '28', '30', '32', '36', 'B', 'Differences are 4, 6, 8, so next is 10, giving 20 + 10 = 30', 'hard', 2023, 1),
(1, 'A store marks up items by 40%. If an item costs the store $50, what is the selling price?', '$60', '$65', '$70', '$75', 'C', '40% of $50 = $20, so selling price = $50 + $20 = $70', 'medium', 2023, 1),
(1, 'What is the greatest common factor (GCF) of 24 and 36?', '4', '6', '8', '12', 'D', 'Factors of 24: 1,2,3,4,6,8,12,24. Factors of 36: 1,2,3,4,6,9,12,18,36. GCF = 12', 'medium', 2023, 1),
(1, 'If 5x - 2 = 3x + 10, what is x?', '4', '5', '6', '7', 'C', '5x - 3x = 10 + 2, so 2x = 12, therefore x = 6', 'medium', 2023, 1),
(1, 'What is the mode of the data set: 4, 7, 4, 9, 4, 5, 7?', '4', '5', '7', '9', 'A', 'The mode is the most frequent value, which is 4 (appears 3 times)', 'easy', 2023, 1);

-- ============================================
-- Insert Language Arts Questions
-- ============================================
INSERT INTO questions (subject_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, difficulty, year, created_by) VALUES
(2, 'Which sentence is grammatically correct?', 'The team are playing well today.', 'The team is playing well today.', 'The team were playing well today.', 'The team be playing well today.', 'B', 'Team is a collective noun treated as singular, so is is correct', 'medium', 2025, 1),
(2, 'Choose the correct word: The weather was _____ cold yesterday.', 'to', 'too', 'two', 'tue', 'B', 'Too means excessively or also', 'easy', 2025, 1),
(2, 'Identify the subject in this sentence: The quick brown fox jumps over the lazy dog.', 'quick', 'fox', 'jumps', 'dog', 'B', 'The subject is fox - the one performing the action', 'easy', 2025, 1),
(2, 'Which word is a synonym for happy?', 'sad', 'joyful', 'angry', 'tired', 'B', 'Joyful means the same as happy', 'easy', 2025, 1),
(2, 'Choose the correct punctuation: I need to buy eggs milk and bread', 'I need to buy eggs milk and bread.', 'I need to buy eggs, milk, and bread.', 'I need to buy eggs; milk; and bread.', 'I need to buy eggs: milk: and bread.', 'B', 'Items in a list should be separated by commas', 'easy', 2025, 1),
(2, 'What is the past tense of run?', 'runned', 'ran', 'runs', 'running', 'B', 'Ran is the correct past tense of run', 'easy', 2025, 1),
(2, 'Which sentence uses the apostrophe correctly?', 'The dogs bone is buried.', 'The dog''s bone is buried.', 'The dogs'' bone is buried.', 'The dogs bone''s is buried.', 'B', 'The apostrophe shows possession: the bone belongs to one dog', 'medium', 2025, 1),
(2, 'Choose the correct word: She did _____ homework last night.', 'her', 'she', 'hers', 'herself', 'A', 'Her is the possessive pronoun needed here', 'easy', 2025, 1),
(2, 'What type of sentence is this: Stop!', 'Declarative', 'Interrogative', 'Imperative', 'Exclamatory', 'C', 'An imperative sentence gives a command', 'medium', 2025, 1),
(2, 'Which word is spelled correctly?', 'recieve', 'receive', 'receve', 'receeve', 'B', 'Receive follows the i before e except after c rule', 'easy', 2025, 1);

-- Continue with more Language Arts, Science, and Social Studies questions...
-- (Truncated for brevity - you can add all 30 questions per subject)

-- ============================================
-- Create Question Sets (Historical)
-- ============================================
INSERT INTO question_sets (name, year, subject_id, description) VALUES
('Math 2023 Question Set', 2023, 1, 'Historical math questions from 2023'),
('Math 2024 Question Set', 2024, 1, 'Historical math questions from 2024'),
('Math 2025 Question Set', 2025, 1, 'Current math questions for 2025'),
('Language Arts 2025 Set', 2025, 2, 'Current language arts questions'),
('Science 2025 Set', 2025, 3, 'Current science questions'),
('Social Studies 2025 Set', 2025, 4, 'Current social studies questions');

-- ============================================
-- System Settings
-- ============================================
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('exam_mode_enabled', 'true', 'boolean', 'Global exam mode toggle'),
('max_violations_allowed', '5', 'number', 'Maximum violations before exam termination'),
('require_fullscreen', 'true', 'boolean', 'Require fullscreen mode for exams'),
('allow_question_flagging', 'true', 'boolean', 'Allow students to flag questions'),
('show_explanations', 'true', 'boolean', 'Show answer explanations after exam'),
('maintenance_mode', 'false', 'boolean', 'System maintenance mode');
