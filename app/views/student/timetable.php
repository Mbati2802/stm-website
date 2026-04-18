<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-calendar-week me-2"></i>Programme Timetables</h4>
            </div>
            <?php if (empty($timetables)): ?>
                <div class="alert alert-info mb-0">No timetable has been published yet.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Programme</th>
                                <th>Title</th>
                                <th>Details</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timetables as $row): ?>
                                <tr>
                                    <td><?= e((string)($row['programme_name'] ?? 'General')) ?></td>
                                    <td><?= e((string)($row['title'] ?? '')) ?></td>
                                    <td><?= e((string)($row['details'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($row['file_path'])): ?>
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e(base_url(ltrim((string)$row['file_path'], '/'))) ?>">Open</a>
                                        <?php else: ?>
                                            <span class="text-muted small">No file</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
