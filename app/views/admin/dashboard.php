<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Dashboard Overview</h1>
                <p class="text-muted mb-0">Real-time operations snapshot for content, users, and engagement.</p>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <?php
            // Use role-specific stats if available, otherwise use general stats
            $displayStats = $teacherStats ?? $registrarStats ?? $viewerStats ?? $stats;
            $faintColors = ['bg-primary bg-opacity-10', 'bg-success bg-opacity-10', 'bg-info bg-opacity-10', 'bg-warning bg-opacity-10', 'bg-danger bg-opacity-10', 'bg-secondary bg-opacity-10'];
            $colorIndex = 0;
            foreach ($displayStats as $label => $val): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="soft-card admin-metric-card h-100 <?= $faintColors[$colorIndex % count($faintColors)] ?>">
                        <p class="text-muted small mb-1"><?= e($label) ?></p>
                        <div class="metric-value" style="font-size:1.4rem"><?= (int)$val ?></div>
                    </div>
                </div>
                <?php $colorIndex++; ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($engagementBreakdown) && (Auth::isSuperAdmin() || Auth::isJuniorAdmin() || Auth::isEditor())): ?>
        <div class="row g-3 mb-3">
            <?php if (!empty($engagementBreakdown)): ?>
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Engagement Mix</h2>
                    <canvas id="engagementBar" height="200"></canvas>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($applicationTrend)): ?>
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Applications Trend</h2>
                    <canvas id="applicationsTrend" height="200"></canvas>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($trafficTrend)): ?>
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Website Traffic Trend</h2>
                    <canvas id="trafficTrend" height="200"></canvas>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($recentMessages) && (Auth::isSuperAdmin() || Auth::isJuniorAdmin() || Auth::isEditor())): ?>
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 text-uppercase text-primary mb-0">Recent Public Messages</h2>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/messages')) ?>">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-md">Name</th><th class="col-md">Subject</th><th class="col-lg">Message</th><th class="col-sm">Date</th></tr></thead>
                            <tbody>
                            <?php foreach (array_slice($recentMessages, 0, 3) as $row): ?>
                                <tr>
                                    <td title="<?= e((string)($row['name'] ?? '')) ?>"><?= e(substr((string)($row['name'] ?? ''), 0, 15)) ?></td>
                                    <td title="<?= e((string)($row['subject'] ?? '')) ?>"><?= e(substr((string)($row['subject'] ?? ''), 0, 15)) ?></td>
                                    <td title="<?= e((string)($row['message'] ?? '')) ?>"><?= e(substr((string)($row['message'] ?? ''), 0, 25)) ?></td>
                                    <td><?= e((string)($row['created_at'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php if (!empty($recentEvents)): ?>
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 text-uppercase text-primary mb-0">Recent Events</h2>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/list/events')) ?>">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-md">Title</th><th class="col-sm">Category</th><th class="col-sm">Status</th><th class="col-sm">Date</th></tr></thead>
                            <tbody>
                            <?php foreach (array_slice($recentEvents, 0, 3) as $row): ?>
                                <tr>
                                    <td title="<?= e((string)($row['title'] ?? '')) ?>"><?= e(substr((string)($row['title'] ?? ''), 0, 20)) ?></td>
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
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($topPages) && (Auth::isSuperAdmin() || Auth::isJuniorAdmin() || Auth::isEditor())): ?>
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Top Performing Pages</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-lg">Path</th><th class="col-sm">Visits</th></tr></thead>
                            <tbody>
                                <?php if (empty($topPages)): ?>
                                    <tr><td colspan="2" class="text-muted">No page traffic recorded yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($topPages as $row): ?>
                                        <tr>
                                            <td title="<?= e((string)($row['path'] ?? '')) ?>"><?= e((string)($row['path'] ?? '')) ?></td>
                                            <td><?= (int)($row['total'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php if (!empty($topCourses)): ?>
            <div class="col-lg-6">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Top Courses by Views</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-lg">Course</th><th class="col-sm">Views</th></tr></thead>
                            <tbody>
                                <?php if (empty($topCourses)): ?>
                                    <tr><td colspan="2" class="text-muted">No course views recorded yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($topCourses as $row): ?>
                                        <tr>
                                            <td title="<?= e((string)($row['title'] ?? '')) ?>"><?= e((string)($row['title'] ?? '')) ?></td>
                                            <td><?= (int)($row['total'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($recentBlockedLogins) && (Auth::isSuperAdmin() || Auth::isJuniorAdmin() || Auth::isEditor())): ?>
        <div class="row g-3 mb-3">
            <div class="col-lg-12">
                <div class="soft-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 text-uppercase text-primary mb-0">Recent Blocked Login Attempts</h2>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/login-trace')) ?>">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead><tr><th class="col-sm">Type</th><th class="col-md">IP Address</th><th class="col-lg">User Agent</th><th class="col-sm">Date</th></tr></thead>
                            <tbody>
                                <?php if (empty($recentBlockedLogins)): ?>
                                    <tr><td colspan="4" class="text-muted">No blocked login attempts recorded yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($recentBlockedLogins, 0, 5) as $row): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $path = (string)($row['path'] ?? '');
                                                if ($path === '/admin/login-rate-limited') {
                                                    echo '<span class="badge bg-warning">Rate Limited</span>';
                                                } elseif ($path === '/admin/login-failed') {
                                                    echo '<span class="badge bg-danger">Failed Login</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                                }
                                                ?>
                                            </td>
                                            <td title="<?= e((string)($row['ip_address'] ?? '')) ?>"><?= e(substr((string)($row['ip_address'] ?? ''), 0, 15)) ?></td>
                                            <td title="<?= e((string)($row['user_agent'] ?? '')) ?>"><?= e(substr((string)($row['user_agent'] ?? ''), 0, 30)) ?></td>
                                            <td><?= e((string)($row['created_at'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="soft-card p-3">
            <h2 class="h6 text-uppercase text-primary mb-3">Quick Actions</h2>
            <div class="admin-grid-actions">
                <?php if (Auth::canViewEntity('programmes')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/programmes')) ?>"><i class="bi bi-journal-text"></i>Programmes</a>
                <?php endif; ?>
                <?php if (Auth::canViewEntity('events')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/list/events')) ?>"><i class="bi bi-calendar-event"></i>Events</a>
                <?php endif; ?>
                <?php if (Auth::canManageEntity('events')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/event-registrations')) ?>"><i class="bi bi-calendar2-week"></i>Event Registrations</a>
                <?php endif; ?>
                <?php if (Auth::canViewEntity('media')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/media')) ?>"><i class="bi bi-folder2-open"></i>Media Library</a>
                <?php endif; ?>
                <?php if (Auth::canViewEntity('messages')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-envelope"></i>Messages</a>
                <?php endif; ?>
                <?php if (Auth::canManageEntity('messages')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/applications')) ?>"><i class="bi bi-ui-checks-grid"></i>Applications</a>
                <?php endif; ?>
                <a class="btn btn-outline-primary" href="<?= e(base_url('admin/internal-messages')) ?>"><i class="bi bi-chat-left-dots"></i>Team Messages</a>
                <?php if (Auth::canViewEntity('students')): ?>
                    <a class="btn btn-outline-primary" href="<?= e(base_url('admin/students')) ?>"><i class="bi bi-people"></i>Students</a>
                <?php endif; ?>
                <?php if (!Auth::isTeacher()): ?>
                    <a class="btn btn-primary" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-sliders"></i>Settings</a>
                <?php endif; ?>
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
    const createBar = function (id, labels, values, colors) {
        const el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') return;
        new Chart(el, {
            type: 'bar',
            data: { labels: labels, datasets: [{ data: values, backgroundColor: colors }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    };
    <?php if (!empty($engagementBreakdown)): ?>
    createBar('engagementBar', <?= json_encode(array_keys($engagementBreakdown)) ?>, <?= json_encode(array_values($engagementBreakdown)) ?>, ['#ef476f','#ffd166','#06d6a0']);
    <?php endif; ?>
    const createLine = function (id, raw, label, color) {
        const el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: raw.map((r) => r.day || ''),
                datasets: [{
                    label: label,
                    data: raw.map((r) => Number(r.total || 0)),
                    borderColor: color,
                    backgroundColor: 'rgba(24,84,144,.12)',
                    fill: true,
                    tension: 0.25
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    };
    <?php if (!empty($applicationTrend)): ?>
    createLine('applicationsTrend', <?= json_encode($applicationTrend) ?>, 'Applications', '#185490');
    <?php endif; ?>
    <?php if (!empty($trafficTrend)): ?>
    createLine('trafficTrend', <?= json_encode($trafficTrend) ?>, 'Visits', '#0aaae8');
    <?php endif; ?>
});
</script>
