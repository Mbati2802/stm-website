<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-book me-2"></i>My Courses</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Semesters</option>
                        <option>Semester 1</option>
                        <option>Semester 2</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h5 class="course-title">Advanced Web Development</h5>
                                <div class="course-code">CS<?= 300 + $i ?> - Web Technologies</div>
                                <div class="course-instructor"><i class="bi bi-person me-1"></i>Dr. John Smith</div>
                            </div>
                            <span class="badge bg-<?= $i <= 2 ? 'success' : ($i <= 4 ? 'warning' : 'info') ?> rounded-pill">
                                <?= $i <= 2 ? 'Active' : ($i <= 4 ? 'In Progress' : 'Upcoming') ?>
                            </span>
                        </div>
                        <div class="course-progress">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="text-muted"><?= 60 + ($i * 5) ?>%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= 60 + ($i * 5) ?>%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                <i class="bi bi-clock me-1"></i><?= 3 + $i ?> hrs/week
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-action-view" title="View Details"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-action-edit" title="Materials"><i class="bi bi-folder"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Course Statistics -->
        <div class="student-stats-grid mt-4">
            <div class="student-stat-card">
                <div class="student-card-icon primary">
                    <i class="bi bi-book"></i>
                </div>
                <div class="student-stat-value">6</div>
                <div class="student-stat-label">Total Courses</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="student-stat-value">2</div>
                <div class="student-stat-label">Completed</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="student-stat-value">4</div>
                <div class="student-stat-label">In Progress</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="student-stat-value">18</div>
                <div class="student-stat-label">Credit Hours</div>
            </div>
        </div>
    </div>
</section>
