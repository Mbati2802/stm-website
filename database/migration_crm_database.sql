-- CRM Database Migration for Student Admissions CRM
-- This adds CRM tables to your existing database
-- Run this in your existing database (stmarys2_stm)

-- CRM Users Table
CREATE TABLE IF NOT EXISTS crm_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'officer') NOT NULL DEFAULT 'officer',
    phone VARCHAR(20),
    email VARCHAR(100),
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CRM Statuses Table
CREATE TABLE IF NOT EXISTS crm_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    order_index INT NOT NULL,
    color VARCHAR(7) DEFAULT '#6c757d',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default CRM statuses
INSERT INTO crm_statuses (name, description, order_index, color) VALUES
('New Inquiry', 'Lead captured from any source', 1, '#6c757d'),
('Contacted', 'Admissions team has reached out', 2, '#17a2b8'),
('Interested', 'Lead has shown interest in courses', 3, '#20c997'),
('Admission Offered', 'Admission letter has been issued', 4, '#fd7e14'),
('Payment Pending', 'Offer made but no registration fee yet', 5, '#ffc107'),
('Registration Paid', 'Registration fee paid - Converted student', 6, '#28a745'),
('Enrolled', 'Student reported to campus', 7, '#007bff'),
('Lost', 'Lead not converted', 8, '#dc3545')
ON DUPLICATE KEY UPDATE name=name;

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    duration VARCHAR(50),
    fee_structure TEXT,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Intakes Table
CREATE TABLE IF NOT EXISTS intakes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'inactive', 'upcoming') NOT NULL DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leads Table
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    program_interest VARCHAR(100),
    intake_id INT,
    location VARCHAR(100),
    lead_source ENUM('website', 'social_media', 'whatsapp', 'call', 'walk_in', 'referral', 'other') NOT NULL,
    status_id INT NOT NULL,
    assigned_officer_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (status_id) REFERENCES crm_statuses(id),
    FOREIGN KEY (assigned_officer_id) REFERENCES crm_users(id) ON DELETE SET NULL,
    FOREIGN KEY (intake_id) REFERENCES intakes(id) ON DELETE SET NULL,
    INDEX idx_status (status_id),
    INDEX idx_officer (assigned_officer_id),
    INDEX idx_phone (phone),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admission Offers Table
CREATE TABLE IF NOT EXISTS admission_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    offer_type ENUM('provisional', 'confirmed') NOT NULL DEFAULT 'provisional',
    letter_generated BOOLEAN NOT NULL DEFAULT FALSE,
    letter_path VARCHAR(255),
    issued_date DATE,
    expiry_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    INDEX idx_lead (lead_id),
    INDEX idx_type (offer_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CRM Payments Table
CREATE TABLE IF NOT EXISTS crm_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('mpesa', 'bank', 'cash', 'online') NOT NULL,
    transaction_code VARCHAR(100),
    payment_date DATE NOT NULL,
    receipt_number VARCHAR(50),
    verified_by INT,
    verified_at TIMESTAMP NULL,
    notes TEXT,
    status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES crm_users(id) ON DELETE SET NULL,
    INDEX idx_lead (lead_id),
    INDEX idx_status (status),
    INDEX idx_transaction (transaction_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Communication Logs Table
CREATE TABLE IF NOT EXISTS communication_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    type ENUM('whatsapp', 'sms', 'email', 'call') NOT NULL,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_by INT,
    status ENUM('sent', 'delivered', 'failed', 'pending') NOT NULL DEFAULT 'pending',
    error_message TEXT,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (sent_by) REFERENCES crm_users(id) ON DELETE SET NULL,
    INDEX idx_lead (lead_id),
    INDEX idx_type (type),
    INDEX idx_sent (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reminders Table
CREATE TABLE IF NOT EXISTS reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    reminder_type ENUM('day1_payment', 'day3_reminder', 'day7_urgency', 'post_payment_welcome') NOT NULL,
    scheduled_date DATE NOT NULL,
    sent_date DATE,
    status ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    INDEX idx_lead (lead_id),
    INDEX idx_scheduled (scheduled_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lead History Table (for tracking status changes)
CREATE TABLE IF NOT EXISTS lead_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    old_status_id INT,
    new_status_id INT NOT NULL,
    changed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (old_status_id) REFERENCES crm_statuses(id) ON DELETE SET NULL,
    FOREIGN KEY (new_status_id) REFERENCES crm_statuses(id),
    FOREIGN KEY (changed_by) REFERENCES crm_users(id) ON DELETE SET NULL,
    INDEX idx_lead (lead_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table (for CRM configuration)
CREATE TABLE IF NOT EXISTS crm_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default CRM admin user (password: admin123 - should be changed immediately)
INSERT INTO crm_users (username, password_hash, name, role, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CRM Administrator', 'admin', 'admin@stmarysmchmcollege.ac.ke')
ON DUPLICATE KEY UPDATE username=username;

-- Insert default CRM settings
INSERT INTO crm_settings (setting_key, setting_value, description) VALUES
('registration_fee_amount', '5000', 'Registration fee amount in KES'),
('payment_reminder_day1', 'true', 'Enable day 1 payment reminder'),
('payment_reminder_day3', 'true', 'Enable day 3 payment reminder'),
('payment_reminder_day7', 'true', 'Enable day 7 urgency reminder'),
('whatsapp_enabled', 'false', 'WhatsApp integration enabled'),
('sms_enabled', 'true', 'SMS integration enabled'),
('email_enabled', 'true', 'Email integration enabled')
ON DUPLICATE KEY UPDATE setting_key=setting_key;
