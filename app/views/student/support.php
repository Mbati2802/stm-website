<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-headset me-2"></i>IT Support</h4>
                <span class="badge bg-primary"><?= count($tickets ?? []) ?> ticket(s)</span>
            </div>

            <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

            <form method="POST" action="<?= e(base_url('portal/support')) ?>" class="soft-card p-3 mb-4">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="General">General</option>
                            <option value="Login">Login</option>
                            <option value="Portal Access">Portal Access</option>
                            <option value="Grades">Grades</option>
                            <option value="Library">Library</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Subject</label>
                        <input class="form-control" name="subject" required placeholder="Briefly describe your issue">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Issue Details</label>
                        <textarea class="form-control" name="message" rows="4" required placeholder="Provide full details so support can assist quickly"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Ticket</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Details</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($tickets ?? []) as $ticket): ?>
                        <tr>
                            <td><?= e((string)($ticket['subject'] ?? 'Support Ticket')) ?></td>
                            <td><?= e(substr((string)($ticket['message'] ?? ''), 0, 110)) ?>...</td>
                            <td><?= e((string)($ticket['created_at'] ?? '')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tickets ?? [])): ?>
                        <tr><td colspan="3" class="text-center text-muted">No support tickets submitted yet.</td></tr>
                        <?php endif; ?>
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
                    <div class="col-md-6"><div class="alert alert-info"><i class="bi bi-clock me-2"></i><strong>Support Hours:</strong><p class="mb-0 mt-1">Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM</p></div></div>
                </div>
            </div>
        </div>
    </div>
</section>
