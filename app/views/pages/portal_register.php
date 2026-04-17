<section class="section-stack">
    <div class="site-width boxed-section" style="max-width: 640px;">
        <h1 class="split-title mb-3"><span class="title-primary">Create</span> <span class="title-secondary">Student Account</span></h1>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/register')) ?>" class="soft-card p-4 bg-white">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
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
            <p class="small mb-0 mt-3 text-center">Already have an account? <a href="<?= e(base_url('portal/login')) ?>">Log in</a></p>
        </form>
    </div>
</section>
