<?php

class AccountsController extends Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function index(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('students')) {
            $this->redirect('admin');
            return;
        }

        try {
            $pdo = Database::getInstance($this->config['db']);
            
            // Check if invoices table exists
            $stmt = $pdo->prepare("SHOW TABLES LIKE 'invoices'");
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                $this->view('admin/accounts/index', [
                    'metaTitle' => 'Accounts & Billing',
                    'invoices' => [],
                    'programmes' => [],
                    'terms' => [],
                    'sessions' => [],
                    'courses' => [],
                    'filters' => [
                        'programme_id' => '',
                        'term_id' => '',
                        'session_id' => '',
                        'course_id' => '',
                        'status' => '',
                    ],
                    'error' => 'Database tables not found. Please run the migration: database/migration_accounts_billing.sql'
                ]);
                return;
            }
            
            // Get filters
            $programmeId = $_GET['programme_id'] ?? '';
            $termId = $_GET['term_id'] ?? '';
            $sessionId = $_GET['session_id'] ?? '';
            $courseId = $_GET['course_id'] ?? '';
            $status = $_GET['status'] ?? '';

            // Build query with filters
            $where = [];
            $params = [];
            
            if (!empty($programmeId)) {
                $where[] = 'i.programme_id = ?';
                $params[] = (int)$programmeId;
            }
            if (!empty($termId)) {
                $where[] = 'i.term_id = ?';
                $params[] = (int)$termId;
            }
            if (!empty($sessionId)) {
                $where[] = 'i.academic_session_id = ?';
                $params[] = (int)$sessionId;
            }
            if (!empty($courseId)) {
                $where[] = 'i.course_id = ?';
                $params[] = (int)$courseId;
            }
            if (!empty($status)) {
                $where[] = 'i.status = ?';
                $params[] = $status;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Get invoices with student and programme info
            $sql = "SELECT i.*, s.name AS student_name, s.admission_number, 
                           p.name AS programme_name, p.abbreviation AS programme_abbr,
                           t.name AS term_name, ses.name AS session_name,
                           COALESCE(SUM(pay.amount), 0) AS paid_amount,
                           i.amount - COALESCE(SUM(pay.amount), 0) AS balance
                    FROM invoices i
                    LEFT JOIN student_accounts s ON i.student_id = s.id
                    LEFT JOIN programmes p ON i.programme_id = p.id
                    LEFT JOIN terms t ON i.term_id = t.id
                    LEFT JOIN academic_sessions ses ON i.academic_session_id = ses.id
                    LEFT JOIN payments pay ON i.id = pay.invoice_id
                    $whereClause
                    GROUP BY i.id
                    ORDER BY i.created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get filter options - handle missing tables gracefully
            $programmes = [];
            try {
                $programmes = $pdo->query('SELECT id, name, abbreviation FROM programmes ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Programmes table doesn't exist
            }
            
            $terms = [];
            try {
                $terms = $pdo->query('SELECT id, name FROM terms ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Terms table doesn't exist
            }
            
            $sessions = [];
            try {
                $sessions = $pdo->query('SELECT id, name FROM academic_sessions ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // academic_sessions table doesn't exist
            }
            
            $courses = [];
            try {
                $courses = $pdo->query('SELECT id, code, title FROM courses ORDER BY code')->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // courses table doesn't exist
            }

            $this->view('admin/accounts/index', [
                'metaTitle' => 'Accounts & Billing',
                'invoices' => $invoices,
                'programmes' => $programmes,
                'terms' => $terms,
                'sessions' => $sessions,
                'courses' => $courses,
                'filters' => [
                    'programme_id' => $programmeId,
                    'term_id' => $termId,
                    'session_id' => $sessionId,
                    'course_id' => $courseId,
                    'status' => $status,
                ]
            ]);
        } catch (PDOException $e) {
            $this->view('admin/accounts/index', [
                'metaTitle' => 'Accounts & Billing',
                'invoices' => [],
                'programmes' => [],
                'terms' => [],
                'sessions' => [],
                'courses' => [],
                'filters' => [
                    'programme_id' => '',
                    'term_id' => '',
                    'session_id' => '',
                    'course_id' => '',
                    'status' => '',
                ],
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Throwable $e) {
            $this->view('admin/accounts/index', [
                'metaTitle' => 'Accounts & Billing',
                'invoices' => [],
                'programmes' => [],
                'terms' => [],
                'sessions' => [],
                'courses' => [],
                'filters' => [
                    'programme_id' => '',
                    'term_id' => '',
                    'session_id' => '',
                    'course_id' => '',
                    'status' => '',
                ],
                'error' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function createInvoice(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('students')) {
            $this->redirect('admin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = (int)($_POST['student_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $amount = (float)($_POST['amount'] ?? 0);
            $dueDate = $_POST['due_date'] ?? '';
            $programmeId = !empty($_POST['programme_id']) ? (int)$_POST['programme_id'] : null;
            $termId = !empty($_POST['term_id']) ? (int)$_POST['term_id'] : null;
            $sessionId = !empty($_POST['session_id']) ? (int)$_POST['session_id'] : null;
            $courseId = !empty($_POST['course_id']) ? (int)$_POST['course_id'] : null;

            if ($studentId === 0 || empty($title) || $amount <= 0) {
                flash('error', 'Student, title, and amount are required.');
                $this->redirect('admin/accounts/create-invoice');
                return;
            }

            $pdo = Database::getInstance($this->config['db']);

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber($pdo);

            // Insert invoice
            $stmt = $pdo->prepare('INSERT INTO invoices (invoice_number, student_id, programme_id, term_id, academic_session_id, course_id, title, description, amount, due_date, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $invoiceNumber,
                $studentId,
                $programmeId,
                $termId,
                $sessionId,
                $courseId,
                $title,
                $description,
                $amount,
                $dueDate,
                'pending',
                $_SESSION['admin_id'] ?? null
            ]);

            $invoiceId = $pdo->lastInsertId();

            // Add fee items if provided
            if (!empty($_POST['fee_items'])) {
                foreach ($_POST['fee_items'] as $item) {
                    if (!empty($item['description']) && !empty($item['amount'])) {
                        $stmt = $pdo->prepare('INSERT INTO fee_items (invoice_id, description, amount) VALUES (?, ?, ?)');
                        $stmt->execute([$invoiceId, trim($item['description']), (float)$item['amount']]);
                    }
                }
            }

            flash('success', 'Invoice created successfully.');
            $this->redirect('admin/accounts');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        $students = $pdo->query('SELECT id, name, admission_number, programme_id FROM student_accounts ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $programmes = $pdo->query('SELECT id, name, abbreviation FROM programmes ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $terms = $pdo->query('SELECT id, name FROM terms ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $sessions = $pdo->query('SELECT id, name FROM academic_sessions ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $courses = $pdo->query('SELECT id, code, title FROM courses ORDER BY code')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/accounts/create_invoice', [
            'metaTitle' => 'Create Invoice',
            'students' => $students,
            'programmes' => $programmes,
            'terms' => $terms,
            'sessions' => $sessions,
            'courses' => $courses
        ]);
    }

    public function viewInvoice(int $id): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('students')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        
        // Get invoice details
        $stmt = $pdo->prepare('SELECT i.*, s.name AS student_name, s.admission_number, s.email AS student_email,
                                      p.name AS programme_name, p.abbreviation AS programme_abbr,
                                      t.name AS term_name, ses.name AS session_name,
                                      c.code AS course_code, c.title AS course_title
                               FROM invoices i
                               LEFT JOIN student_accounts s ON i.student_id = s.id
                               LEFT JOIN programmes p ON i.programme_id = p.id
                               LEFT JOIN terms t ON i.term_id = t.id
                               LEFT JOIN academic_sessions ses ON i.academic_session_id = ses.id
                               LEFT JOIN courses c ON i.course_id = c.id
                               WHERE i.id = ?');
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            flash('error', 'Invoice not found.');
            $this->redirect('admin/accounts');
            return;
        }

        // Get fee items
        $stmt = $pdo->prepare('SELECT * FROM fee_items WHERE invoice_id = ?');
        $stmt->execute([$id]);
        $feeItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get payments
        $stmt = $pdo->prepare('SELECT p.*, pm.name AS payment_method_name 
                               FROM payments p
                               LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
                               WHERE p.invoice_id = ? 
                               ORDER BY p.payment_date DESC');
        $stmt->execute([$id]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate totals
        $totalPaid = array_sum(array_column($payments, 'amount'));
        $balance = $invoice['amount'] - $totalPaid;

        $this->view('admin/accounts/view_invoice', [
            'metaTitle' => 'Invoice ' . $invoice['invoice_number'],
            'invoice' => $invoice,
            'feeItems' => $feeItems,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'balance' => $balance
        ]);
    }

    public function recordPayment(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('students')) {
            $this->redirect('admin');
            return;
        }

        $invoiceId = (int)($_GET['invoice_id'] ?? 0);
        
        if ($invoiceId === 0) {
            flash('error', 'Invoice ID is required.');
            $this->redirect('admin/accounts');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceId = (int)($_POST['invoice_id'] ?? 0);
            $studentId = (int)($_POST['student_id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0);
            $paymentMethodId = (int)($_POST['payment_method_id'] ?? 0);
            $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');
            $transactionCode = trim($_POST['transaction_code'] ?? '');
            $chequeNumber = trim($_POST['cheque_number'] ?? '');
            $bankName = trim($_POST['bank_name'] ?? '');
            $notes = trim($_POST['notes'] ?? '');

            if ($invoiceId === 0 || $studentId === 0 || $amount <= 0 || $paymentMethodId === 0) {
                flash('error', 'Invoice, student, amount, and payment method are required.');
                $this->redirect('admin/accounts/record-payment?invoice_id=' . $invoiceId);
                return;
            }

            $pdo = Database::getInstance($this->config['db']);

            // Generate payment number
            $paymentNumber = $this->generatePaymentNumber($pdo);

            // Insert payment
            $stmt = $pdo->prepare('INSERT INTO payments (payment_number, invoice_id, student_id, amount, payment_method_id, transaction_code, cheque_number, bank_name, payment_date, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $paymentNumber,
                $invoiceId,
                $studentId,
                $amount,
                $paymentMethodId,
                $transactionCode,
                $chequeNumber,
                $bankName,
                $paymentDate,
                $notes,
                $_SESSION['admin_id'] ?? null
            ]);

            // Update invoice status
            $this->updateInvoiceStatus($pdo, $invoiceId);

            flash('success', 'Payment recorded successfully.');
            $this->redirect('admin/accounts/view-invoice/' . $invoiceId);
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        
        // Get invoice details
        $stmt = $pdo->prepare('SELECT i.*, s.name AS student_name, s.admission_number FROM invoices i LEFT JOIN student_accounts s ON i.student_id = s.id WHERE i.id = ?');
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            flash('error', 'Invoice not found.');
            $this->redirect('admin/accounts');
            return;
        }

        // Get payment methods
        $paymentMethods = $pdo->query('SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/accounts/record_payment', [
            'metaTitle' => 'Record Payment',
            'invoice' => $invoice,
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function studentPayments(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('students')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        
        // Get filters
        $programmeId = $_GET['programme_id'] ?? '';
        $termId = $_GET['term_id'] ?? '';
        $sessionId = $_GET['session_id'] ?? '';
        $courseId = $_GET['course_id'] ?? '';

        // Build query with filters
        $where = [];
        $params = [];
        
        if (!empty($programmeId)) {
            $where[] = 's.programme_id = ?';
            $params[] = (int)$programmeId;
        }
        if (!empty($termId)) {
            $where[] = 'i.term_id = ?';
            $params[] = (int)$termId;
        }
        if (!empty($sessionId)) {
            $where[] = 'i.academic_session_id = ?';
            $params[] = (int)$sessionId;
        }
        if (!empty($courseId)) {
            $where[] = 'i.course_id = ?';
            $params[] = (int)$courseId;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get student payment info
        $sql = "SELECT s.id AS student_id, s.name, s.admission_number, s.email,
                       p.name AS programme_name, p.abbreviation AS programme_abbr,
                       COALESCE(SUM(i.amount), 0) AS total_invoiced,
                       COALESCE(SUM(pay.amount), 0) AS total_paid,
                       COALESCE(SUM(i.amount), 0) - COALESCE(SUM(pay.amount), 0) AS balance
                FROM student_accounts s
                LEFT JOIN programmes p ON s.programme_id = p.id
                LEFT JOIN invoices i ON s.id = i.student_id AND i.status != 'cancelled'
                LEFT JOIN payments pay ON i.id = pay.invoice_id
                $whereClause
                GROUP BY s.id, s.name, s.admission_number, s.email, p.name, p.abbreviation
                ORDER BY s.name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get filter options
        $programmes = $pdo->query('SELECT id, name, abbreviation FROM programmes ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $terms = $pdo->query('SELECT id, name FROM terms ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $sessions = $pdo->query('SELECT id, name FROM academic_sessions ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        $courses = $pdo->query('SELECT id, code, title FROM courses ORDER BY code')->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/accounts/student_payments', [
            'metaTitle' => 'Student Payments',
            'students' => $students,
            'programmes' => $programmes,
            'terms' => $terms,
            'sessions' => $sessions,
            'courses' => $courses,
            'filters' => [
                'programme_id' => $programmeId,
                'term_id' => $termId,
                'session_id' => $sessionId,
                'course_id' => $courseId,
            ]
        ]);
    }

    public function generateReceipt(int $paymentId): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('students')) {
            $this->redirect('admin');
            return;
        }

        $pdo = Database::getInstance($this->config['db']);
        
        // Get payment details
        $stmt = $pdo->prepare('SELECT p.*, i.invoice_number, i.title AS invoice_title, i.amount AS invoice_amount,
                                      s.name AS student_name, s.admission_number, s.email AS student_email,
                                      pm.name AS payment_method_name
                               FROM payments p
                               LEFT JOIN invoices i ON p.invoice_id = i.id
                               LEFT JOIN student_accounts s ON p.student_id = s.id
                               LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
                               WHERE p.id = ?');
        $stmt->execute([$paymentId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            flash('error', 'Payment not found.');
            $this->redirect('admin/accounts');
            return;
        }

        // Get invoice balance info
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS total_paid FROM payments WHERE invoice_id = ?');
        $stmt->execute([$payment['invoice_id']]);
        $totalPaid = $stmt->fetch(PDO::FETCH_ASSOC)['total_paid'];

        // Mark receipt as generated
        $stmt = $pdo->prepare('UPDATE payments SET receipt_generated = 1 WHERE id = ?');
        $stmt->execute([$paymentId]);

        $this->view('admin/accounts/receipt', [
            'metaTitle' => 'Receipt ' . $payment['payment_number'],
            'payment' => $payment,
            'totalPaid' => $totalPaid
        ]);
    }

    private function generateInvoiceNumber(PDO $pdo): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM invoices WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?');
        $stmt->execute([$year, $month]);
        $count = $stmt->fetchColumn() + 1;
        
        $sequence = str_pad((string)$count, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}/{$year}/{$month}/{$sequence}";
    }

    private function generatePaymentNumber(PDO $pdo): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');
        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM payments WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?');
        $stmt->execute([$year, $month]);
        $count = $stmt->fetchColumn() + 1;
        
        $sequence = str_pad((string)$count, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}/{$year}/{$month}/{$sequence}";
    }

    private function updateInvoiceStatus(PDO $pdo, int $invoiceId): void
    {
        // Get invoice amount
        $stmt = $pdo->prepare('SELECT amount FROM invoices WHERE id = ?');
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$invoice) {
            return;
        }

        // Get total paid
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS total_paid FROM payments WHERE invoice_id = ?');
        $stmt->execute([$invoiceId]);
        $totalPaid = $stmt->fetch(PDO::FETCH_ASSOC)['total_paid'];

        // Determine status
        $status = 'pending';
        if ($totalPaid >= $invoice['amount']) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        }

        // Check if overdue
        $stmt = $pdo->prepare('SELECT due_date FROM invoices WHERE id = ?');
        $stmt->execute([$invoiceId]);
        $dueDate = $stmt->fetch(PDO::FETCH_ASSOC)['due_date'];
        
        if ($dueDate && $dueDate < date('Y-m-d') && $status === 'pending') {
            $status = 'overdue';
        }

        // Update status
        $stmt = $pdo->prepare('UPDATE invoices SET status = ? WHERE id = ?');
        $stmt->execute([$status, $invoiceId]);
    }
}
