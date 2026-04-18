<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-journal-text me-2"></i>Digital Library</h4>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search resources..." style="width: 200px;">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Categories</option>
                        <option>Textbooks</option>
                        <option>Research Papers</option>
                        <option>Video Lectures</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h5 class="course-title">Database System Concepts</h5>
                                <div class="course-code">By Abraham Silberschatz</div>
                                <div class="course-instructor"><i class="bi bi-book me-1"></i>Textbook</div>
                            </div>
                            <span class="badge bg-success rounded-pill">Available</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                <i class="bi bi-download me-1"></i><?= 120 + $i * 15 ?> downloads
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-action-view" title="Preview"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-action-edit" title="Download"><i class="bi bi-download"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Library Statistics -->
        <div class="student-stats-grid mt-4">
            <div class="student-stat-card">
                <div class="student-card-icon primary">
                    <i class="bi bi-book"></i>
                </div>
                <div class="student-stat-value">1,250</div>
                <div class="student-stat-label">Total Resources</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-file-earmark-pdf"></i>
                </div>
                <div class="student-stat-value">850</div>
                <div class="student-stat-label">PDF Documents</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-play-circle"></i>
                </div>
                <div class="student-stat-value">320</div>
                <div class="student-stat-label">Video Lectures</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-download"></i>
                </div>
                <div class="student-stat-value">80</div>
                <div class="student-stat-label">Your Downloads</div>
            </div>
        </div>
    </div>
</section>
