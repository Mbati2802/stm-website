<section class="section-stack">
    <div class="site-width boxed-section" style="max-width: 560px;">
        <h1 class="split-title mb-3"><span class="title-primary">Forgot</span> <span class="title-secondary">Password</span></h1>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(base_url('portal/forgot-password')) ?>" class="soft-card p-4 bg-white">
            <label class="form-label">Registered Email</label>
            <input type="email" name="email" class="form-control mb-3" required>
            <button class="btn btn-primary w-100">Send Reset Code</button>
        </form>
    </div>
</section>
