<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-folder me-2"></i>Study Materials</h4>
            </div>
            <?php if (empty($materials)): ?>
                <div class="alert alert-info mb-0">No study materials are available right now.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Title</th>
                                <th>Summary</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td><?= e((string)($material['course_code'] ?? 'N/A')) ?> - <?= e((string)($material['course_title'] ?? '')) ?></td>
                                    <td><?= e((string)($material['title'] ?? '')) ?></td>
                                    <td><?= e((string)($material['summary'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($material['file_path'])): ?>
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e(base_url(ltrim((string)$material['file_path'], '/'))) ?>">Open</a>
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
