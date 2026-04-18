<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-calendar-check me-2"></i>Attendance Record</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Current Month</option>
                        <option>Last 3 Months</option>
                        <option>This Semester</option>
                    </select>
                </div>
            </div>
            
            <div class="student-stats-grid mb-4">
                <div class="student-stat-card">
                    <div class="student-card-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="student-stat-value">92%</div>
                    <div class="student-stat-label">Overall Attendance</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="student-stat-value">45</div>
                    <div class="student-stat-label">Classes Attended</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon danger">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <div class="student-stat-value">4</div>
                    <div class="student-stat-label">Classes Missed</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="student-stat-value">2</div>
                    <div class="student-stat-label">Late Arrivals</div>
                </div>
            </div>

            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Course</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Check-in Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $attendance = [
                            ['2024-01-15', 'CS301 - Database Systems', '09:00 AM', 'Present', '08:55 AM'],
                            ['2024-01-15', 'CS302 - Algorithms', '11:00 AM', 'Present', '10:58 AM'],
                            ['2024-01-14', 'CS303 - Web Technologies', '02:00 PM', 'Late', '02:15 PM'],
                            ['2024-01-14', 'CS304 - Software Engineering', '09:00 AM', 'Present', '08:50 AM'],
                            ['2024-01-13', 'CS305 - Computer Networks', '11:00 AM', 'Absent', '-'],
                            ['2024-01-13', 'CS306 - Operating Systems', '02:00 PM', 'Present', '01:55 PM'],
                            ['2024-01-12', 'CS301 - Database Systems', '09:00 AM', 'Present', '08:52 AM'],
                            ['2024-01-12', 'CS302 - Algorithms', '11:00 AM', 'Present', '10:55 AM'],
                        ];
                        foreach ($attendance as $record): ?>
                        <tr>
                            <td><?= $record[0] ?></td>
                            <td><?= $record[1] ?></td>
                            <td><?= $record[2] ?></td>
                            <td>
                                <?php if ($record[3] === 'Present'): ?>
                                    <span class="badge bg-success">Present</span>
                                <?php elseif ($record[3] === 'Late'): ?>
                                    <span class="badge bg-warning">Late</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Absent</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $record[4] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Attendance by Course -->
        <div class="student-card mt-4">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-bar-chart me-2"></i>Attendance by Course</h4>
            </div>
            <?php 
            $courseAttendance = [
                ['CS301 - Database Systems', 95],
                ['CS302 - Algorithms', 88],
                ['CS303 - Web Technologies', 92],
                ['CS304 - Software Engineering', 90],
                ['CS305 - Computer Networks', 85],
                ['CS306 - Operating Systems', 94],
            ];
            foreach ($courseAttendance as $course): ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small><?= $course[0] ?></small>
                    <small class="<?= $course[1] >= 90 ? 'text-success' : ($course[1] >= 80 ? 'text-warning' : 'text-danger') ?>"><?= $course[1] ?>%</small>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-<?= $course[1] >= 90 ? 'success' : ($course[1] >= 80 ? 'warning' : 'danger') ?>" style="width: <?= $course[1] ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
