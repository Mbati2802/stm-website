<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-award me-2"></i>Grades & Results</h4>
            </div>
            <?php if (empty($grades)): ?>
                <div class="alert alert-info mb-0">No grades have been published yet.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Unit Code</th>
                                <th>Unit Name</th>
                                <th>Marks</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?= e((string)($grade['course_code'] ?? 'N/A')) ?></td>
                                    <td><?= e((string)($grade['course_title'] ?? '')) ?></td>
                                    <td><?= e((string)($grade['marks'] ?? '')) ?></td>
                                    <td><span class="badge bg-primary"><?= e((string)($grade['grade'] ?? '')) ?></span></td>
                                    <td><?= e((string)($grade['remarks'] ?? '')) ?></td>
                                    <td><?= e((string)($grade['created_at'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
