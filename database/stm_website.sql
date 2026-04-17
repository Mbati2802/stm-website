
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_departments_slug (slug)
);

CREATE TABLE programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NULL,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(210) NOT NULL UNIQUE,
    category VARCHAR(60) NOT NULL,
    terms INT NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_programmes_category (category),
    INDEX idx_programmes_name (name),
    CONSTRAINT fk_programmes_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(210) NOT NULL UNIQUE,
    summary TEXT NULL,
    body LONGTEXT NULL,
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_news_slug (slug),
    INDEX idx_news_title (title)
);

CREATE TABLE careers LIKE news;
ALTER TABLE careers DROP INDEX idx_news_slug, DROP INDEX idx_news_title;
ALTER TABLE careers ADD INDEX idx_careers_slug (slug), ADD INDEX idx_careers_title (title);

CREATE TABLE tenders LIKE news;
ALTER TABLE tenders DROP INDEX idx_news_slug, DROP INDEX idx_news_title;
ALTER TABLE tenders ADD INDEX idx_tenders_slug (slug), ADD INDEX idx_tenders_title (title);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(210) NOT NULL UNIQUE,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NULL,
    category VARCHAR(80) NULL,
    time_label VARCHAR(120) NULL,
    location VARCHAR(190) NULL,
    venue_type VARCHAR(40) NULL,
    registration_status VARCHAR(40) NULL,
    registration_url VARCHAR(255) NULL,
    capacity INT NULL,
    summary TEXT NULL,
    body LONGTEXT NULL,
    image_path VARCHAR(255) NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_events_slug (slug),
    INDEX idx_events_starts_at (starts_at)
);

CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    category VARCHAR(80) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_gallery_category (category)
);

CREATE TABLE library_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    summary TEXT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_library_title (title)
);

CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NULL,
    subject VARCHAR(190) NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_messages_email (email)
);

CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    content LONGTEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(140) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    is_student TINYINT(1) NOT NULL DEFAULT 1,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_regs_event (event_id),
    INDEX idx_event_regs_email (email),
    CONSTRAINT fk_event_regs_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE media_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    category VARCHAR(80) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_media_category (category)
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NULL
);

CREATE TABLE student_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    admission_number VARCHAR(80) NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE student_password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    reset_code VARCHAR(12) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_reset_student (student_id),
    INDEX idx_student_reset_code (reset_code),
    CONSTRAINT fk_student_reset_student FOREIGN KEY (student_id) REFERENCES student_accounts(id) ON DELETE CASCADE
);

CREATE TABLE student_announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE student_timetables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    file_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users(name, email, password) VALUES
('System Admin', 'admin@stm.ac.ke', '$2y$10$A0QHJ7MFHAnYpLEB.E7Tc.63GiGi7wZe5YCG8VvI7YsV7e59ri.BS');

INSERT INTO departments(name, slug, description) VALUES
('Health Sciences', 'health-sciences', 'Training students for practical health sector roles.'),
('Biomedical & Technology', 'biomedical-technology', 'Technology-driven medical and diagnostics training.'),
('Community and Social Sciences', 'community-social-sciences', 'Community-centered development and support programs.');

INSERT INTO programmes(name, slug, category, terms, department_id, description) VALUES
('Diploma in Mortuary Science', 'diploma-in-mortuary-science', 'Diploma', 6, 1, 'Professional mortuary science training with practical labs.'),
('Diploma in Optometry Technology', 'diploma-in-optometry-technology', 'Diploma', 6, 1, 'Eye care diagnostics and optical technology skills.'),
('Diploma in Perioperative Theater Technology', 'diploma-in-perioperative-theater-technology', 'Diploma', 6, 1, 'Operating theatre preparation and perioperative procedures.'),
('Diploma in Orthopedic & Trauma Medicine', 'diploma-in-orthopedic-trauma-medicine', 'Diploma', 6, 1, 'Trauma support and orthopedic care practices.'),
('Certificate in Perioperative Theatre Technology', 'certificate-in-perioperative-theatre-technology', 'Certificate', 4, 1, 'Theatre support foundational skills and protocols.'),
('Certificate in Health Service Support', 'certificate-in-health-service-support', 'Certificate', 4, 1, 'Core healthcare service operations and patient support.'),
('Certificate in Orthopedic & Trauma Medicine', 'certificate-in-orthopedic-trauma-medicine', 'Certificate', 4, 1, 'Basic trauma and orthopedic support competencies.'),
('Certificate in Mortuary Science', 'certificate-in-mortuary-science', 'Certificate', 4, 1, 'Fundamentals of mortuary operations and ethics.'),
('Caregiving (CNA)', 'caregiving-cna', 'Short Course', 2, 1, 'Compassionate caregiving and nursing assistant fundamentals.'),
('Certificate in Dental Assistant', 'certificate-in-dental-assistant', 'Certificate', 4, 1, 'Chair-side assistance and dental clinic operations.'),
('Certificate in Counselling Psychology', 'certificate-in-counselling-psychology', 'Certificate', 4, 3, 'Basic counselling approaches and ethical practice.'),
('Artisan in Health Service Support', 'artisan-in-health-service-support', 'Artisan', 2, 1, 'Entry-level practical health support training.'),
('Diploma in Community Health', 'diploma-in-community-health', 'Diploma', 6, 3, 'Community health planning, promotion, and outreach.'),
('Certificate in Community Health', 'certificate-in-community-health', 'Certificate', 4, 3, 'Foundation in community health support skills.'),
('Diploma in Counselling Psychology', 'diploma-in-counselling-psychology', 'Diploma', 6, 3, 'In-depth counselling methods and supervised practice.'),
('Diploma in Biomedical Engineering', 'diploma-in-biomedical-engineering', 'Diploma', 6, 2, 'Biomedical devices, maintenance, and diagnostics.'),
('Certificate in Biomedical Engineering', 'certificate-in-biomedical-engineering', 'Certificate', 4, 2, 'Core biomedical equipment support and troubleshooting.');

