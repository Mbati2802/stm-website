<section>
    <div class="portal-card p-4 p-md-5 mx-auto" style="max-width: 640px;">
        <h1 class="portal-title mb-1">Staff Portal Dashboard</h1>
        <p class="portal-subtitle mb-3">You are logged in through the staff portal. Use the button below to continue to the administration area, or log out to return to the staff login page.</p>
        <div class="d-grid gap-3">
            <a class="btn btn-primary" href="<?= e(base_url('admin')) ?>">Open Administration Panel</a>
            <a class="btn btn-outline-primary" href="<?= e(base_url('staff/logout')) ?>">Log Out</a>
        </div>
    </div>
</section>
