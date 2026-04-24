<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 640px;">
        <h1 class="portal-title mb-1">Create Student Account</h1>
        <p class="portal-subtitle mb-3">Only official college email addresses are allowed.</p>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/register')) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
                <small class="text-muted">Use your official college email ending with <strong>@stmarysmchmcollege.ac.ke</strong>.</small>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
            </div>
            <button class="btn btn-primary w-100 mt-4">Create Account</button>
            <p class="small text-muted mt-3 mb-2">
                After registration, the admissions office will assign your admission number. Use that admission number and your password to log in.
            </p>
            <p class="small mb-0 text-center">Already have an account? <a href="<?= e(base_url('portal/login')) ?>">Log in</a></p>
        </form>
    </div>
</section>
