<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-book me-2"></i>My Units</h4>
            </div>
            <?php if (empty($courses)): ?>
                <div class="alert alert-info mb-0">No units are currently available. Please check with your administrator.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Unit</th>
                                <th>Programme</th>
                                <th>Teacher</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= e((string)($course['code'] ?? 'N/A')) ?></td>
                                    <td><?= e((string)($course['title'] ?? '')) ?></td>
                                    <td><?= e((string)($course['programme_name'] ?? 'General')) ?></td>
                                    <td><?= e((string)($course['teacher_name'] ?? 'Unassigned')) ?></td>
                                    <td><?= e((string)($course['description'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
