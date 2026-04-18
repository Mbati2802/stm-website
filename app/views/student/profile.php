<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-profile-header">
                <div class="student-profile-info">
                    <div class="student-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="student-details">
                        <h3><?= e($_SESSION['student_name'] ?? 'John Doe') ?></h3>
                        <p><i class="bi bi-envelope me-2"></i><?= e($_SESSION['student_email'] ?? 'student@stmarysmchmcollege.ac.ke') ?></p>
                        <p><i class="bi bi-credit-card me-2"></i><?= e($_SESSION['student_admission_number'] ?? 'STM/2024/0001') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-4">
            <div class="col-lg-8">
                <div class="student-card">
                    <div class="student-card-header">
                        <h4 class="student-card-title"><i class="bi bi-person me-2"></i>Personal Information</h4>
                        <button class="btn btn-sm btn-primary"><i class="bi bi-pencil me-1"></i>Edit</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="<?= e($_SESSION['student_name'] ?? 'John Doe') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?= e($_SESSION['student_email'] ?? 'student@stmarysmchmcollege.ac.ke') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" value="+254 700 123 456" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" value="2000-05-15" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admission Number</label>
                            <input type="text" class="form-control" value="<?= e($_SESSION['student_admission_number'] ?? 'STM/2024/0001') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Programme</label>
                            <input type="text" class="form-control" value="Computer Science" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" rows="2" readonly>Nairobi, Kenya</textarea>
                        </div>
                    </div>
                </div>

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
