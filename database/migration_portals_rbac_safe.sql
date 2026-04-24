-- Safe incremental migration for Portal UI + Staff RBAC + Academic CMS modules
-- MySQL/MariaDB-compatible (uses INFORMATION_SCHEMA checks + dynamic SQL)
-- Non-destructive: only creates missing tables/columns/indexes/constraints.

SET @db := DATABASE();

-- ---------------------------------------------------------------------
-- users table role/status support
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'role'
    ),
    'SELECT "users.role exists" AS info',
    'ALTER TABLE users ADD COLUMN role VARCHAR(30) NOT NULL DEFAULT ''super_admin'' AFTER password'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'status'
    ),
    'SELECT "users.status exists" AS info',
    'ALTER TABLE users ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT ''active'' AFTER role'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'created_by'
    ),
    'SELECT "users.created_by exists" AS info',
    'ALTER TABLE users ADD COLUMN created_by INT NULL AFTER status'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE users
SET role = 'super_admin'
WHERE role IS NULL OR role = '';

UPDATE users
SET status = 'active'
WHERE status IS NULL OR status = '';

-- ---------------------------------------------------------------------
-- portal_courses
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'portal_courses'
    ),
    'SELECT "portal_courses exists" AS info',
    'CREATE TABLE portal_courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        programme_id INT NULL,
        teacher_id INT NULL,
        code VARCHAR(80) NULL,
        title VARCHAR(190) NOT NULL,
        description TEXT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'portal_courses'
          AND INDEX_NAME = 'idx_portal_courses_programme'
    ),
    'SELECT "idx_portal_courses_programme exists" AS info',
    'CREATE INDEX idx_portal_courses_programme ON portal_courses(programme_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'portal_courses'
          AND INDEX_NAME = 'idx_portal_courses_teacher'
    ),
    'SELECT "idx_portal_courses_teacher exists" AS info',
    'CREATE INDEX idx_portal_courses_teacher ON portal_courses(teacher_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- programme_timetables
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'programme_timetables'
    ),
    'SELECT "programme_timetables exists" AS info',
    'CREATE TABLE programme_timetables (
        id INT AUTO_INCREMENT PRIMARY KEY,
        programme_id INT NULL,
        title VARCHAR(190) NOT NULL,
        details TEXT NULL,
        file_path VARCHAR(255) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'programme_timetables'
          AND INDEX_NAME = 'idx_programme_timetables_programme'
    ),
    'SELECT "idx_programme_timetables_programme exists" AS info',
    'CREATE INDEX idx_programme_timetables_programme ON programme_timetables(programme_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- course_grades
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_grades'
    ),
    'SELECT "course_grades exists" AS info',
    'CREATE TABLE course_grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NULL,
        course_id INT NULL,
        grade VARCHAR(20) NOT NULL,
        remarks VARCHAR(255) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_grades'
          AND COLUMN_NAME = 'marks'
    ),
    'SELECT "course_grades.marks exists" AS info',
    'ALTER TABLE course_grades ADD COLUMN marks DECIMAL(5,2) NULL AFTER grade'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_grades'
          AND COLUMN_NAME = 'grading_scheme_id'
    ),
    'SELECT "course_grades.grading_scheme_id exists" AS info',
    'ALTER TABLE course_grades ADD COLUMN grading_scheme_id INT NULL AFTER marks'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_grades'
          AND INDEX_NAME = 'idx_course_grades_student'
    ),
    'SELECT "idx_course_grades_student exists" AS info',
    'CREATE INDEX idx_course_grades_student ON course_grades(student_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_grades'
          AND INDEX_NAME = 'idx_course_grades_course'
    ),
    'SELECT "idx_course_grades_course exists" AS info',
    'CREATE INDEX idx_course_grades_course ON course_grades(course_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_grades'
          AND INDEX_NAME = 'idx_course_grades_scheme'
    ),
    'SELECT "idx_course_grades_scheme exists" AS info',
    'CREATE INDEX idx_course_grades_scheme ON course_grades(grading_scheme_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- grading_schemes
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'grading_schemes'
    ),
    'SELECT "grading_schemes exists" AS info',
    'CREATE TABLE grading_schemes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        grade_label VARCHAR(20) NOT NULL,
        min_score DECIMAL(5,2) NOT NULL DEFAULT 0,
        max_score DECIMAL(5,2) NOT NULL DEFAULT 100,
        remarks VARCHAR(190) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- course_assignments
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_assignments'
    ),
    'SELECT "course_assignments exists" AS info',
    'CREATE TABLE course_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NULL,
        title VARCHAR(190) NOT NULL,
        instructions TEXT NULL,
        due_at DATETIME NULL,
        file_path VARCHAR(255) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- events portal publishing
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'events'
          AND COLUMN_NAME = 'publish_to_portal'
    ),
    'SELECT "events.publish_to_portal exists" AS info',
    'ALTER TABLE events ADD COLUMN publish_to_portal TINYINT(1) NOT NULL DEFAULT 0 AFTER is_featured'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'events'
          AND COLUMN_NAME = 'portal_announcement_text'
    ),
    'SELECT "events.portal_announcement_text exists" AS info',
    'ALTER TABLE events ADD COLUMN portal_announcement_text TEXT NULL AFTER publish_to_portal'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'course_assignments'
          AND INDEX_NAME = 'idx_course_assignments_course'
    ),
    'SELECT "idx_course_assignments_course exists" AS info',
    'CREATE INDEX idx_course_assignments_course ON course_assignments(course_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- study_materials
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'study_materials'
    ),
    'SELECT "study_materials exists" AS info',
    'CREATE TABLE study_materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NULL,
        title VARCHAR(190) NOT NULL,
        summary TEXT NULL,
        file_path VARCHAR(255) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'study_materials'
          AND INDEX_NAME = 'idx_study_materials_course'
    ),
    'SELECT "idx_study_materials_course exists" AS info',
    'CREATE INDEX idx_study_materials_course ON study_materials(course_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- Foreign keys (added only if missing)
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_portal_courses_programme'
    ),
    'SELECT "fk_portal_courses_programme exists" AS info',
    'ALTER TABLE portal_courses
        ADD CONSTRAINT fk_portal_courses_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_portal_courses_teacher'
    ),
    'SELECT "fk_portal_courses_teacher exists" AS info',
    'ALTER TABLE portal_courses
        ADD CONSTRAINT fk_portal_courses_teacher
        FOREIGN KEY (teacher_id) REFERENCES users(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_programme_timetables_programme'
    ),
    'SELECT "fk_programme_timetables_programme exists" AS info',
    'ALTER TABLE programme_timetables
        ADD CONSTRAINT fk_programme_timetables_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_grades_student'
    ),
    'SELECT "fk_course_grades_student exists" AS info',
    'ALTER TABLE course_grades
        ADD CONSTRAINT fk_course_grades_student
        FOREIGN KEY (student_id) REFERENCES student_accounts(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_grades_course'
    ),
    'SELECT "fk_course_grades_course exists" AS info',
    'ALTER TABLE course_grades
        ADD CONSTRAINT fk_course_grades_course
        FOREIGN KEY (course_id) REFERENCES portal_courses(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_grades_scheme'
    ),
    'SELECT "fk_course_grades_scheme exists" AS info',
    'ALTER TABLE course_grades
        ADD CONSTRAINT fk_course_grades_scheme
        FOREIGN KEY (grading_scheme_id) REFERENCES grading_schemes(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_assignments_course'
    ),
    'SELECT "fk_course_assignments_course exists" AS info',
    'ALTER TABLE course_assignments
        ADD CONSTRAINT fk_course_assignments_course
        FOREIGN KEY (course_id) REFERENCES portal_courses(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_study_materials_course'
    ),
    'SELECT "fk_study_materials_course exists" AS info',
    'ALTER TABLE study_materials
        ADD CONSTRAINT fk_study_materials_course
        FOREIGN KEY (course_id) REFERENCES portal_courses(id)
        ON DELETE SET NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- messages read tracking
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'messages'
          AND COLUMN_NAME = 'read_at'
    ),
    'SELECT "messages.read_at exists" AS info',
    'ALTER TABLE messages ADD COLUMN read_at DATETIME NULL AFTER message'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'messages'
          AND INDEX_NAME = 'idx_messages_read_at'
    ),
    'SELECT "idx_messages_read_at exists" AS info',
    'CREATE INDEX idx_messages_read_at ON messages(read_at)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- email_logs
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'email_logs'
    ),
    'SELECT "email_logs exists" AS info',
    'CREATE TABLE email_logs (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        status VARCHAR(60) NOT NULL,
        recipient_email VARCHAR(190) NULL,
        subject VARCHAR(255) NULL,
        error_message VARCHAR(1000) NULL,
        context_json LONGTEXT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'email_logs'
          AND INDEX_NAME = 'idx_email_logs_created'
    ),
    'SELECT "idx_email_logs_created exists" AS info',
    'CREATE INDEX idx_email_logs_created ON email_logs(created_at)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'email_logs'
          AND INDEX_NAME = 'idx_email_logs_status'
    ),
    'SELECT "idx_email_logs_status exists" AS info',
    'CREATE INDEX idx_email_logs_status ON email_logs(status)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- admin_messages
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'admin_messages'
    ),
    'SELECT "admin_messages exists" AS info',
    'CREATE TABLE admin_messages (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        recipient_id INT NOT NULL,
        subject VARCHAR(190) NOT NULL,
        body TEXT NOT NULL,
        read_at DATETIME NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'admin_messages'
          AND INDEX_NAME = 'idx_admin_messages_recipient'
    ),
    'SELECT "idx_admin_messages_recipient exists" AS info',
    'CREATE INDEX idx_admin_messages_recipient ON admin_messages(recipient_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'admin_messages'
          AND INDEX_NAME = 'idx_admin_messages_sender'
    ),
    'SELECT "idx_admin_messages_sender exists" AS info',
    'CREATE INDEX idx_admin_messages_sender ON admin_messages(sender_id)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'admin_messages'
          AND INDEX_NAME = 'idx_admin_messages_read'
    ),
    'SELECT "idx_admin_messages_read exists" AS info',
    'CREATE INDEX idx_admin_messages_read ON admin_messages(read_at)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_admin_messages_sender'
    ),
    'SELECT "fk_admin_messages_sender exists" AS info',
    'ALTER TABLE admin_messages
        ADD CONSTRAINT fk_admin_messages_sender
        FOREIGN KEY (sender_id) REFERENCES users(id)
        ON DELETE CASCADE'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_admin_messages_recipient'
    ),
    'SELECT "fk_admin_messages_recipient exists" AS info',
    'ALTER TABLE admin_messages
        ADD CONSTRAINT fk_admin_messages_recipient
        FOREIGN KEY (recipient_id) REFERENCES users(id)
        ON DELETE CASCADE'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- programme_applications
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'programme_applications'
    ),
    'SELECT "programme_applications exists" AS info',
    'CREATE TABLE programme_applications (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(190) NOT NULL,
        email VARCHAR(190) NOT NULL,
        phone VARCHAR(60) NOT NULL,
        guardian_name VARCHAR(190) NULL,
        guardian_phone VARCHAR(60) NULL,
        county VARCHAR(120) NULL,
        course_selection VARCHAR(190) NOT NULL,
        grade VARCHAR(80) NULL,
        level VARCHAR(80) NULL,
        preferred_intake VARCHAR(80) NULL,
        referral_source VARCHAR(190) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'programme_applications'
          AND INDEX_NAME = 'idx_programme_applications_created'
    ),
    'SELECT "idx_programme_applications_created exists" AS info',
    'CREATE INDEX idx_programme_applications_created ON programme_applications(created_at)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'programme_applications'
          AND INDEX_NAME = 'idx_programme_applications_course'
    ),
    'SELECT "idx_programme_applications_course exists" AS info',
    'CREATE INDEX idx_programme_applications_course ON programme_applications(course_selection)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- page_visits
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'page_visits'
    ),
    'SELECT "page_visits exists" AS info',
    'CREATE TABLE page_visits (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        path VARCHAR(255) NOT NULL,
        user_role VARCHAR(40) NULL,
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        session_id VARCHAR(128) NULL,
        ip_address VARCHAR(64) NULL,
        user_agent VARCHAR(255) NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'page_visits'
          AND INDEX_NAME = 'idx_page_visits_created'
    ),
    'SELECT "idx_page_visits_created exists" AS info',
    'CREATE INDEX idx_page_visits_created ON page_visits(created_at)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'page_visits'
          AND INDEX_NAME = 'idx_page_visits_path'
    ),
    'SELECT "idx_page_visits_path exists" AS info',
    'CREATE INDEX idx_page_visits_path ON page_visits(path)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'page_visits'
          AND INDEX_NAME = 'idx_page_visits_admin'
    ),
    'SELECT "idx_page_visits_admin exists" AS info',
    'CREATE INDEX idx_page_visits_admin ON page_visits(is_admin)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- Optional seed: ensure at least one super admin exists
-- ---------------------------------------------------------------------
INSERT INTO users(name, email, password, role, status, created_at)
SELECT 'System Admin', 'admin@stm.ac.ke', '$2y$10$A0QHJ7MFHAnYpLEB.E7Tc.63GiGi7wZe5YCG8VvI7YsV7e59ri.BS', 'super_admin', 'active', NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@stm.ac.ke'
);
