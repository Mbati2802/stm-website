-- Migration for existing databases with academic_sessions table
-- This script renames academic_sessions to academic_years and creates sessions table

-- Rename academic_sessions to academic_years
RENAME TABLE `academic_sessions` TO `academic_years`;

-- Update foreign key references in terms table
ALTER TABLE `terms` 
DROP FOREIGN KEY `terms_ibfk_1`,
ADD CONSTRAINT `fk_terms_academic_year` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE;

-- Update foreign key references in student_enrollments table
ALTER TABLE `student_enrollments` 
DROP FOREIGN KEY `student_enrollments_ibfk_2`,
ADD CONSTRAINT `fk_student_enrollments_academic_year` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE;

-- Create sessions table (sequential, no dates, tied to student progress)
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

-- Add session_id to student_enrollments if not exists
ALTER TABLE `student_enrollments` 
ADD COLUMN `session_id` INT NULL AFTER `academic_session_id`,
ADD INDEX `idx_session_id` (`session_id`),
ADD FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE SET NULL;

-- Insert default sessions (skip if already exist)
INSERT IGNORE INTO `sessions` (`name`, `code`, `sequence_number`, `is_active`) VALUES
('Session 1', 'S1', 1, 1),
('Session 2', 'S2', 2, 1),
('Session 3', 'S3', 3, 1),
('Session 4', 'S4', 4, 1),
('Session 5', 'S5', 5, 1),
('Session 6', 'S6', 6, 1);
