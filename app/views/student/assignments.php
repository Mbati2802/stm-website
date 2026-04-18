<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-file-earmark-text me-2"></i>Assignments</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Submitted</option>
                        <option>Graded</option>
                    </select>
                </div>
            </div>
            
            <?php 
            $assignments = [
                ['Database Design Project', 'CS301', 'Due in 2 days', 'urgent', 85],
                ['Algorithm Analysis Report', 'CS302', 'Due in 5 days', 'normal', 0],
                ['Web Development Quiz', 'CS303', 'Completed', 'completed', 92],
                ['Software Engineering Case Study', 'CS304', 'Due in 1 week', 'normal', 0],
                ['Network Configuration Lab', 'CS305', 'Due in 3 days', 'urgent', 0],
                ['OS Process Simulation', 'CS306', 'Graded', 'graded', 88],
            ];
            foreach ($assignments as $assignment): ?>
            <div class="assignment-card <?= $assignment[3] ?>">
                <div class="assignment-header">
                    <div>
                        <h6 class="assignment-title"><?= $assignment[0] ?></h6>
                        <small class="text-muted"><?= $assignment[1] ?></small>
                    </div>
                    <?php if ($assignment[3] === 'urgent'): ?>
                        <span class="badge bg-danger rounded-pill"><?= $assignment[2] ?></span>
                    <?php elseif ($assignment[3] === 'normal'): ?>
                        <span class="badge bg-warning rounded-pill"><?= $assignment[2] ?></span>
                    <?php elseif ($assignment[3] === 'completed'): ?>
                        <span class="badge bg-success rounded-pill">Submitted</span>
                    <?php else: ?>
                        <span class="badge bg-primary rounded-pill">Grade: <?= $assignment[4] ?>%</span>
                    <?php endif; ?>
                </div>
                <p class="assignment-description">
                    <?= $assignment[3] === 'urgent' ? 'Design and implement a complete database system for a library management system.' : 
                       ($assignment[3] === 'normal' ? 'Analyze time and space complexity of sorting algorithms.' : 
                       ($assignment[3] === 'completed' ? 'HTML, CSS, and JavaScript fundamentals quiz.' : 'Process simulation using C programming.')) ?>
                </p>
                <?php if ($assignment[3] === 'urgent' || $assignment[3] === 'normal'): ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-primary">Start Assignment</button>
                    <button class="btn btn-sm btn-outline-secondary">View Details</button>
                </div>
                <?php elseif ($assignment[3] === 'graded'): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="bi bi-check-circle me-1 text-success"></i>Submitted on Jan 10, 2024
                    </div>
                    <button class="btn btn-sm btn-action-view" title="View Feedback"><i class="bi bi-chat-dots"></i></button>
                </div>
                <?php else: ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="bi bi-check-circle me-1 text-success"></i>Submitted on Jan 8, 2024
                    </div>
                    <div class="small text-primary">
                        <i class="bi bi-clock me-1"></i>Awaiting grading
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Assignment Statistics -->
        <div class="student-stats-grid mt-4">
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="student-stat-value">3</div>
                <div class="student-stat-label">Pending</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="student-stat-value">1</div>
                <div class="student-stat-label">Submitted</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon primary">
                    <i class="bi bi-award"></i>
                </div>
                <div class="student-stat-value">2</div>
                <div class="student-stat-label">Graded</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="student-stat-value">90%</div>
                <div class="student-stat-label">Avg Score</div>
            </div>
        </div>
    </div>
</section>
