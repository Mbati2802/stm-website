<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-file-earmark-text me-2"></i>Assignments</h4>
            </div>
            <?php if (empty($assignments)): ?>
                <div class="alert alert-info mb-0">No assignments have been posted yet.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Assignment</th>
                                <th>Due Date</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?= e((string)($assignment['course_code'] ?? 'N/A')) ?> - <?= e((string)($assignment['course_title'] ?? '')) ?></td>
                                    <td><?= e((string)($assignment['title'] ?? '')) ?><br><small class="text-muted"><?= e((string)($assignment['instructions'] ?? '')) ?></small></td>
                                    <td><?= e((string)($assignment['due_at'] ?? 'Not set')) ?></td>
                                    <td>
                                        <?php if (!empty($assignment['file_path'])): ?>
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e(base_url(ltrim((string)$assignment['file_path'], '/'))) ?>">Open</a>
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
