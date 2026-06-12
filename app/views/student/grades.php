<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-award me-2"></i>Grades & Results</h4>
            </div>
            <?php if (empty($gradeRows ?? [])): ?>
                <div class="alert alert-info mb-0">No grades have been published yet.</div>
            <?php else: ?>
                <div class="table-responsive admin-table-card">
                    <table class="table align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Unit Code</th>
                                <th>Unit Name</th>
                                <?php foreach (($examColumns ?? []) as $examColumn): ?>
                                    <th><?= e((string)$examColumn) ?></th>
                                <?php endforeach; ?>
                                <th>Grade</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($gradeRows ?? []) as $gradeRow): ?>
                                <tr>
                                    <td><?= e((string)($gradeRow['course_code'] ?? 'N/A')) ?></td>
                                    <td><?= e((string)($gradeRow['course_title'] ?? '')) ?></td>
                                    <?php foreach (($examColumns ?? []) as $examColumn): ?>
                                        <?php $examMark = $gradeRow['exam_marks'][$examColumn] ?? null; ?>
                                        <td><?= $examMark === null || $examMark === '' ? '-' : e((string)$examMark) ?></td>
                                    <?php endforeach; ?>
                                    <td>
                                        <?php if (!empty($gradeRow['grade'])): ?>
                                            <span class="badge bg-primary"><?= e((string)$gradeRow['grade']) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($gradeRow['comment']) ? e((string)$gradeRow['comment']) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
