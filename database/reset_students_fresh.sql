-- ============================================================
-- FULL STUDENT RESET — clears all student data and resets
-- the auto-increment counter so the first new student gets ID 1
-- and admission numbers will start from 0001.
--
-- WARNING: THIS IS IRREVERSIBLE. TAKE A DATABASE BACKUP FIRST.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Clear billing / accounts data
DELETE FROM fee_items;
DELETE FROM payments;
DELETE FROM invoices;

-- Clear academic data linked to students
DELETE FROM course_grades;
DELETE FROM student_enrollments;

-- Clear auth tokens
DELETE FROM student_password_resets;

-- Clear main student table
DELETE FROM student_accounts;

-- Reset all AUTO_INCREMENT counters to 1
ALTER TABLE fee_items AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;
ALTER TABLE invoices AUTO_INCREMENT = 1;
ALTER TABLE course_grades AUTO_INCREMENT = 1;
ALTER TABLE student_enrollments AUTO_INCREMENT = 1;
ALTER TABLE student_password_resets AUTO_INCREMENT = 1;
ALTER TABLE student_accounts AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- Confirm reset
SELECT 'Student reset complete. Next student will be ID 1.' AS status;
