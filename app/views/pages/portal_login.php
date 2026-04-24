<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 560px;">
        <h1 class="portal-title mb-1">Student Portal Login</h1>
        <p class="portal-subtitle mb-3">Use your admission number and password.</p>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/login')) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Admission Number</label>
                <input name="admission_number" class="form-control" required>
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
