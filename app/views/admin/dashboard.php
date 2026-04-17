<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Dashboard Overview</h1>
                <p class="text-muted mb-0">Manage content, students, enquiries, and settings from one place.</p>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <?php foreach ($stats as $label => $val): ?>
                <div class="col-6 col-lg-3">
                    <div class="soft-card admin-metric-card h-100">
                        <p class="text-muted small mb-1"><?= e($label) ?></p>
                        <div class="metric-value"><?= (int)$val ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="soft-card p-3">
            <h2 class="h6 text-uppercase text-muted mb-3">Quick Actions</h2>
            <div class="admin-grid-actions">
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/programmes')) ?>"><i class="bi bi-journal-text"></i>Programmes</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/events')) ?>"><i class="bi bi-calendar-event"></i>Events</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/event-registrations')) ?>"><i class="bi bi-calendar2-week"></i>Event Registrations</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/media')) ?>"><i class="bi bi-folder2-open"></i>Media Library</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/news')) ?>"><i class="bi bi-newspaper"></i>News</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/gallery')) ?>"><i class="bi bi-images"></i>Gallery</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-envelope"></i>Messages</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/students')) ?>"><i class="bi bi-people"></i>Students</a>
                <a class="btn btn-primary" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-sliders"></i>Settings</a>
            </div>
        </div>
    </div>
</section>
