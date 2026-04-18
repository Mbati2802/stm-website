<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-folder me-2"></i>Study Materials</h4>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search materials..." style="width: 200px;">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Courses</option>
                        <option>CS301 - Database</option>
                        <option>CS302 - Algorithms</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3">
                <?php for ($i = 1; $i <= 8; $i++): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h5 class="course-title">Chapter <?= $i ?>: <?= ['Introduction to Databases', 'Relational Model', 'SQL Basics', 'Database Design', 'Normalization', 'Transactions', 'Indexing', 'Query Optimization'][$i-1] ?></h5>
                                <div class="course-code">CS301 - Database Systems</div>
                                <div class="course-instructor">
                                    <i class="bi bi-<?= ['file-earmark-pdf', 'play-circle', 'file-earmark-text', 'camera-video'][$i % 4] ?> me-1"></i>
                                    <?= ['PDF', 'Video', 'Notes', 'Recording'][$i % 4] ?>
                                </div>
                            </div>
                            <span class="badge bg-<?= $i % 2 === 0 ? 'success' : 'primary' ?> rounded-pill"><?= $i % 2 === 0 ? 'New' : 'Updated' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                <i class="bi bi-clock me-1"></i><?= 15 + $i * 5 ?> min
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-action-view" title="View"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-action-edit" title="Download"><i class="bi bi-download"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>
