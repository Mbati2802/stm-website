-- Update exam_types table to include specific exam types
-- Run this in phpMyAdmin

-- First, clear existing exam types
DELETE FROM exam_types;

-- Insert specific exam types
INSERT INTO exam_types (name, code, type, description, is_active) VALUES
('Assignment', 'ASSIGNMENT', 'single', 'Assignment marks', 1),
('CAT', 'CAT', 'single', 'Continuous Assessment Test', 1),
('End Term', 'END_TERM', 'single', 'End of term examination', 1),
('Practical', 'PRACTICAL', 'single', 'Practical examination', 1),
('TOTALS', 'TOTALS', 'consolidated', 'Sum of all exam marks', 1);

-- Verify the exam types
SELECT * FROM exam_types;
