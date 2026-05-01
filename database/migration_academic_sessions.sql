-- Create academic_sessions table for managing academic years
CREATE TABLE IF NOT EXISTS `academic_sessions` (
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

-- Create terms table for managing terms within academic sessions
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
  FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions`(`id`) ON DELETE CASCADE
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

-- Create student_enrollments table to link students to sessions/terms/intakes
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
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`term_id`) REFERENCES `terms`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`intake_id`) REFERENCES `intakes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`programme_id`) REFERENCES `programmes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default data
INSERT INTO `academic_sessions` (`name`, `code`, `start_date`, `end_date`, `is_current`, `is_active`) VALUES
('2024-2025', '2024-2025', '2024-01-01', '2025-12-31', 1, 1);

INSERT INTO `terms` (`academic_session_id`, `name`, `code`, `start_date`, `end_date`, `is_current`, `is_active`) VALUES
(1, 'Term 1', 'T1', '2024-01-01', '2024-04-30', 0, 1),
(1, 'Term 2', 'T2', '2024-05-01', '2024-08-31', 0, 1),
(1, 'Term 3', 'T3', '2024-09-01', '2024-12-31', 0, 1);

INSERT INTO `intakes` (`name`, `code`, `start_date`, `end_date`, `is_active`) VALUES
('January Intake', 'JAN', '2024-01-01', NULL, 1),
('May Intake', 'MAY', '2024-05-01', NULL, 1),
('September Intake', 'SEP', '2024-09-01', NULL, 1);
