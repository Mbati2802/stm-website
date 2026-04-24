<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Dashboard Overview</h1>
                <p class="text-muted mb-0">Real-time operations snapshot for content, users, and engagement.</p>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <?php foreach ($stats as $label => $val): ?>
                <div class="col-6 col-lg-2">
                    <div class="soft-card admin-metric-card h-100">
                        <p class="text-muted small mb-1"><?= e($label) ?></p>
                        <div class="metric-value"><?= (int)$val ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Content Distribution</h2>
                    <canvas id="contentPie" height="220"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Engagement Mix</h2>
                    <canvas id="engagementPie" height="220"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Team Roles</h2>
                    <canvas id="rolesPie" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Recent Public Messages</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-md">Name</th><th class="col-md">Subject</th><th class="col-lg">Message</th><th class="col-sm">Date</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentMessages as $row): ?>
                                <tr>
                                    <td title="<?= e((string)($row['name'] ?? '')) ?>"><?= e((string)($row['name'] ?? '')) ?></td>
                                    <td title="<?= e((string)($row['subject'] ?? '')) ?>"><?= e((string)($row['subject'] ?? '')) ?></td>
                                    <td title="<?= e((string)($row['message'] ?? '')) ?>"><?= e((string)($row['message'] ?? '')) ?></td>
                                    <td><?= e((string)($row['created_at'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Recent Events</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-md">Title</th><th class="col-sm">Category</th><th class="col-sm">Status</th><th class="col-sm">Date</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentEvents as $row): ?>
                                <tr>
                                    <td title="<?= e((string)($row['title'] ?? '')) ?>"><?= e((string)($row['title'] ?? '')) ?></td>
                                    <td><?= e((string)($row['category'] ?? '')) ?></td>
                                    <td><?= e((string)($row['registration_status'] ?? 'Open')) ?></td>
                                    <td><?= e((string)($row['starts_at'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="soft-card p-3">
            <h2 class="h6 text-uppercase text-primary mb-3">Quick Actions</h2>
            <div class="admin-grid-actions">
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/programmes')) ?>"><i class="bi bi-journal-text"></i>Programmes</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/events')) ?>"><i class="bi bi-calendar-event"></i>Events</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/event-registrations')) ?>"><i class="bi bi-calendar2-week"></i>Event Registrations</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/media')) ?>"><i class="bi bi-folder2-open"></i>Media Library</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-envelope"></i>Messages</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/internal-messages')) ?>"><i class="bi bi-chat-left-dots"></i>Team Messages</a>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/students')) ?>"><i class="bi bi-people"></i>Students</a>
                <a class="btn btn-primary" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-sliders"></i>Settings</a>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const createPie = function (id, labels, values, colors) {
        const el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') return;
        new Chart(el, {
            type: 'pie',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: colors }] },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    };
    createPie('contentPie', <?= json_encode(array_keys($contentBreakdown)) ?>, <?= json_encode(array_values($contentBreakdown)) ?>, ['#1e6fb7','#3598db','#6fc2ff','#9f8df2','#f2a65a','#7ccba2']);
    createPie('engagementPie', <?= json_encode(array_keys($engagementBreakdown)) ?>, <?= json_encode(array_values($engagementBreakdown)) ?>, ['#ef476f','#ffd166','#06d6a0']);
    createPie('rolesPie', <?= json_encode(array_keys($roleCounts)) ?>, <?= json_encode(array_values($roleCounts)) ?>, ['#185490','#b45f06','#2a9d8f']);
});
</script>
