-- Direct SQL to insert terms for your academic year
-- Run this in phpMyAdmin to populate terms

-- First, check your academic year ID
SELECT id, name, code FROM academic_years;

-- Then replace the X below with your actual academic year ID and run:
INSERT INTO terms (academic_session_id, name, code, start_date, end_date, is_current, is_active) VALUES 
(X, 'Term 1', 'T1', '2024-01-01', '2024-04-30', 0, 1),
(X, 'Term 2', 'T2', '2024-05-01', '2024-08-31', 0, 1),
(X, 'Term 3', 'T3', '2024-09-01', '2024-12-31', 0, 1);
