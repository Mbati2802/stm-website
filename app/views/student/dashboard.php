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
                        <h3>Welcome back, <?= e($student['name'] ?? 'Student') ?>!</h3>
                        <p><i class="bi bi-book me-2"></i><?= e($programmeName ?? 'Not assigned') ?></p>
                        <p><i class="bi bi-geo-alt me-2"></i>Admission Number: <?= e($student['admission_number'] ?? 'Not assigned') ?></p>
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
                <div class="student-stat-value"><?= count($courses ?? []) ?></div>
                <div class="student-stat-label">Available Courses</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="student-stat-value"><?= count($assignments ?? []) ?></div>
                <div class="student-stat-label">Assignments</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-megaphone"></i>
                </div>
                <div class="student-stat-value"><?= count($announcements ?? []) ?></div>
                <div class="student-stat-label">Announcements</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="student-stat-value"><?= count($timetables ?? []) ?></div>
                <div class="student-stat-label">Timetables</div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Units -->
            <div class="col-lg-8">
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title">
                            <i class="bi bi-book me-2"></i>My Units
                        </h4>
                        <a href="<?= e(base_url('student/courses')) ?>" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <?php if (!empty($courses)): ?>
                        <?php foreach (array_slice($courses, 0, 3) as $course): ?>
                        <div class="unit-card">
                            <div class="unit-header">
                                <div>
                                    <h5 class="unit-title"><?= e($course['title'] ?? 'Course') ?></h5>
                                    <div class="unit-code"><?= e($course['code'] ?? 'CODE') ?></div>
                                    <div class="unit-instructor"><i class="bi bi-person me-1"></i><?= e($course['teacher_name'] ?? 'Not assigned') ?></div>
                                </div>
                                <span class="badge bg-primary rounded-pill">Active</span>
                            </div>
                            <div class="unit-progress">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Progress</small>
                                    <small class="text-muted">Ongoing</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 50%"></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No courses available yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Events & Assignments -->
            <div class="col-lg-4">
                <!-- Upcoming Assignments -->
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title">
                            <i class="bi bi-calendar-check me-2"></i>Assignments
                        </h4>
                    </div>
                    
                    <?php if (!empty($assignments)): ?>
                        <?php foreach (array_slice($assignments, 0, 3) as $assignment): ?>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <div>
                                    <h6 class="assignment-title"><?= e($assignment['title'] ?? 'Assignment') ?></h6>
                                    <small class="text-muted"><?= e($assignment['course_title'] ?? 'Course') ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Active</span>
                            </div>
                            <p class="assignment-description"><?= e(substr((string)($assignment['instructions'] ?? ''), 0, 100)) ?>...</p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No assignments available yet.</p>
                    <?php endif; ?>
                </div>

                <!-- Recent Announcements -->
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title">
                            <i class="bi bi-megaphone me-2"></i>Announcements
                        </h4>
                    </div>
                    
                    <?php if (!empty($announcements ?? [])): ?>
                        <?php foreach (array_slice($announcements, 0, 3) as $announce): ?>
                            <div class="alert-student alert-info">
                                <i class="bi bi-info-circle"></i>
                                <div>
                                    <strong><?= e((string)($announce['title'] ?? 'Announcement')) ?></strong>
                                    <p class="mb-0 small"><?= e(plain_text((string)($announce['body'] ?? ''))) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-light mb-0">No announcements right now.</div>
                    <?php endif; ?>
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
