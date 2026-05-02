<?php

class CommunicationService
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function sendSMS(string $phone, string $message): bool
    {
        if (!$this->config['sms']['enabled']) {
            return false;
        }

        // Log the SMS attempt
        error_log("SMS to {$phone}: {$message}");

        // TODO: Integrate with SMS provider (Africa's Talking, BulkSMS, etc.)
        // For now, just return true to simulate success
        return true;
    }

    public function sendWhatsApp(string $phone, string $message): bool
    {
        if (!$this->config['whatsapp']['enabled']) {
            return false;
        }

        // Log the WhatsApp attempt
        error_log("WhatsApp to {$phone}: {$message}");

        // TODO: Integrate with WhatsApp Business API or third-party service
        // For now, just return true to simulate success
        return true;
    }

    public function sendEmail(string $to, string $subject, string $message): bool
    {
        if (!$this->config['email']['enabled']) {
            return false;
        }

        // Log the email attempt
        error_log("Email to {$to}: {$subject}");

        // TODO: Integrate with email service (PHPMailer, SendGrid, etc.)
        // For now, just return true to simulate success
        return true;
    }

    public function logCommunication(int $leadId, string $type, string $message, ?int $sentBy = null): bool
    {
        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        try {
            $stmt = $pdo->prepare('INSERT INTO communication_logs (lead_id, type, message, sent_by, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$leadId, $type, $message, $sentBy, 'sent']);
            return true;
        } catch (PDOException $e) {
            error_log("Failed to log communication: " . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentConfirmation(int $leadId, float $amount, string $transactionCode): bool
    {
        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        // Get lead details
        $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ?');
        $stmt->execute([$leadId]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lead) {
            return false;
        }

        $message = "Dear " . $lead['name'] . ", your registration fee payment of KES " . number_format($amount, 2) . " (Ref: " . $transactionCode . ") has been received. Your admission is now confirmed. Welcome to St. Mary's MCH Medical Training College!";

        // Send SMS
        $smsSent = $this->sendSMS($lead['phone'], $message);
        
        // Log the communication
        $this->logCommunication($leadId, 'sms', $message, null);

        return $smsSent;
    }

    public function sendAdmissionOfferReminder(int $leadId): bool
    {
        $config = require __DIR__ . '/../../config/crm_config.php';
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['options']
        );

        // Get lead details with admission offer
        $stmt = $pdo->prepare('SELECT l.*, ao.expiry_date 
                                 FROM leads l 
                                 LEFT JOIN admission_offers ao ON l.id = ao.lead_id 
                                 WHERE l.id = ? 
                                 ORDER BY ao.created_at DESC LIMIT 1');
        $stmt->execute([$leadId]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lead || !$lead['expiry_date']) {
            return false;
        }

        $message = "Dear " . $lead['name'] . ", your admission offer for " . ($lead['course_interest'] ?? 'Medical Training') . " at St. Mary's MCH Medical Training College expires on " . date('F j, Y', strtotime($lead['expiry_date'])) . ". Please pay your registration fee of KES 5,000 to secure your seat. Call us for payment details.";

        // Send SMS
        $smsSent = $this->sendSMS($lead['phone'], $message);
        
        // Log the communication
        $this->logCommunication($leadId, 'sms', $message, null);

        return $smsSent;
    }
}
