-- Rollback companion for Portal UI + Staff RBAC + Academic CMS migration
-- WARNING: This is destructive for the added schema objects.
-- Review carefully before running in production.

SET @db := DATABASE();

-- ---------------------------------------------------------------------
-- Drop foreign keys if they exist
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_study_materials_course'
    ),
    'ALTER TABLE study_materials DROP FOREIGN KEY fk_study_materials_course',
    'SELECT "fk_study_materials_course not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_assignments_course'
    ),
    'ALTER TABLE course_assignments DROP FOREIGN KEY fk_course_assignments_course',
    'SELECT "fk_course_assignments_course not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_grades_scheme'
    ),
    'ALTER TABLE course_grades DROP FOREIGN KEY fk_course_grades_scheme',
    'SELECT "fk_course_grades_scheme not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_grades_course'
    ),
    'ALTER TABLE course_grades DROP FOREIGN KEY fk_course_grades_course',
    'SELECT "fk_course_grades_course not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_course_grades_student'
    ),
    'ALTER TABLE course_grades DROP FOREIGN KEY fk_course_grades_student',
    'SELECT "fk_course_grades_student not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_programme_timetables_programme'
    ),
    'ALTER TABLE programme_timetables DROP FOREIGN KEY fk_programme_timetables_programme',
    'SELECT "fk_programme_timetables_programme not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_portal_courses_teacher'
    ),
    'ALTER TABLE portal_courses DROP FOREIGN KEY fk_portal_courses_teacher',
    'SELECT "fk_portal_courses_teacher not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = @db
          AND CONSTRAINT_NAME = 'fk_portal_courses_programme'
    ),
    'ALTER TABLE portal_courses DROP FOREIGN KEY fk_portal_courses_programme',
    'SELECT "fk_portal_courses_programme not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- Drop added tables if they exist
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS study_materials;
DROP TABLE IF EXISTS course_assignments;
DROP TABLE IF EXISTS course_grades;
DROP TABLE IF EXISTS grading_schemes;
DROP TABLE IF EXISTS programme_timetables;
DROP TABLE IF EXISTS portal_courses;

-- ---------------------------------------------------------------------
-- Remove events portal publishing columns
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'events'
          AND COLUMN_NAME = 'portal_announcement_text'
    ),
    'ALTER TABLE events DROP COLUMN portal_announcement_text',
    'SELECT "events.portal_announcement_text not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'events'
          AND COLUMN_NAME = 'publish_to_portal'
    ),
    'ALTER TABLE events DROP COLUMN publish_to_portal',
    'SELECT "events.publish_to_portal not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
-- Remove RBAC columns from users (if present)
-- ---------------------------------------------------------------------
SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'created_by'
    ),
    'ALTER TABLE users DROP COLUMN created_by',
    'SELECT "users.created_by not found" AS info'
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
    'ALTER TABLE users DROP COLUMN status',
    'SELECT "users.status not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    EXISTS(
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = @db
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'role'
    ),
    'ALTER TABLE users DROP COLUMN role',
    'SELECT "users.role not found" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
