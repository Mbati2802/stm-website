-- Create deleted_records table for backup system
CREATE TABLE IF NOT EXISTS `deleted_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `table_name` VARCHAR(100) NOT NULL,
  `record_id` INT NOT NULL,
  `record_data` TEXT NOT NULL,
  `deleted_by` INT NULL,
  `deleted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_table_name` (`table_name`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
