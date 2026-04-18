<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-journal-text me-2"></i>Digital Library</h4>
            </div>
            <?php if (empty($libraryResources)): ?>
                <div class="alert alert-info mb-0">No library resources have been uploaded yet.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Summary</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libraryResources as $item): ?>
                                <tr>
                                    <td><?= e((string)($item['title'] ?? '')) ?></td>
                                    <td><?= e((string)($item['summary'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($item['file_path'])): ?>
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e(base_url(ltrim((string)$item['file_path'], '/'))) ?>">Open</a>
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
