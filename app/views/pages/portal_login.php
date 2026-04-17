<section class="section-stack">
    <div class="site-width boxed-section" style="max-width: 560px;">
        <h1 class="split-title mb-3"><span class="title-primary">Student</span> <span class="title-secondary">Portal Login</span></h1>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/login')) ?>" class="soft-card p-4 bg-white">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100 mb-3">Log In</button>
            <div class="d-flex justify-content-between small">
                <a href="<?= e(base_url('portal/register')) ?>">Create account</a>
                <a href="<?= e(base_url('portal/forgot-password')) ?>">Forgot password?</a>
            </div>
        </form>
    </div>
</section>
