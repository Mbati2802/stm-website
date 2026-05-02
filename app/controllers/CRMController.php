<?php

use app\core\CRMAuth;

class CRMController
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function login(): void
    {
        if (CRMAuth::check()) {
            $this->redirect('crm/dashboard');
            return;
        }

        $error = $_GET['error'] ?? null;
        
        $this->view('crm/login', [
            'metaTitle' => 'CRM Login',
            'error' => $error
        ], 'crm');
    }

    public function authenticate(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            header('Location: /crm/login?error=Please fill in all fields');
            exit;
        }

        try {
            $config = require __DIR__ . '/../../config/crm_config.php';
            $pdo = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                $config['db']['options']
            );

            $stmt = $pdo->prepare('SELECT * FROM crm_users WHERE username = ? AND status = "active"');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !CRMAuth::verifyPassword($password, $user['password_hash'])) {
                header('Location: /crm/login?error=Invalid credentials');
                exit;
            }

            CRMAuth::login($user['id'], $user['username'], $user['role']);
            header('Location: /crm/dashboard');
            exit;
        } catch (PDOException $e) {
            header('Location: /crm/login?error=Database error');
            exit;
        }
    }

    public function logout(): void
    {
        CRMAuth::logout();
        header('Location: /crm/login');
        exit;
    }

    public function dashboard(): void
    {
        CRMAuth::requireLogin();

        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        // Get dashboard metrics
        $totalInquiries = $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
        $contacted = $pdo->query('SELECT COUNT(*) FROM leads WHERE status_id = 2')->fetchColumn();
        $interested = $pdo->query('SELECT COUNT(*) FROM leads WHERE status_id = 3')->fetchColumn();
        $offersIssued = $pdo->query('SELECT COUNT(*) FROM leads WHERE status_id = 4')->fetchColumn();
        $registrationPaid = $pdo->query('SELECT COUNT(*) FROM leads WHERE status_id = 6')->fetchColumn();
        $enrolled = $pdo->query('SELECT COUNT(*) FROM leads WHERE status_id = 7')->fetchColumn();
        $lost = $pdo->query('SELECT COUNT(*) FROM leads WHERE status_id = 8')->fetchColumn();

        // Revenue from registration fees
        $revenue = $pdo->query('SELECT COALESCE(SUM(amount), 0) FROM crm_payments WHERE status = "verified"')->fetchColumn();

        // Conversion rate
        $conversionRate = $totalInquiries > 0 ? round(($registrationPaid / $totalInquiries) * 100, 1) : 0;

        // Recent leads
        $stmt = $pdo->prepare('SELECT l.*, s.name as status_name, s.color as status_color 
                                 FROM leads l 
                                 LEFT JOIN crm_statuses s ON l.status_id = s.id 
                                 ORDER BY l.created_at DESC LIMIT 10');
        $stmt->execute();
        $recentLeads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Recent payments
        $stmt = $pdo->prepare('SELECT p.*, l.name as lead_name, l.phone 
                                 FROM crm_payments p 
                                 LEFT JOIN leads l ON p.lead_id = l.id 
                                 ORDER BY p.created_at DESC LIMIT 5');
        $stmt->execute();
        $recentPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('crm/dashboard', [
            'metaTitle' => 'CRM Dashboard',
            'totalInquiries' => $totalInquiries,
            'contacted' => $contacted,
            'interested' => $interested,
            'offersIssued' => $offersIssued,
            'registrationPaid' => $registrationPaid,
            'enrolled' => $enrolled,
            'lost' => $lost,
            'revenue' => $revenue,
            'conversionRate' => $conversionRate,
            'recentLeads' => $recentLeads,
            'recentPayments' => $recentPayments
        ], 'crm');
    }

    public function leads(): void
    {
        CRMAuth::requireLogin();

        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        // Get all leads with status
        $stmt = $pdo->prepare('SELECT l.*, s.name as status_name, s.color as status_color, u.name as officer_name
                                 FROM leads l 
                                 LEFT JOIN crm_statuses s ON l.status_id = s.id 
                                 LEFT JOIN crm_users u ON l.assigned_officer_id = u.id
                                 ORDER BY l.created_at DESC');
        $stmt->execute();
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all statuses
        $stmt = $pdo->query('SELECT * FROM crm_statuses ORDER BY order_index');
        $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('crm/leads', [
            'metaTitle' => 'Leads',
            'leads' => $leads,
            'statuses' => $statuses
        ], 'crm');
    }

    public function createLead(): void
    {
        CRMAuth::requireLogin();

        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $courseInterest = trim($_POST['course_interest'] ?? '');
            $intakeId = (int)($_POST['intake_id'] ?? 0);
            $location = trim($_POST['location'] ?? '');
            $leadSource = $_POST['lead_source'] ?? 'other';
            $notes = trim($_POST['notes'] ?? '');

            if ($name === '' || $phone === '' || $leadSource === '') {
                echo json_encode(['success' => false, 'message' => 'Name, phone, and lead source are required']);
                exit;
            }

            // Get first status (New Inquiry)
            $stmt = $pdo->query('SELECT id FROM crm_statuses ORDER BY order_index LIMIT 1');
            $statusId = $stmt->fetchColumn();

            $stmt = $pdo->prepare('INSERT INTO leads (name, phone, email, course_interest, intake_id, location, lead_source, status_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $phone, $email, $courseInterest, $intakeId ?: null, $location, $leadSource, $statusId, $notes]);

            echo json_encode(['success' => true, 'message' => 'Lead created successfully']);
            exit;
        }

        // Get intakes
        $stmt = $pdo->query('SELECT * FROM intakes WHERE status != "inactive" ORDER BY start_date DESC');
        $intakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('crm/create_lead', [
            'metaTitle' => 'Create Lead',
            'intakes' => $intakes
        ], 'crm');
    }

    public function viewLead(int $id): void
    {
        CRMAuth::requireLogin();

        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        // Get lead details
        $stmt = $pdo->prepare('SELECT l.*, s.name as status_name, s.color as status_color, s.order_index as status_order,
                                 u.name as officer_name, i.name as intake_name
                                 FROM leads l 
                                 LEFT JOIN crm_statuses s ON l.status_id = s.id 
                                 LEFT JOIN crm_users u ON l.assigned_officer_id = u.id
                                 LEFT JOIN intakes i ON l.intake_id = i.id
                                 WHERE l.id = ?');
        $stmt->execute([$id]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lead) {
            header('Location: /crm/leads');
            exit;
        }

        // Get all statuses
        $stmt = $pdo->query('SELECT * FROM crm_statuses ORDER BY order_index');
        $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get lead history
        $stmt = $pdo->prepare('SELECT lh.*, s.name as old_status_name, s2.name as new_status_name, u.name as changed_by_name
                                 FROM lead_history lh
                                 LEFT JOIN crm_statuses s ON lh.old_status_id = s.id
                                 LEFT JOIN crm_statuses s2 ON lh.new_status_id = s2.id
                                 LEFT JOIN crm_users u ON lh.changed_by = u.id
                                 WHERE lh.lead_id = ?
                                 ORDER BY lh.created_at DESC');
        $stmt->execute([$id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get communication logs
        $stmt = $pdo->prepare('SELECT * FROM communication_logs WHERE lead_id = ? ORDER BY sent_at DESC');
        $stmt->execute([$id]);
        $communications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get payments
        $stmt = $pdo->prepare('SELECT p.*, u.name as verified_by_name
                                 FROM crm_payments p
                                 LEFT JOIN crm_users u ON p.verified_by = u.id
                                 WHERE p.lead_id = ?
                                 ORDER BY p.created_at DESC');
        $stmt->execute([$id]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get admission offers
        $stmt = $pdo->prepare('SELECT * FROM admission_offers WHERE lead_id = ? ORDER BY created_at DESC');
        $stmt->execute([$id]);
        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('crm/view_lead', [
            'metaTitle' => 'Lead Details',
            'lead' => $lead,
            'statuses' => $statuses,
            'history' => $history,
            'communications' => $communications,
            'payments' => $payments,
            'offers' => $offers
        ], 'crm');
    }

    public function updateLeadStatus(): void
    {
        CRMAuth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $leadId = (int)($_POST['lead_id'] ?? 0);
        $newStatusId = (int)($_POST['status_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        if ($leadId === 0 || $newStatusId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        try {
            $config = require __DIR__ . '/../../config/crm_config.php';
            $pdo = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                $config['db']['options']
            );

            // Get current status
            $stmt = $pdo->prepare('SELECT status_id FROM leads WHERE id = ?');
            $stmt->execute([$leadId]);
            $currentStatusId = $stmt->fetchColumn();

            // Update lead status
            $stmt = $pdo->prepare('UPDATE leads SET status_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$newStatusId, $leadId]);

            // Log status change in history
            $stmt = $pdo->prepare('INSERT INTO lead_history (lead_id, old_status_id, new_status_id, changed_by, notes) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$leadId, $currentStatusId, $newStatusId, CRMAuth::id(), $notes]);

            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
    }

    public function assignLead(): void
    {
        CRMAuth::requireLogin();

        $leadId = (int)($_POST['lead_id'] ?? 0);
        $officerId = (int)($_POST['officer_id'] ?? 0);

        if ($leadId === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid lead ID']);
            exit;
        }

        try {
            $config = require __DIR__ . '/../../config/crm_config.php';
            $pdo = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                $config['db']['options']
            );

            $stmt = $pdo->prepare('UPDATE leads SET assigned_officer_id = ? WHERE id = ?');
            $stmt->execute([$officerId ?: null, $leadId]);

            echo json_encode(['success' => true, 'message' => 'Lead assigned successfully']);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
    }

    private function redirect(string $path): void
    {
        header('Location: /' . ltrim($path, '/'));
        exit;
    }

    private function view(string $view, array $data = [], string $layout = null): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            echo 'View not found: ' . $view;
            return;
        }

        if ($layout) {
            $layoutPath = __DIR__ . '/../views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                require_once $layoutPath;
            } else {
                require_once $viewPath;
            }
        } else {
            require_once $viewPath;
        }
    }
}
