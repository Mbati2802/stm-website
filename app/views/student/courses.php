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
                    <table class="table align-middle admin-table student-mobile-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Unit</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td data-label="Code"><strong><?= e((string)($course['code'] ?? 'N/A')) ?></strong></td>
                                    <td data-label="Unit"><?= e((string)($course['title'] ?? '')) ?></td>
                                    <td data-label="Teacher"><i class="bi bi-person me-1 text-muted"></i><?= e((string)($course['teacher_name'] ?? 'Unassigned')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
