<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Programme Applications</h1>
                <p class="text-muted mb-0">Track all received applications with trend insights.</p>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-4">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Applications Trend (30 days)</h2>
                    <canvas id="applicationsTrendChart" height="230"></canvas>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="soft-card p-3 h-100">
                    <h2 class="h6 text-uppercase text-primary mb-3">Recent Applications</h2>
                    <div class="table-responsive">
                        <table class="table table-sm admin-table mb-0">
                            <thead>
                                <tr>
                                    <th class="col-md">Applicant</th>
                                    <th class="col-md">Contact</th>
                                    <th class="col-md">Course</th>
                                    <th class="col-sm">Level</th>
                                    <th class="col-sm">Intake</th>
                                    <th class="col-sm">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr><td colspan="6" class="text-muted">No applications found yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td title="<?= e((string)($row['name'] ?? '')) ?>"><?= e((string)($row['name'] ?? '')) ?></td>
                                            <td title="<?= e((string)($row['email'] ?? '')) ?>"><?= e((string)($row['email'] ?? '')) ?> / <?= e((string)($row['phone'] ?? '')) ?></td>
                                            <td title="<?= e((string)($row['course_selection'] ?? '')) ?>"><?= e((string)($row['course_selection'] ?? '')) ?></td>
                                            <td><?= e((string)($row['level'] ?? '')) ?></td>
                                            <td><?= e((string)($row['preferred_intake'] ?? '')) ?></td>
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
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartEl = document.getElementById('applicationsTrendChart');
    if (!chartEl || typeof Chart === 'undefined') return;
    const raw = <?= json_encode($trend) ?> || [];
    const labels = raw.map((r) => r.day || '');
    const values = raw.map((r) => Number(r.total || 0));
    new Chart(chartEl, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Applications',
                data: values,
                borderColor: '#185490',
                backgroundColor: 'rgba(24,84,144,.15)',
                fill: true,
                tension: 0.25
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
