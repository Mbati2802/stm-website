<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-credit-card me-2"></i>Fee Statement</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Semester 2 (Current)</option>
                        <option>Semester 1</option>
                        <option>All Semesters</option>
                    </select>
                    <button class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download PDF</button>
                </div>
            </div>
            
            <div class="student-stats-grid mb-4">
                <div class="student-stat-card">
                    <div class="student-card-icon primary">
                        <i class="bi bi-cash"></i>
                    </div>
                    <div class="student-stat-value">KES 150,000</div>
                    <div class="student-stat-label">Total Fees</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="student-stat-value">KES 120,000</div>
                    <div class="student-stat-label">Paid</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="student-stat-value">KES 30,000</div>
                    <div class="student-stat-label">Balance</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon danger">
                        <i class="bi bi-calendar"></i>
                    </div>
                    <div class="student-stat-value">Feb 15</div>
                    <div class="student-stat-label">Due Date</div>
                </div>
            </div>

            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $fees = [
                            ['Tuition Fees', 'KES 100,000', 'Paid', 'Jan 5, 2024'],
                            ['Library Fees', 'KES 5,000', 'Paid', 'Jan 5, 2024'],
                            ['Lab Fees', 'KES 10,000', 'Paid', 'Jan 5, 2024'],
                            ['Examination Fees', 'KES 5,000', 'Paid', 'Jan 5, 2024'],
                            ['Student Activity Fees', 'KES 15,000', 'Pending', '-'],
                            ['Hostel Fees', 'KES 15,000', 'Pending', '-'],
                        ];
                        foreach ($fees as $fee): ?>
                        <tr>
                            <td><?= $fee[0] ?></td>
                            <td><strong><?= $fee[1] ?></strong></td>
                            <td>
                                <?php if ($fee[2] === 'Paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $fee[3] ?></td>
                            <td>
                                <?php if ($fee[2] === 'Pending'): ?>
                                    <button class="btn btn-sm btn-primary">Pay Now</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-action-view" title="View Receipt"><i class="bi bi-receipt"></i></button>
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
