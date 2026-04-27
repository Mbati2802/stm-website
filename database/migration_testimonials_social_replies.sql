-- ============================================================
-- Migration: Testimonials table, Social Updates table,
--            Message reply tracking columns
-- ============================================================

-- 1. Add reply tracking to messages table
-- IF NOT EXISTS is supported by MySQL 8.0+ and MariaDB 10.x+.
-- Safe to re-run: existing columns and indexes are silently skipped.

ALTER TABLE messages ADD COLUMN IF NOT EXISTS read_at DATETIME NULL;
ALTER TABLE messages ADD COLUMN IF NOT EXISTS replied_at DATETIME NULL;
ALTER TABLE messages ADD COLUMN IF NOT EXISTS reply_subject VARCHAR(190) NULL;
ALTER TABLE messages ADD COLUMN IF NOT EXISTS reply_body TEXT NULL;
ALTER TABLE messages ADD COLUMN IF NOT EXISTS replied_by INT NULL;

CREATE INDEX IF NOT EXISTS idx_messages_read_at ON messages (read_at);
CREATE INDEX IF NOT EXISTS idx_messages_replied_at ON messages (replied_at);

-- 2. Testimonials table (replaces JSON-based home_testimonials_json)
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    course VARCHAR(200) NOT NULL DEFAULT '',
    message TEXT NOT NULL,
    image_path VARCHAR(500) NULL,
    is_visible TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_testimonials_visible (is_visible),
    INDEX idx_testimonials_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Social Updates table (replaces third-party embed)
CREATE TABLE IF NOT EXISTS social_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    image_path VARCHAR(500) NULL,
    link_url VARCHAR(500) NULL,
    source VARCHAR(50) DEFAULT 'general',
    is_pinned TINYINT(1) DEFAULT 0,
    is_visible TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_social_visible (is_visible),
    INDEX idx_social_pinned (is_pinned)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Seed default testimonials (matching current JSON defaults)
INSERT INTO testimonials (name, course, message, image_path, sort_order) VALUES
('Brenda W.', 'Prospective Student', 'The admissions team was responsive and helped me choose the right programme path.', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300', 1),
('Daniel K.', 'Current Student', 'Course delivery is practical and the learning environment is supportive and well organized.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300', 2),
('Sharon M.', 'Parent', 'Clear communication and professional training standards gave us confidence in the college.', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300', 3),
('Ian K.', 'Applicant', 'Programme information is clear and the support team gives timely guidance on requirements.', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=300', 4),
('Purity N.', 'Current Student', 'The timetable is practical and helps me balance learning with my other responsibilities.', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=300', 5),
('Grace A.', 'Guardian', 'The college environment is disciplined, safe, and focused on quality healthcare training.', 'https://images.unsplash.com/photo-1554151228-14d9def656e4?w=300', 6);
