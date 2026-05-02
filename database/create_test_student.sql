-- Create test student for dashboard verification
-- Admission number: CBE/0008/MAY/2026
-- Password: CBE/0008/MAY/2026 (hashed)

-- First check if student already exists
SELECT * FROM student_accounts WHERE admission_number = 'CBE/0008/MAY/2026';

-- If student doesn't exist, insert them
INSERT INTO student_accounts (
    name,
    email,
    admission_number,
    password,
    programme_id,
    national_id,
    gender,
    date_of_birth,
    phone,
    county,
    sub_county,
    guardian_name,
    guardian_relationship,
    guardian_phone,
    guardian_email,
    previous_school,
    kcse_year,
    kcse_grade,
    kcse_index,
    preferred_intake,
    disability_status,
    referral_source,
    additional_notes,
    created_at
) VALUES (
    'Test Student',
    'cbe0008may2026@stmarysmchmcollege.ac.ke',
    'CBE/0008/MAY/2026',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- This is 'password' - should be changed to actual password hash
    5, -- Diploma in Perioperative Theater Technology
    '12345678',
    'Male',
    '2000-01-01',
    '+254700000000',
    'Nairobi',
    'Westlands',
    'John Doe',
    'Parent',
    '+254711111111',
    'parent@example.com',
    'High School',
    2020,
    'A',
    '123456/789',
    'September',
    'None',
    'Online',
    'Test student for dashboard verification',
    NOW()
) ON DUPLICATE KEY UPDATE admission_number = admission_number;

-- Enroll the student in current academic session
-- Get the current academic session and term
SELECT id, name FROM academic_sessions ORDER BY id DESC LIMIT 1;
SELECT id, name FROM terms ORDER BY id DESC LIMIT 1;
SELECT id, name FROM intakes ORDER BY id DESC LIMIT 1;

-- Enroll student (replace IDs with actual values from queries above)
INSERT INTO student_enrollments (
    student_id,
    academic_session_id,
    term_id,
    intake_id,
    programme_id,
    session_id,
    enrollment_date,
    status
)
VALUES (
    (SELECT id FROM student_accounts WHERE admission_number = 'CBE/0008/MAY/2026'),
    1, -- Replace with actual academic_session_id
    1, -- Replace with actual term_id
    1, -- Replace with actual intake_id
    5, -- Diploma in Perioperative Theater Technology
    1, -- Session 1
    CURDATE(),
    'active'
) ON DUPLICATE KEY UPDATE status = 'active';
