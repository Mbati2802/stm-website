<section class="section-stack">
    <div class="site-width boxed-section" style="max-width: 640px;">
        <h1 class="split-title mb-3"><span class="title-primary">Reset</span> <span class="title-secondary">Password</span></h1>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/reset-password')) ?>" class="soft-card p-4 bg-white">
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
