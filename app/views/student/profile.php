<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-profile-header">
                <div class="student-profile-info">
                    <div class="student-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="student-details">
                        <h3><?= e($student['name'] ?? 'John Doe') ?></h3>
                        <p><i class="bi bi-envelope me-2"></i><?= e($student['email'] ?? 'student@stmarysmchmcollege.ac.ke') ?></p>
                        <p><i class="bi bi-credit-card me-2"></i><?= e($student['admission_number'] ?? 'STM/2024/0001') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-4">
            <div class="col-lg-8">
                <?php if (!empty($editMode)): ?>
                <!-- Edit Profile Form -->
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title"><i class="bi bi-pencil me-2"></i>Edit Personal Information</h4>
                        <a href="<?= e(base_url('portal/profile')) ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
                    </div>
                    <form method="POST" action="<?= e(base_url('portal/profile/update')) ?>">
                        <div class="row g-3">
                            <!-- Editable Fields -->
                            <div class="col-md-6">
                                <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= e($student['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= e($student['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= e($student['phone'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="national_id">National ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="national_id" name="national_id" value="<?= e($student['national_id'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="county">County <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="county" name="county" value="<?= e($student['county'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="sub_county">Sub County <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sub_county" name="sub_county" value="<?= e($student['sub_county'] ?? '') ?>" required>
                            </div>

                            <!-- Read-only Fields (Non-editable) -->
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Date of Birth</label>
                                <input type="date" class="form-control" value="<?= e($student['date_of_birth'] ?? '') ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Admission Number</label>
                                <input type="text" class="form-control" value="<?= e($student['admission_number'] ?? '') ?>" readonly disabled>
                                <small class="text-muted">Contact admin to change</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Programme</label>
                                <input type="text" class="form-control" value="<?= e($programmeName ?? '') ?>" readonly disabled>
                                <small class="text-muted">Contact admin to change</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Gender</label>
                                <input type="text" class="form-control" value="<?= e($student['gender'] ?? '') ?>" readonly disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Guardian Name</label>
                                <input type="text" class="form-control" value="<?= e($student['guardian_name'] ?? '') ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Guardian Relationship</label>
                                <input type="text" class="form-control" value="<?= e($student['guardian_relationship'] ?? '') ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Guardian Phone</label>
                                <input type="tel" class="form-control" value="<?= e($student['guardian_phone'] ?? '') ?>" readonly disabled>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                                <a href="<?= e(base_url('portal/profile')) ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- View Profile Mode -->
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title"><i class="bi bi-person me-2"></i>Personal Information</h4>
                        <a href="<?= e(base_url('portal/profile/edit')) ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="<?= e($student['name'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?= e($student['email'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" value="<?= e($student['phone'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" value="<?= e($student['date_of_birth'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admission Number</label>
                            <input type="text" class="form-control" value="<?= e($student['admission_number'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Programme</label>
                            <input type="text" class="form-control" value="<?= e($programmeName ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">National ID</label>
                            <input type="text" class="form-control" value="<?= e($student['national_id'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <input type="text" class="form-control" value="<?= e($student['gender'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">County</label>
                            <input type="text" class="form-control" value="<?= e($student['county'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub County</label>
                            <input type="text" class="form-control" value="<?= e($student['sub_county'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Guardian Name</label>
                            <input type="text" class="form-control" value="<?= e($student['guardian_name'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Relationship</label>
                            <input type="text" class="form-control" value="<?= e($student['guardian_relationship'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Phone</label>
                            <input type="tel" class="form-control" value="<?= e($student['guardian_phone'] ?? '') ?>" readonly>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="student-card mt-4">
                    <div class="student-card-header">
                        <h4 class="student-card-title"><i class="bi bi-shield-lock me-2"></i>Security</h4>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter current password">
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" placeholder="Enter new password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" placeholder="Confirm new password">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Update Password</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title"><i class="bi bi-trophy me-2"></i>Achievements</h4>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success">Dean's List</span>
                            <small class="text-muted">Semester 1</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary">Best Project</span>
                            <small class="text-muted">CS301</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning">Perfect Attendance</span>
                            <small class="text-muted">Semester 1</small>
                        </div>
                    </div>
                </div>

                <div class="student-card mt-4">
                    <div class="student-card-header">
                        <h4 class="student-card-title"><i class="bi bi-bell me-2"></i>Notifications</h4>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                        <label class="form-check-label" for="emailNotif">Email Notifications</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="smsNotif">
                        <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="pushNotif" checked>
                        <label class="form-check-label" for="pushNotif">Push Notifications</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
