-- Create academic_years table for managing academic years
CREATE TABLE IF NOT EXISTS `academic_years` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_code` (`code`),
  INDEX `idx_is_current` (`is_current`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create terms table for managing terms within academic years
CREATE TABLE IF NOT EXISTS `terms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `academic_session_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `code` VARCHAR(20) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_session_code` (`academic_session_id`, `code`),
  INDEX `idx_academic_session_id` (`academic_session_id`),
  INDEX `idx_is_current` (`is_current`),
  INDEX `idx_is_active` (`is_active`),
  FOREIGN KEY (`academic_session_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create intakes table for managing admission intakes
CREATE TABLE IF NOT EXISTS `intakes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_code` (`code`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student_enrollments table to link students to years/terms/intakes
CREATE TABLE IF NOT EXISTS `student_enrollments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `academic_session_id` INT NOT NULL,
  `term_id` INT NOT NULL,
  `intake_id` INT NOT NULL,
  `programme_id` INT NULL,
  `enrollment_date` DATE NOT NULL,
  `status` ENUM('active', 'suspended', 'graduated', 'withdrawn') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_student_id` (`student_id`),
  INDEX `idx_academic_session_id` (`academic_session_id`),
  INDEX `idx_term_id` (`term_id`),
  INDEX `idx_intake_id` (`intake_id`),
  INDEX `idx_programme_id` (`programme_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`academic_session_id`) REFERENCES `academic_years`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`term_id`) REFERENCES `terms`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`intake_id`) REFERENCES `intakes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create sessions table for student progress tracking (no dates, sequential)
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

-- Add session_id to student_enrollments
ALTER TABLE `student_enrollments` 
ADD COLUMN `session_id` INT NULL AFTER `academic_session_id`,
ADD INDEX `idx_session_id` (`session_id`),
ADD FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE SET NULL;

-- Insert default data
INSERT INTO `academic_years` (`name`, `code`, `start_date`, `end_date`, `is_current`, `is_active`) VALUES
('2024-2025', '2024-2025', '2024-01-01', '2025-12-31', 1, 1);

INSERT INTO `terms` (`academic_session_id`, `name`, `code`, `start_date`, `end_date`, `is_current`, `is_active`) VALUES
(1, 'Term 1', 'T1', '2024-01-01', '2024-04-30', 0, 1),
(1, 'Term 2', 'T2', '2024-05-01', '2024-08-31', 0, 1),
(1, 'Term 3', 'T3', '2024-09-01', '2024-12-31', 0, 1);

INSERT INTO `intakes` (`name`, `code`, `start_date`, `end_date`, `is_active`) VALUES
('January Intake', 'JAN', '2024-01-01', NULL, 1),
('May Intake', 'MAY', '2024-05-01', NULL, 1),
('September Intake', 'SEP', '2024-09-01', NULL, 1);

INSERT INTO `sessions` (`name`, `code`, `sequence_number`, `is_active`) VALUES
('Session 1', 'S1', 1, 1),
('Session 2', 'S2', 2, 1),
('Session 3', 'S3', 3, 1),
('Session 4', 'S4', 4, 1),
('Session 5', 'S5', 5, 1),
('Session 6', 'S6', 6, 1);
