-- Insert terms for ALL existing academic years that don't have terms
-- Run this in phpMyAdmin

-- For each academic year without terms, insert default terms
INSERT INTO terms (academic_session_id, name, code, start_date, end_date, is_current, is_active)
SELECT 
    ay.id,
    'Term 1',
    'T1',
    ay.start_date,
    DATE_ADD(ay.start_date, INTERVAL 4 MONTH),
    0,
    1
FROM academic_years ay
LEFT JOIN terms t ON t.academic_session_id = ay.id AND t.code = 'T1'
WHERE t.id IS NULL;

INSERT INTO terms (academic_session_id, name, code, start_date, end_date, is_current, is_active)
SELECT 
    ay.id,
    'Term 2',
    'T2',
    DATE_ADD(ay.start_date, INTERVAL 4 MONTH),
    DATE_ADD(ay.start_date, INTERVAL 8 MONTH),
    0,
    1
FROM academic_years ay
LEFT JOIN terms t ON t.academic_session_id = ay.id AND t.code = 'T2'
WHERE t.id IS NULL;

INSERT INTO terms (academic_session_id, name, code, start_date, end_date, is_current, is_active)
SELECT 
    ay.id,
    'Term 3',
    'T3',
    DATE_ADD(ay.start_date, INTERVAL 8 MONTH),
    ay.end_date,
    0,
    1
FROM academic_years ay
LEFT JOIN terms t ON t.academic_session_id = ay.id AND t.code = 'T3'
WHERE t.id IS NULL;

-- Verify
SELECT ay.name as year, t.name as term, t.code 
FROM academic_years ay 
LEFT JOIN terms t ON t.academic_session_id = ay.id 
ORDER BY ay.id, t.start_date;
