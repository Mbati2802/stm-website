<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <img src="/assets/images/logo.png" alt="College Logo" onerror="this.style.display='none'">
            <h3>CRM Login</h3>
            <p class="text-muted">St. Mary's MCH Medical Training College</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/crm/login">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <small class="text-muted">Default: admin / admin123</small>
        </div>
    </div>
</div>
