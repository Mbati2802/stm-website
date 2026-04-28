<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 560px;">
        <h1 class="portal-title mb-1">Staff Portal Login</h1>
        <p class="portal-subtitle mb-3">Access the staff management system.</p>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('staff/login')) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100 mb-3">Log In</button>
        </form>
    </div>
</section>