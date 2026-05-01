-- Create exam_types table for managing different exam types
CREATE TABLE IF NOT EXISTS `exam_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `type` ENUM('single', 'consolidated') NOT NULL DEFAULT 'single',
  `parent_exam_ids` TEXT NULL COMMENT 'JSON array of exam type IDs to sum for consolidated exams',
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_code` (`code`),
  INDEX `idx_type` (`type`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default exam types
INSERT INTO `exam_types` (`name`, `code`, `type`, `description`) VALUES
('Ordinary Exam', 'ORDINARY', 'single', 'Regular term examinations'),
('Supplementary Exam', 'SUPPLEMENTARY', 'single', 'Make-up examinations'),
('Final Exam', 'FINAL', 'single', 'End of term/semester examinations'),
('TOTALS', 'TOTALS', 'consolidated', 'Sum of all exam marks');
