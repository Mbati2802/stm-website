<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-clipboard-check me-2"></i>Exams & Assessments</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Exams</option>
                        <option>Upcoming</option>
                        <option>Completed</option>
                    </select>
                </div>
            </div>
            
            <?php 
            $exams = [
                ['Mid-Term Examination', 'CS301 - Database Systems', 'Jan 25, 2024', '09:00 AM', 'Room 205', 'upcoming', 100],
                ['Mid-Term Examination', 'CS302 - Algorithms', 'Jan 26, 2024', '11:00 AM', 'Room 301', 'upcoming', 100],
                ['Mid-Term Examination', 'CS303 - Web Technologies', 'Jan 27, 2024', '02:00 PM', 'Lab 1', 'upcoming', 100],
                ['Quiz 1', 'CS304 - Software Engineering', 'Jan 15, 2024', '10:00 AM', 'Room 205', 'completed', 85],
                ['Quiz 1', 'CS305 - Computer Networks', 'Jan 16, 2024', '11:00 AM', 'Room 301', 'completed', 78],
                ['Quiz 1', 'CS306 - Operating Systems', 'Jan 17, 2024', '02:00 PM', 'Lab 2', 'completed', 92],
            ];
            foreach ($exams as $exam): ?>
            <div class="assignment-card <?= $exam[5] === 'upcoming' ? 'urgent' : 'completed' ?>">
                <div class="assignment-header">
                    <div>
                        <h6 class="assignment-title"><?= $exam[0] ?></h6>
                        <small class="text-muted"><?= $exam[1] ?></small>
                    </div>
                    <?php if ($exam[5] === 'upcoming'): ?>
                        <span class="badge bg-primary rounded-pill">Upcoming</span>
                    <?php else: ?>
                        <span class="badge bg-success rounded-pill">Score: <?= $exam[6] ?>%</span>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="bi bi-calendar me-1"></i><?= $exam[2] ?>
                        <i class="bi bi-clock ms-3 me-1"></i><?= $exam[3] ?>
                        <i class="bi bi-geo-alt ms-3 me-1"></i><?= $exam[4] ?>
                    </div>
                    <?php if ($exam[5] === 'upcoming'): ?>
                        <button class="btn btn-sm btn-primary">View Details</button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-action-view" title="View Results"><i class="bi bi-eye"></i></button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Exam Statistics -->
        <div class="student-stats-grid mt-4">
            <div class="student-stat-card">
                <div class="student-card-icon primary">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="student-stat-value">3</div>
                <div class="student-stat-label">Upcoming Exams</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="student-stat-value">3</div>
                <div class="student-stat-label">Completed</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-award"></i>
                </div>
                <div class="student-stat-value">85%</div>
                <div class="student-stat-label">Average Score</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="student-stat-value">Top 20%</div>
                <div class="student-stat-label">Class Rank</div>
            </div>
        </div>
    </div>
</section>
