-- Migration for Accounts/Billing Module
-- Create tables for invoices, payments, and payment methods

-- Payment Methods Table
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default payment methods
INSERT INTO payment_methods (name, description, is_active) VALUES
('M-PESA', 'Mobile money payment via M-PESA', 1),
('Bankers Cheque', 'Payment via bankers cheque', 1),
('Bank Transfer', 'Direct bank transfer', 1),
('Cash', 'Cash payment at the office', 1),
('Card', 'Credit/Debit card payment', 1);

-- Invoices Table
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    student_id INT NOT NULL,
    programme_id INT,
    term_id INT,
    academic_session_id INT,
    course_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    amount DECIMAL(10, 2) NOT NULL,
    due_date DATE,
    status ENUM('pending', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student_accounts(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) NOT NULL UNIQUE,
    invoice_id INT NOT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method_id INT NOT NULL,
    transaction_code VARCHAR(100),
    cheque_number VARCHAR(100),
    bank_name VARCHAR(100),
    payment_date DATE NOT NULL,
    notes TEXT,
    receipt_generated TINYINT(1) DEFAULT 0,
    receipt_path VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES student_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    INDEX idx_payment_number (payment_number),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_student_id (student_id),
    INDEX idx_transaction_code (transaction_code),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fee Items Table (for invoice line items)
CREATE TABLE IF NOT EXISTS fee_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Student Balance View (for easy balance queries)
CREATE OR REPLACE VIEW student_balance_view AS
SELECT 
    s.id AS student_id,
    s.admission_number,
    s.name AS student_name,
    s.programme_id,
    p.name AS programme_name,
    COALESCE(SUM(i.amount), 0) AS total_invoiced,
    COALESCE(SUM(pay.amount), 0) AS total_paid,
    COALESCE(SUM(i.amount), 0) - COALESCE(SUM(pay.amount), 0) AS balance
FROM student_accounts s
LEFT JOIN programmes p ON s.programme_id = p.id
LEFT JOIN invoices i ON s.id = i.student_id AND i.status != 'cancelled'
LEFT JOIN payments pay ON i.id = pay.invoice_id
GROUP BY s.id, s.admission_number, s.name, s.programme_id, p.name;
