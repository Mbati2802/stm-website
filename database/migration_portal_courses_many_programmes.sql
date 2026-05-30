-- Migration: Support many-to-many relationship between portal_courses and programmes
-- One unit can belong to multiple programmes

-- Create junction table for many-to-many relationship
CREATE TABLE IF NOT EXISTS portal_course_programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portal_course_id INT NOT NULL,
    programme_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_course_programme (portal_course_id, programme_id),
    CONSTRAINT fk_pcp_course FOREIGN KEY (portal_course_id) 
        REFERENCES portal_courses(id) ON DELETE CASCADE,
    CONSTRAINT fk_pcp_programme FOREIGN KEY (programme_id) 
        REFERENCES programmes(id) ON DELETE CASCADE,
    INDEX idx_pcp_course (portal_course_id),
    INDEX idx_pcp_programme (programme_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing data: Copy programme_id from portal_courses to junction table
-- This preserves existing relationships
INSERT IGNORE INTO portal_course_programmes (portal_course_id, programme_id)
SELECT id, programme_id FROM portal_courses WHERE programme_id IS NOT NULL;

-- Note: After migration, the old programme_id column in portal_courses can be:
-- 1. Kept for backward compatibility (nullable)
-- 2. Dropped later after full migration
-- For now, we keep it nullable but don't use it for new records

-- Make programme_id nullable to allow units without a primary programme
-- ALTER TABLE portal_courses MODIFY programme_id INT NULL;
