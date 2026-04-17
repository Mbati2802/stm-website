<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 640px;">
        <h1 class="portal-title mb-1">Reset Password</h1>
        <p class="portal-subtitle mb-3">Enter the reset code and choose a new password.</p>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/reset-password')) ?>">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Reset Code</label>
                <input name="code" class="form-control" required>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
            </div>
            <button class="btn btn-primary w-100 mt-4">Update Password</button>
        </form>
    </div>
</section>
