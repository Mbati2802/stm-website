-- Migration for existing databases with academic_sessions table
-- This script handles all possible states of the database gracefully

-- Step 1: Handle academic_years table
-- Drop academic_years if it exists (in case of previous failed migration)
DROP TABLE IF EXISTS `academic_years`;

-- Rename academic_sessions to academic_years only if academic_sessions still exists
-- If academic_sessions doesn't exist, the table might already be renamed or data is in academic_years
SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'academic_sessions';

SET @sql = IF(@table_exists > 0, 'RENAME TABLE academic_sessions TO academic_years', 'SELECT ''Table already renamed or does not exist''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Handle sessions table
-- Create sessions table only if it doesn't exist
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `sequence_number` INT NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_code` (`code`),
  INDEX `idx_sequence_number` (`sequence_number`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 3: Handle session_id column in student_enrollments
-- Check if column exists before adding
SET @column_exists = 0;
SELECT COUNT(*) INTO @column_exists FROM information_schema.columns 
WHERE table_schema = DATABASE() AND table_name = 'student_enrollments' AND column_name = 'session_id';

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE student_enrollments ADD COLUMN session_id INT NULL AFTER academic_session_id',
    'SELECT ''Column already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index if it doesn't exist
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists FROM information_schema.statistics 
WHERE table_schema = DATABASE() AND table_name = 'student_enrollments' AND index_name = 'idx_session_id';

SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE student_enrollments ADD INDEX idx_session_id (session_id)',
    'SELECT ''Index already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key if it doesn't exist
-- First drop any existing foreign key on session_id
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists FROM information_schema.table_constraints 
WHERE table_schema = DATABASE() AND table_name = 'student_enrollments' AND constraint_name LIKE '%session_id%';

SET @sql = IF(@fk_exists > 0, 
    'ALTER TABLE student_enrollments DROP FOREIGN KEY student_enrollments_ibfk_session',
    'SELECT ''No foreign key to drop''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add the foreign key
SET @sql = 'ALTER TABLE student_enrollments ADD FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE SET NULL';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Update foreign key references in terms table
-- Drop old foreign key if exists
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists FROM information_schema.table_constraints 
WHERE table_schema = DATABASE() AND table_name = 'terms' AND constraint_name = 'terms_ibfk_1';

SET @sql = IF(@fk_exists > 0, 
    'ALTER TABLE terms DROP FOREIGN KEY terms_ibfk_1',
    'SELECT ''No foreign key to drop''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add new foreign key if it doesn't exist
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists FROM information_schema.table_constraints 
WHERE table_schema = DATABASE() AND table_name = 'terms' AND constraint_name = 'fk_terms_academic_year';

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE terms ADD CONSTRAINT fk_terms_academic_year FOREIGN KEY (academic_session_id) REFERENCES academic_years(id) ON DELETE CASCADE',
    'SELECT ''Foreign key already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Update foreign key references in student_enrollments table
-- Drop old foreign key if exists
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists FROM information_schema.table_constraints 
WHERE table_schema = DATABASE() AND table_name = 'student_enrollments' AND constraint_name = 'student_enrollments_ibfk_2';

SET @sql = IF(@fk_exists > 0, 
    'ALTER TABLE student_enrollments DROP FOREIGN KEY student_enrollments_ibfk_2',
    'SELECT ''No foreign key to drop''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add new foreign key if it doesn't exist
SET @fk_exists = 0;
SELECT COUNT(*) INTO @fk_exists FROM information_schema.table_constraints 
WHERE table_schema = DATABASE() AND table_name = 'student_enrollments' AND constraint_name = 'fk_student_enrollments_academic_year';

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE student_enrollments ADD CONSTRAINT fk_student_enrollments_academic_year FOREIGN KEY (academic_session_id) REFERENCES academic_years(id) ON DELETE CASCADE',
    'SELECT ''Foreign key already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6: Insert default sessions (skip if already exist)
INSERT IGNORE INTO `sessions` (`name`, `code`, `sequence_number`, `is_active`) VALUES
('Session 1', 'S1', 1, 1),
('Session 2', 'S2', 2, 1),
('Session 3', 'S3', 3, 1),
('Session 4', 'S4', 4, 1),
('Session 5', 'S5', 5, 1),
('Session 6', 'S6', 6, 1);
