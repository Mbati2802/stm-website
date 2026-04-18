<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-award me-2"></i>Grades & Results</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Current Semester</option>
                        <option>All Semesters</option>
                    </select>
                    <button class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download</button>
                </div>
            </div>
            
            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credits</th>
                            <th>Mid-Term</th>
                            <th>Final</th>
                            <th>Grade</th>
                            <th>GPA Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $courses = [
                            ['CS301', 'Database Systems', 3, 85, 88, 'A-', 3.7],
                            ['CS302', 'Algorithms', 4, 78, 82, 'B+', 3.3],
                            ['CS303', 'Web Technologies', 3, 90, 92, 'A', 4.0],
                            ['CS304', 'Software Engineering', 3, 75, 80, 'B', 3.0],
                            ['CS305', 'Computer Networks', 3, 82, 85, 'B+', 3.3],
                            ['CS306', 'Operating Systems', 4, 88, 90, 'A-', 3.7],
                        ];
                        foreach ($courses as $course): ?>
                        <tr>
                            <td><strong><?= $course[0] ?></strong></td>
                            <td><?= $course[1] ?></td>
                            <td><?= $course[2] ?></td>
                            <td><?= $course[3] ?>%</td>
                            <td><?= $course[4] ?>%</td>
                            <td>
                                <span class="badge bg-<?= 
                                    $course[5] === 'A' ? 'success' : 
                                    ($course[5] === 'A-' ? 'primary' : 
                                    ($course[5] === 'B+' ? 'info' : 'warning')) 
                                ?>"><?= $course[5] ?></span>
                            </td>
                            <td><?= $course[6] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GPA Summary -->
        <div class="student-stats-grid mt-4">
            <div class="student-stat-card">
                <div class="student-card-icon primary">
                    <i class="bi bi-calculator"></i>
                </div>
                <div class="student-stat-value">3.5</div>
                <div class="student-stat-label">Current GPA</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon success">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="student-stat-value">3.4</div>
                <div class="student-stat-label">Cumulative GPA</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon warning">
                    <i class="bi bi-book"></i>
                </div>
                <div class="student-stat-value">20</div>
                <div class="student-stat-label">Total Credits</div>
            </div>
            <div class="student-stat-card">
                <div class="student-card-icon danger">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="student-stat-value">Top 15%</div>
                <div class="student-stat-label">Class Rank</div>
            </div>
        </div>

        <!-- Grade Distribution Chart -->
        <div class="student-card mt-4">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-pie-chart me-2"></i>Grade Distribution</h4>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>A Grade (4.0)</small>
                            <small>1 course</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 16.7%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>A- Grade (3.7)</small>
                            <small>2 courses</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 33.3%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>B+ Grade (3.3)</small>
                            <small>2 courses</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 33.3%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>B Grade (3.0)</small>
                            <small>1 course</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 16.7%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Performance Summary</strong>
                        <p class="mb-0 small mt-2">You're performing above average with a strong GPA. Keep up the good work!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
