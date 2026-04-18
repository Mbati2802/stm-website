<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 640px;">
        <h1 class="portal-title mb-1">Reset Password</h1>
        <p class="portal-subtitle mb-3">Enter the reset code sent to your email and choose a new password.</p>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/reset-password')) ?>">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter your registered email">
                <small class="text-muted">Use the same email you requested the reset code for</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Reset Code</label>
                <input name="code" class="form-control" required placeholder="Enter the 6-digit code from your email">
                <small class="text-muted">The code expires in 15 minutes</small>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6" placeholder="Min 6 characters">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Re-enter password">
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle me-2"></i>
                <small>If you don't receive the code, check your spam folder or <a href="<?= e(base_url('portal/forgot-password')) ?>">request a new code</a>.</small>
            </div>
            <button class="btn btn-primary w-100 mt-3"><i class="bi bi-shield-lock me-2"></i>Update Password</button>
        </form>
        <div class="text-center mt-3">
            <a href="<?= e(base_url('portal/login')) ?>" class="text-muted small">Back to Login</a>
        </div>
    </div>
</section>
