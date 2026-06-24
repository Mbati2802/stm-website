<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .logo-section p {
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert.alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .alert.alert-info {
            background: #eef;
            color: #33c;
            border: 1px solid #ccf;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group.two-fa {
            display: none;
        }

        .form-group.two-fa.active {
            display: block;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #ddd;
            z-index: -1;
        }

        .step.active:not(:last-child)::after {
            background: #667eea;
        }

        .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 50%;
            line-height: 40px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #999;
            transition: all 0.3s;
        }

        .step.active .step-number {
            background: #667eea;
            color: white;
        }

        .step-label {
            font-size: 12px;
            color: #999;
        }

        .step.active .step-label {
            color: #667eea;
            font-weight: 600;
        }

        .security-info {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        .security-info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <h1>🔐 Super Admin</h1>
                <p>Administrative Control Center</p>
            </div>

            <div id="alertContainer"></div>

            <div class="step-indicator">
                <div class="step active" id="step1">
                    <div class="step-number">1</div>
                    <div class="step-label">Login</div>
                </div>
                <div class="step" id="step2">
                    <div class="step-number">2</div>
                    <div class="step-label">2FA</div>
                </div>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="super.admin@stmarysmchmcollege.ac.ke" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-group two-fa" id="otpGroup">
                    <label for="otp">2FA Code</label>
                    <input type="text" id="otp" name="otp" placeholder="000000" maxlength="6" pattern="[0-9]{6}">
                    <small style="display: block; margin-top: 5px; color: #999;">Enter the 6-digit code sent to your email</small>
                </div>

                <button type="submit" id="submitBtn">
                    <span>Sign In</span>
                </button>
            </form>

            <div class="security-info">
                <strong>🛡️ Security Reminder:</strong><br>
                • This is a restricted administrative area<br>
                • All activities are logged and monitored<br>
                • Unauthorized access is prohibited by law
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const otpGroup = document.getElementById('otpGroup');
        const otpInput = document.getElementById('otp');
        const submitBtn = document.getElementById('submitBtn');
        const alertContainer = document.getElementById('alertContainer');
        let requires2FA = false;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (requires2FA) {
                // Verify 2FA
                await verify2FA();
            } else {
                // Initial login
                await attemptLogin();
            }
        });

        async function attemptLogin() {
            const email = emailInput.value.trim();
            const password = passwordInput.value;

            if (!email || !password) {
                showAlert('Please enter both email and password', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span>Logging in...';

            try {
                const response = await fetch('/super-admin/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                });

                const data = await response.json();

                if (data.success) {
                    if (data.requires_2fa) {
                        // Show 2FA step
                        requires2FA = true;
                        otpGroup.classList.add('active');
                        document.getElementById('step1').classList.remove('active');
                        document.getElementById('step2').classList.add('active');
                        showAlert('2FA code sent to your email', 'info');
                        submitBtn.innerHTML = '<span>Verify 2FA</span>';
                        otpInput.focus();
                    } else {
                        // Login successful
                        showAlert('Login successful! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = '/super-admin/dashboard';
                        }, 1500);
                    }
                } else {
                    showAlert(data.message || 'Login failed', 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
                console.error(error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Sign In</span>';
            }
        }

        async function verify2FA() {
            const otp = otpInput.value.trim();

            if (!otp || otp.length !== 6) {
                showAlert('Please enter a valid 6-digit code', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span>Verifying...';

            try {
                const response = await fetch('/super-admin/verify-2fa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `otp=${encodeURIComponent(otp)}`
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('2FA verified! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '/super-admin/dashboard';
                    }, 1500);
                } else {
                    showAlert(data.message || '2FA verification failed', 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
                console.error(error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Verify 2FA</span>';
            }
        }

        function showAlert(message, type) {
            alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            alertContainer.scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
