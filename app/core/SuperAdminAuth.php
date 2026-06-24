<?php

class SuperAdminAuth
{
    private static $db;
    private static $super_admin_id = null;
    private static $session_token = null;
    private static $max_failed_attempts = 3;
    private static $lockout_duration = 900; // 15 minutes

    public function __construct()
    {
        self::$db = Database::getInstance();
    }

    /**
     * Initialize super admin session from stored token
     */
    public static function initSession()
    {
        session_start();
        self::ensureDb();
        
        if (isset($_SESSION['super_admin_token'])) {
            $token = $_SESSION['super_admin_token'];
            self::validateSessionToken($token);
        }
    }

    /**
     * Attempt super admin login with email and password
     */
    public static function attempt($email, $password, $ip_address = null, $user_agent = null)
    {
        $ip_address = $ip_address ?? $_SERVER['REMOTE_ADDR'];
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];

        self::ensureDb();

        // Check if IP is whitelisted
        if (!self::isIpWhitelisted($email, $ip_address)) {
            self::logSuspiciousActivity($email, 'unauthorized_ip', "Login attempt from non-whitelisted IP: $ip_address");
            return ['success' => false, 'message' => 'Access from this IP is not permitted'];
        }

        // Get super admin user
        $stmt = self::$db->prepare("
            SELECT id, password_hash, locked_until, failed_login_attempts, 
                   two_fa_enabled, status 
            FROM super_admin 
            WHERE email = ? AND status = 'active'
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            self::logSuspiciousActivity($email, 'invalid_email', "Login attempt with non-existent email");
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Check if account is locked
        if ($admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
            return ['success' => false, 'message' => 'Account is locked. Please try again later.'];
        }

        // Verify password
        if (!password_verify($password, $admin['password_hash'])) {
            self::recordFailedLogin($admin['id'], $ip_address, $user_agent);
            self::logSuspiciousActivity($email, 'failed_login', "Incorrect password attempt");
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Password correct - reset failed attempts
        self::$db->prepare("
            UPDATE super_admin 
            SET failed_login_attempts = 0, locked_until = NULL 
            WHERE id = ?
        ")->execute([$admin['id']]);

        // If 2FA is enabled, send OTP
        if ($admin['two_fa_enabled']) {
            $otp = self::generateOTP();
            $result = self::sendOTP($admin['id'], $otp, $email);
            
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to send 2FA code. Please try again.'];
            }

            // Store temporary session for 2FA verification
            $_SESSION['super_admin_temp'] = [
                'admin_id' => $admin['id'],
                'email' => $email,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'temp_token' => self::generateToken()
            ];

            return ['success' => true, 'requires_2fa' => true, 'message' => '2FA code sent to your email'];
        }

        // No 2FA required - create session
        return self::createSession($admin['id'], $email, $ip_address, $user_agent);
    }

    /**
     * Verify 2FA OTP
     */
    public static function verify2FA($otp_code, $ip_address = null, $user_agent = null)
    {
        if (!isset($_SESSION['super_admin_temp'])) {
            return ['success' => false, 'message' => '2FA session expired. Please login again.'];
        }

        self::ensureDb();

        $temp = $_SESSION['super_admin_temp'];
        $admin_id = $temp['admin_id'];
        $ip_address = $ip_address ?? $_SERVER['REMOTE_ADDR'];
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];

        // Verify OTP from database
        $stmt = self::$db->prepare("
            SELECT id FROM two_fa_otp 
            WHERE super_admin_id = ? 
            AND otp_code = ?
            AND is_used = FALSE
            AND expires_at > NOW()
            AND verified_at IS NULL
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$admin_id, $otp_code]);
        $otp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otp) {
            // Increment failed attempts
            self::$db->prepare("
                UPDATE two_fa_otp 
                SET attempts = attempts + 1 
                WHERE super_admin_id = ? 
                AND is_used = FALSE
                AND expires_at > NOW()
            ")->execute([$admin_id]);

            self::logSuspiciousActivity($temp['email'], 'invalid_otp', "Invalid 2FA code attempt");
            return ['success' => false, 'message' => 'Invalid OTP code'];
        }

        // Mark OTP as verified
        self::$db->prepare("
            UPDATE two_fa_otp 
            SET verified_at = NOW(), is_used = TRUE 
            WHERE id = ?
        ")->execute([$otp['id']]);

        // Clean old OTPs
        self::$db->prepare("
            DELETE FROM two_fa_otp 
            WHERE super_admin_id = ? 
            AND expires_at < NOW()
        ")->execute([$admin_id]);

        // Create session
        return self::createSession($admin_id, $temp['email'], $ip_address, $user_agent);
    }

    /**
     * Create authenticated session
     */
    private static function createSession($admin_id, $email, $ip_address, $user_agent)
    {
        $token = self::generateToken();
        $expires_at = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        // Store session in database
        $stmt = self::$db->prepare("
            INSERT INTO user_sessions 
            (user_id, user_type, session_token, ip_address, user_agent, expires_at) 
            VALUES (?, 'super_admin', ?, ?, ?, ?)
        ");
        $stmt->execute([$admin_id, $token, $ip_address, $user_agent, $expires_at]);

        // Update last login
        self::$db->prepare("
            UPDATE super_admin 
            SET last_login = NOW(), last_login_ip = ?
            WHERE id = ?
        ")->execute([$ip_address, $admin_id]);

        // Set PHP session
        session_regenerate_id(true);
        $_SESSION['super_admin_token'] = $token;
        $_SESSION['super_admin_id'] = $admin_id;
        $_SESSION['super_admin_email'] = $email;

        // Clear temp session
        unset($_SESSION['super_admin_temp']);

        return ['success' => true, 'message' => 'Login successful'];
    }

    /**
     * Validate session token from database
     */
    private static function validateSessionToken($token)
    {
        $stmt = self::$db->prepare("
            SELECT user_id FROM user_sessions 
            WHERE session_token = ? 
            AND user_type = 'super_admin'
            AND is_active = TRUE
            AND expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($session) {
            self::$super_admin_id = $session['user_id'];
            self::$session_token = $token;
            
            // Update last activity
            self::$db->prepare("
                UPDATE user_sessions 
                SET last_activity = NOW() 
                WHERE session_token = ?
            ")->execute([$token]);

            return true;
        }

        return false;
    }

    /**
     * Ensure database connection is initialized
     */
    private static function ensureDb(): void
    {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
    }

    /**
     * Check if IP is whitelisted for super admin
     */
    private static function isIpWhitelisted($email, $ip_address)
    {
        $stmt = self::$db->prepare("
            SELECT ip_whitelist FROM super_admin 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !$admin['ip_whitelist']) {
            return true; // No whitelist means all IPs allowed
        }

        $whitelist = explode(',', $admin['ip_whitelist']);
        $whitelist = array_map('trim', $whitelist);

        return in_array($ip_address, $whitelist);
    }

    /**
     * Record failed login attempt
     */
    private static function recordFailedLogin($admin_id, $ip_address, $user_agent)
    {
        $stmt = self::$db->prepare("
            SELECT failed_login_attempts FROM super_admin WHERE id = ?
        ");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        $attempts = $admin['failed_login_attempts'] + 1;

        $update_data = [
            'failed_login_attempts' => $attempts,
            'last_failed_attempt' => date('Y-m-d H:i:s')
        ];

        // Lock account after max failed attempts
        if ($attempts >= self::$max_failed_attempts) {
            $update_data['locked_until'] = date('Y-m-d H:i:s', time() + self::$lockout_duration);
        }

        $sql = "UPDATE super_admin SET ";
        $fields = [];
        foreach ($update_data as $key => $value) {
            $fields[] = "$key = ?";
        }
        $sql .= implode(', ', $fields) . " WHERE id = ?";

        $values = array_values($update_data);
        $values[] = $admin_id;

        self::$db->prepare($sql)->execute($values);
    }

    /**
     * Generate 6-digit OTP
     */
    private static function generateOTP()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate secure token
     */
    private static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Send OTP via email
     */
    private static function sendOTP($admin_id, $otp, $email)
    {
        // Invalidate any previous unused OTPs for this admin
        self::$db->prepare("
            UPDATE two_fa_otp 
            SET is_used = TRUE 
            WHERE super_admin_id = ? AND is_used = FALSE AND verified_at IS NULL
        ")->execute([$admin_id]);

        // Store new OTP (valid for 10 minutes)
        $expires_at = date('Y-m-d H:i:s', time() + 600);
        
        $stmt = self::$db->prepare("
            INSERT INTO two_fa_otp 
            (super_admin_id, otp_code, otp_type, expires_at) 
            VALUES (?, ?, 'email', ?)
        ");
        $stmt->execute([$admin_id, $otp, $expires_at]);

        // Send email with OTP
        $subject = "Super Admin Login - 2FA Code";
        $message = "
            <h2>Two-Factor Authentication</h2>
            <p>Your 2FA verification code is:</p>
            <h3 style='color: #333;'>{$otp}</h3>
            <p>This code expires in 10 minutes.</p>
            <p>If you did not request this, please ignore this email.</p>
        ";

        return send_notification_email($email, $subject, 'Your 2FA code is: ' . $otp, $message);
    }

    /**
     * Log suspicious activity
     */
    private static function logSuspiciousActivity($email, $alert_type, $description)
    {
        try {
            $stmt = self::$db->prepare("
                INSERT INTO suspicious_activity_alerts 
                (user_id, user_type, alert_type, description, ip_address, severity) 
                VALUES (
                    (SELECT id FROM super_admin WHERE email = ?),
                    'super_admin',
                    ?,
                    ?,
                    ?,
                    'medium'
                )
            ");
            $stmt->execute([$email, $alert_type, $description, $_SERVER['REMOTE_ADDR']]);
        } catch (Exception $e) {
            error_log("SuperAdminAuth::logSuspiciousActivity failed: " . $e->getMessage());
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated()
    {
        return isset($_SESSION['super_admin_token']) && self::$super_admin_id !== null;
    }

    /**
     * Get current super admin ID
     */
    public static function getId()
    {
        return self::$super_admin_id ?? $_SESSION['super_admin_id'] ?? null;
    }

    /**
     * Get current super admin email
     */
    public static function getEmail()
    {
        return $_SESSION['super_admin_email'] ?? null;
    }

    /**
     * Require authentication
     */
    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            header('Location: /super-admin/login');
            exit;
        }
    }

    /**
     * Logout
     */
    public static function logout()
    {
        self::ensureDb();
        if (isset($_SESSION['super_admin_token'])) {
            // Deactivate session token
            self::$db->prepare("
                UPDATE user_sessions 
                SET is_active = FALSE 
                WHERE session_token = ?
            ")->execute([$_SESSION['super_admin_token']]);
        }

        session_destroy();
        return true;
    }

    /**
     * Verify session is still valid
     */
    public static function validateSession()
    {
        if (!isset($_SESSION['super_admin_token'])) {
            return false;
        }

        $stmt = self::$db->prepare("
            SELECT id FROM user_sessions 
            WHERE session_token = ? 
            AND user_type = 'super_admin'
            AND is_active = TRUE
            AND expires_at > NOW()
        ");
        $stmt->execute([$_SESSION['super_admin_token']]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
