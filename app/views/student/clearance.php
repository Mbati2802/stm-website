<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-check-circle me-2"></i>Clearance Status</h4>
                <button class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download Clearance Form</button>
            </div>
            
            <div class="student-stats-grid mb-4">
                <div class="student-stat-card">
                    <div class="student-card-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="student-stat-value">4</div>
                    <div class="student-stat-label">Cleared</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon warning">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="student-stat-value">2</div>
                    <div class="student-stat-label">Pending</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon primary">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="student-stat-value">67%</div>
                    <div class="student-stat-label">Progress</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="student-stat-value">2</div>
                    <div class="student-stat-label">Issues</div>
                </div>
            </div>

            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $clearance = [
                            ['Library', 'Cleared', 'No outstanding books or fines', 'Jan 15, 2024'],
                            ['Finance', 'Cleared', 'All fees paid', 'Jan 14, 2024'],
                            ['Sports Department', 'Cleared', 'Equipment returned', 'Jan 13, 2024'],
                            ['Hostel', 'Cleared', 'Room vacated properly', 'Jan 12, 2024'],
                            ['Academic Department', 'Pending', 'Awaiting final grade submission', '-'],
                            ['IT Department', 'Pending', 'Return college laptop', '-'],
                        ];
                        foreach ($clearance as $item): ?>
                        <tr>
                            <td><strong><?= $item[0] ?></strong></td>
                            <td>
                                <?php if ($item[1] === 'Cleared'): ?>
                                    <span class="badge bg-success">Cleared</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $item[2] ?></td>
                            <td><?= $item[3] ?></td>
                            <td>
                                <?php if ($item[1] === 'Pending'): ?>
                                    <button class="btn btn-sm btn-primary">Follow Up</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-action-view" title="View Details"><i class="bi bi-eye"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
