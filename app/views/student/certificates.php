<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-patch-check me-2"></i>Certificates & Documents</h4>
            </div>
            
            <div class="row g-3">
                <?php 
                $certificates = [
                    ['Student ID Card', 'Official college identification', 'bi-card-heading', true],
                    ['Academic Transcript', 'Complete academic record', 'bi-file-earmark-text', true],
                    ['Unit Completion Certificate', 'Semester 1 completion', 'bi-award', true],
                    ['Internship Certificate', 'Summer internship 2023', 'bi-briefcase', true],
                    ['Attendance Certificate', 'Excellent attendance record', 'bi-calendar-check', false],
                    ['Scholarship Certificate', 'Merit-based scholarship', 'bi-trophy', false],
                ];
                foreach ($certificates as $cert): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="unit-card">
                        <div class="unit-header">
                            <div>
                                <h5 class="unit-title"><?= $cert[0] ?></h5>
                                <div class="unit-code"><?= $cert[1] ?></div>
                            </div>
                            <?php if ($cert[3]): ?>
                                <span class="badge bg-success rounded-pill">Available</span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill">Request</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                <i class="bi <?= $cert[2] ?> me-1"></i>Document
                            </div>
                            <div class="action-buttons">
                                <?php if ($cert[3]): ?>
                                    <button class="btn btn-sm btn-action-view" title="Download"><i class="bi bi-download"></i></button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary">Request</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
