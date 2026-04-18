<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-headset me-2"></i>IT Support</h4>
                <button class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>New Ticket</button>
            </div>
            
            <div class="student-stats-grid mb-4">
                <div class="student-stat-card">
                    <div class="student-card-icon primary">
                        <i class="bi bi-ticket"></i>
                    </div>
                    <div class="student-stat-value">5</div>
                    <div class="student-stat-label">Total Tickets</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon warning">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="student-stat-value">2</div>
                    <div class="student-stat-label">Open</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="student-stat-value">3</div>
                    <div class="student-stat-label">Resolved</div>
                </div>
                <div class="student-stat-card">
                    <div class="student-card-icon danger">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="student-stat-value">24h</div>
                    <div class="student-stat-label">Avg Response</div>
                </div>
            </div>

            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $tickets = [
                            ['#TKT001', 'Cannot access student portal', 'Login Issue', 'Open', 'Jan 18, 2024'],
                            ['#TKT002', 'WiFi not working in hostel', 'Network', 'Open', 'Jan 17, 2024'],
                            ['#TKT003', 'Library system access denied', 'Access', 'Resolved', 'Jan 15, 2024'],
                            ['#TKT004', 'Email not receiving messages', 'Email', 'Resolved', 'Jan 12, 2024'],
                            ['#TKT005', 'Password reset not working', 'Account', 'Resolved', 'Jan 10, 2024'],
                        ];
                        foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><strong><?= $ticket[0] ?></strong></td>
                            <td><?= $ticket[1] ?></td>
                            <td><?= $ticket[2] ?></td>
                            <td>
                                <?php if ($ticket[3] === 'Open'): ?>
                                    <span class="badge bg-warning">Open</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Resolved</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $ticket[4] ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-action-view" title="View Details"><i class="bi bi-eye"></i></button>
                                    <?php if ($ticket[3] === 'Open'): ?>
                                        <button class="btn btn-sm btn-action-edit" title="Follow Up"><i class="bi bi-chat-dots"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Contact Information -->
            <div class="student-card mt-4">
                <div class="student-card-header">
                    <h4 class="student-card-title"><i class="bi bi-info-circle me-2"></i>Contact IT Support</h4>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <p class="mb-0">support@stmarysmchmcollege.ac.ke</p>
                        </div>
                        <div class="mb-3">
                            <strong>Phone:</strong>
                            <p class="mb-0">+254 700 123 456</p>
                        </div>
                        <div class="mb-3">
                            <strong>Location:</strong>
                            <p class="mb-0">IT Department, Ground Floor, Admin Block</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="bi bi-clock me-2"></i>
                            <strong>Support Hours:</strong>
                            <p class="mb-0 mt-1">Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