INSERT INTO news(title, slug, summary, body, image_path) VALUES
('New Skills Lab Launch', 'new-skills-lab-launch', 'Our new simulation lab is now open for training.', 'The institute has launched a modern simulation lab to improve practical training outcomes.', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=700'),
('May Intake Ongoing', 'may-intake-ongoing', 'Applications are open for all major programmes.', 'Students are encouraged to apply early for placement and support.', 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=700');

INSERT INTO careers(title, slug, summary, body, image_path) VALUES
('Lecturer - Biomedical Engineering', 'lecturer-biomedical-engineering', 'Join our faculty to shape future professionals.', 'Minimum 3 years teaching and industry experience preferred.', 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=700');

INSERT INTO tenders(title, slug, summary, body, image_path) VALUES
('Tender for Lab Equipment Supply', 'tender-lab-equipment-supply', 'Procurement notice for modern lab equipment.', 'Qualified suppliers are invited to submit proposals.', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=700');

INSERT INTO events(title, slug, starts_at, category, time_label, location, venue_type, registration_status, summary, body, image_path, is_featured) VALUES
('Career Guidance & Course Advisory Day', 'career-guidance-course-advisory-day', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Career Days', '9:00 AM - EAT', 'Main Campus Hall', 'Campus', 'Open', 'Meet our admissions team and get course guidance.', 'Join us for a one-day advisory session. Learn about entry requirements, course structure, and how to apply.', 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900', 1),
('Health Skills Practical Workshop', 'health-skills-practical-workshop', DATE_ADD(CURDATE(), INTERVAL 28 DAY), 'Clinical Training Sessions', '10:00 AM - EAT', 'Clinical Skills Lab', 'Campus', 'Closing soon', 'Hands-on practical skills workshop with instructors.', 'A practical workshop covering core clinical skills, safety protocols, and career tips from our trainers.', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=900', 0);

INSERT INTO gallery(title, category, image_path) VALUES
('Graduation Day 2025', 'Graduations', '/uploads/gallery/sample1.jpg'),
('Clinical Practice Session', 'Classes', '/uploads/gallery/sample2.jpg'),
('Science Lab Demonstration', 'Labs', '/uploads/gallery/sample3.jpg');

INSERT INTO library_resources(title, summary, file_path) VALUES
('Student Handbook 2026', 'Policies, code of conduct and student services.', '/uploads/library/handbook.pdf'),
('Clinical Attachment Guide', 'Guidelines for placements and assessments.', '/uploads/library/attachment-guide.pdf');

INSERT INTO faqs(question, answer) VALUES
('How do I apply?', 'Visit the Programmes page, select your course, then contact admissions or apply through the office.'),
('Do you offer short courses?', 'Yes. We offer market-relevant short courses and artisan pathways.');

INSERT INTO pages(title, slug, content) VALUES
('About St. Mary\'s', 'about', 'St. Mary\'s Technical Institute delivers practical, inclusive and transformation-focused technical education for Kenyan learners.'),
('The Principal', 'principal', 'Our institution is guided by a vision of competence, compassion and excellence. We welcome every learner ready to grow.'),
('Registrar', 'registrar', 'The Registrar office supports admissions, records and progression with timely and transparent service.');

INSERT INTO settings(setting_key, setting_value) VALUES
('phone', '+254 791 309011 or +254101711499'),
('email', 'contact@stmarysmchmcollege.ac.ke'),
('location', 'Amani House, along Biashara Street, Kiambu Town'),
('top_message', 'Admissions Open - Apply Today'),
('registrar_email', 'registrar@stmarysmchmcollege.ac.ke'),
('admission_number_format', 'STM/{YEAR}/{SEQ4}');

INSERT INTO student_announcements(title, body) VALUES
('Welcome to Student Portal', 'This portal helps you access announcements, timetables, and key academic updates.'),
('May Intake Reminder', 'New students should complete onboarding documentation before reporting date.');

INSERT INTO student_timetables(title, file_path) VALUES
('Semester 1 Timetable', '/uploads/library/semester-1-timetable.pdf');
