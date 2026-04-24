<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 560px;">
        <h1 class="portal-title mb-1">Forgot Password</h1>
        <p class="portal-subtitle mb-3">A verification code will be sent to your student email.</p>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/forgot-password')) ?>">
            <?= csrf_field() ?>
            <label class="form-label">Registered Email</label>
            <input type="email" name="email" class="form-control mb-3" required>
            <button class="btn btn-primary w-100">Send Reset Code</button>
        </form>
    </div>
</section>
