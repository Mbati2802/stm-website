<section class="py-4">
    <div class="student-content-wrap">
        <!-- Welcome Header -->
        <div class="student-card">
            <div class="student-profile-header">
                <div class="student-profile-info">
                    <div class="student-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="student-details">
                        <h3>Welcome back, <?= e($_SESSION['student_name'] ?? 'Student') ?>!</h3>
                        <p><i class="bi bi-book me-2"></i><?= e($_SESSION['programme'] ?? 'Computer Science') ?> - Year <?= e($_SESSION['year'] ?? '2') ?></p>
                        <p><i class="bi bi-geo-alt me-2"></i>Student ID: <?= e($_SESSION['student_id'] ?? 'STM2024001') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="student-stats-grid">
            <div class="student-stat-card">
                <div class="student-card-icon primary">
                    <i class="bi bi-book"></i>
                </div>
                <div class="student-stat-value">6</div>
                <div class="student-stat-label">Active Courses</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="student-stat-value">3</div>
                <div class="student-stat-label">Pending Assignments</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-award"></i>
                </div>
                <div class="student-stat-value">78%</div>
                <div class="student-stat-label">Average Grade</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="student-stat-value">92%</div>
                <div class="student-stat-label">Attendance Rate</div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Courses -->
            <div class="col-lg-8">
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title">
                            <i class="bi bi-book me-2"></i>My Courses
                        </h4>
                        <a href="<?= e(base_url('student/courses')) ?>" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h5 class="course-title">Advanced Web Development</h5>
                                <div class="course-code">CS<?= 300 + $i ?> - Web Technologies</div>
                                <div class="course-instructor"><i class="bi bi-person me-1"></i>Dr. John Smith</div>
                            </div>
                            <span class="badge bg-<?= $i == 1 ? 'success' : ($i == 2 ? 'warning' : 'info') ?> rounded-pill">
                                <?= $i == 1 ? 'Active' : ($i == 2 ? 'In Progress' : 'Upcoming') ?>
                            </span>
                        </div>
                        <div class="course-progress">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="text-muted"><?= 60 + ($i * 10) ?>%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= 60 + ($i * 10) ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Upcoming Events & Assignments -->
            <div class="col-lg-4">
                <!-- Upcoming Assignments -->
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title">
                            <i class="bi bi-calendar-check me-2"></i>Upcoming Deadlines
                        </h4>
                    </div>
                    
                    <div class="assignment-card urgent">
                        <div class="assignment-header">
                            <div>
                                <h6 class="assignment-title">Database Design Project</h6>
                                <small class="text-muted">CS301 - Database Systems</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">2 days</span>
                        </div>
                        <p class="assignment-description">Design and implement a complete database system for a library management system.</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary">Start Assignment</button>
                            <button class="btn btn-sm btn-outline-secondary">View Details</button>
                        </div>
                    </div>

                    <div class="assignment-card">
                        <div class="assignment-header">
                            <div>
                                <h6 class="assignment-title">Algorithm Analysis Report</h6>
                                <small class="text-muted">CS302 - Algorithms</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">5 days</span>
                        </div>
                        <p class="assignment-description">Analyze time and space complexity of sorting algorithms.</p>
                    </div>

                    <div class="assignment-card completed">
                        <div class="assignment-header">
                            <div>
                                <h6 class="assignment-title">Web Development Quiz</h6>
                                <small class="text-muted">CS303 - Web Technologies</small>
                            </div>
                            <span class="badge bg-success rounded-pill">Completed</span>
                        </div>
                        <p class="assignment-description">HTML, CSS, and JavaScript fundamentals quiz.</p>
                    </div>
                </div>

                <!-- Recent Announcements -->
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title">
                            <i class="bi bi-megaphone me-2"></i>Announcements
                        </h4>
                    </div>
                    
                    <div class="alert-student alert-info">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>Schedule Update</strong>
                            <p class="mb-0 small">Tomorrow's CS301 lecture moved to Room 205</p>
                        </div>
                    </div>

                    <div class="alert-student alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <div>
                            <strong>Library Maintenance</strong>
                            <p class="mb-0 small">Digital library will be unavailable this weekend</p>
                        </div>
                    </div>

                    <div class="alert-student alert-success">
                        <i class="bi bi-trophy"></i>
                        <div>
                            <strong>Congratulations!</strong>
                            <p class="mb-0 small">You've been selected for the Dean's List</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h4>
            </div>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="<?= e(base_url('student/assignments')) ?>" class="btn btn-student btn-outline-primary w-100">
                        <i class="bi bi-file-earmark-text d-block mb-2" style="font-size: 1.5rem;"></i>
                        Assignments
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="<?= e(base_url('student/grades')) ?>" class="btn btn-student btn-outline-success w-100">
                        <i class="bi bi-award d-block mb-2" style="font-size: 1.5rem;"></i>
                        Grades
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="<?= e(base_url('student/library')) ?>" class="btn btn-student btn-outline-info w-100">
                        <i class="bi bi-journal-text d-block mb-2" style="font-size: 1.5rem;"></i>
                        Library
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="<?= e(base_url('student/fees')) ?>" class="btn btn-student btn-outline-warning w-100">
                        <i class="bi bi-credit-card d-block mb-2" style="font-size: 1.5rem;"></i>
                        Fee Statement
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
