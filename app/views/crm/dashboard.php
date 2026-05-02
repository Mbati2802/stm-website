<h2>Dashboard</h2>

<!-- Metrics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card metric-card">
            <div class="metric-value"><?= $totalInquiries ?></div>
            <div class="metric-label">Total Inquiries</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card metric-card">
            <div class="metric-value text-info"><?= $contacted ?></div>
            <div class="metric-label">Contacted</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card metric-card">
            <div class="metric-value text-success"><?= $registrationPaid ?></div>
            <div class="metric-label">Registration Paid</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card metric-card">
            <div class="metric-value text-primary"><?= $conversionRate ?>%</div>
            <div class="metric-label">Conversion Rate</div>
        </div>
    </div>
</div>

<!-- Additional Metrics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card metric-card">
            <div class="metric-value text-warning"><?= $interested ?></div>
            <div class="metric-label">Interested</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card metric-card">
            <div class="metric-value text-warning"><?= $offersIssued ?></div>
            <div class="metric-label">Offers Issued</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card metric-card">
            <div class="metric-value text-info"><?= $enrolled ?></div>
            <div class="metric-label">Enrolled</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card metric-card">
            <div class="metric-value text-danger"><?= $lost ?></div>
            <div class="metric-label">Lost</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card metric-card">
            <div class="metric-value text-success">KES <?= number_format($revenue, 2) ?></div>
            <div class="metric-label">Revenue (Registration Fees)</div>
        </div>
    </div>
</div>

<!-- Recent Leads -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> Recent Leads</span>
        <a href="/crm/leads" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLeads as $lead): ?>
                        <tr>
                            <td><strong><?= e($lead['name']) ?></strong></td>
                            <td><?= e($lead['phone']) ?></td>
                            <td><?= e($lead['course_interest'] ?? '-') ?></td>
                            <td><?= e(ucwords(str_replace('_', ' ', $lead['lead_source']))) ?></td>
                            <td>
                                <span class="status-badge" style="background: <?= $lead['status_color'] ?>; color: white;">
                                    <?= e($lead['status_name']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($lead['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Payments -->
<div class="card">
    <div class="card-header">
        <span><i class="bi bi-cash"></i> Recent Payments</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Phone</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction Code</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPayments as $payment): ?>
                        <tr>
                            <td><strong><?= e($payment['lead_name']) ?></strong></td>
                            <td><?= e($payment['phone']) ?></td>
                            <td class="text-success">KES <?= number_format($payment['amount'], 2) ?></td>
                            <td><?= e(ucwords($payment['payment_method'])) ?></td>
                            <td><?= e($payment['transaction_code'] ?? '-') ?></td>
                            <td><?= date('M j, Y', strtotime($payment['payment_date'])) ?></td>
                            <td>
                                <?php if ($payment['status'] === 'verified'): ?>
                                    <span class="badge bg-success">Verified</span>
                                <?php elseif ($payment['status'] === 'pending'): ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function e(string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
</script>
