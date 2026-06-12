<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between gap-3 w-100">
                    <h4 class="student-card-title mb-0"><i class="bi bi-award me-2"></i>Grades & Results</h4>
                    <?php if (!empty($gradeRows ?? [])): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= e(base_url('portal/transcript')) ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Preview Transcript
                            </a>
                            <a href="<?= e(base_url('portal/transcript?download=1')) ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-download me-1"></i>Download PDF
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
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
                                    <?php $examLabel = is_array($examColumn) ? (string)($examColumn['label'] ?? '') : (string)$examColumn; ?>
                                    <th><?= e($examLabel) ?></th>
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
                                        <?php $examKey = is_array($examColumn) ? (int)($examColumn['id'] ?? 0) : (string)$examColumn; ?>
                                        <?php $examMark = $gradeRow['exam_marks'][$examKey] ?? null; ?>
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
