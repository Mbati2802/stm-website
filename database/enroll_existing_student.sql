-- Enroll existing student for testing marks entry
-- Run this in phpMyAdmin

-- First, get the student ID
SELECT id, name FROM student_accounts LIMIT 1;

-- Replace X with your student ID from above, then run:
INSERT INTO student_enrollments 
(student_id, academic_session_id, term_id, intake_id, programme_id, session_id, enrollment_date, status) 
VALUES 
(X, 1, 1, 1, 1, 1, CURDATE(), 'active');

-- To enroll the same student in all 3 terms:
INSERT INTO student_enrollments 
(student_id, academic_session_id, term_id, intake_id, programme_id, session_id, enrollment_date, status) 
VALUES 
(X, 1, 2, 1, 1, 1, CURDATE(), 'active'),
(X, 1, 3, 1, 1, 1, CURDATE(), 'active');

-- Verify enrollment
SELECT * FROM student_enrollments;
