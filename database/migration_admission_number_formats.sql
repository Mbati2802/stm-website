-- Migration for admission number formats table
CREATE TABLE IF NOT EXISTS `admission_number_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of the format (e.g., Standard Format 2026)',
  `format_pattern` varchar(255) NOT NULL COMMENT 'Pattern with placeholders: {PROG_ABBR}, {YYYY}, {YY}, {MM}, {SEQ4}, {SEQ3}, {SEQ2}',
  `is_default` tinyint(1) DEFAULT 0 COMMENT '1 if this is the default format',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default format
INSERT INTO `admission_number_formats` (`name`, `format_pattern`, `is_default`) VALUES
('Standard Format', 'STM/{YYYY}/{SEQ4}', 1);
