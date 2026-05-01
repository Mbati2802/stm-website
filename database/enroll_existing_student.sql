-- Enroll existing student for testing marks entry
-- Run this in phpMyAdmin

-- First, get the student ID
SELECT id, name FROM student_accounts LIMIT 1;

-- Replace X with your student ID from above
-- Use the same IDs you selected in the filters:
-- programme_id=5, session_id=1, term_id=2, student_session_id=2
INSERT INTO student_enrollments 
(student_id, academic_session_id, term_id, intake_id, programme_id, session_id, enrollment_date, status) 
VALUES 
(X, 1, 2, 1, 5, 2, CURDATE(), 'active');

-- Verify enrollment
SELECT * FROM student_enrollments;
