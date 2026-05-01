<?php
$kenyanCounties = [
    'Baringo','Bomet','Bungoma','Busia','Elgeyo-Marakwet','Embu','Garissa','Homa Bay','Isiolo','Kajiado','Kakamega',
    'Kericho','Kiambu','Kilifi','Kirinyaga','Kisii','Kisumu','Kitui','Kwale','Laikipia','Lamu','Machakos','Makueni',
    'Mandera','Marsabit','Meru','Migori','Mombasa','Murang\'a','Nairobi','Nakuru','Nandi','Narok','Nyamira','Nyandarua',
    'Nyeri','Samburu','Siaya','Taita-Taveta','Tana River','Tharaka-Nithi','Trans Nzoia','Turkana','Uasin Gishu',
    'Vihiga','Wajir','West Pokot'
];
?>

<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Admission Form</h1>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <div class="soft-card p-4">
            <form method="POST" action="<?= e(base_url('admin/admission')) ?>">
                <?= csrf_field() ?>
                
                <h2 class="h6 text-uppercase text-muted mb-3">Personal Information</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input required name="name" class="form-control" placeholder="Enter full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select required name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input required type="date" name="date_of_birth" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">National ID Number <span class="text-danger">*</span></label>
                        <input required name="national_id" class="form-control" placeholder="Enter ID number">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input required name="phone" class="form-control" placeholder="+254 XXX XXX XXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input required type="email" name="email" class="form-control" placeholder="student@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">County <span class="text-danger">*</span></label>
                        <select required name="county" class="form-select">
                            <option value="">Select County</option>
                            <?php foreach ($kenyanCounties as $county): ?>
                                <option value="<?= e($county) ?>"><?= e($county) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sub-County/Town</label>
                        <input name="sub_county" class="form-control" placeholder="Enter sub-county or town">
                    </div>
                </div>

                <h2 class="h6 text-uppercase text-muted mb-3">Guardian Information</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Guardian Name <span class="text-danger">*</span></label>
                        <input required name="guardian_name" class="form-control" placeholder="Guardian full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Relationship <span class="text-danger">*</span></label>
                        <select required name="guardian_relationship" class="form-select">
                            <option value="">Select Relationship</option>
                            <option value="Parent">Parent</option>
                            <option value="Guardian">Guardian</option>
                            <option value="Sponsor">Sponsor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Phone <span class="text-danger">*</span></label>
                        <input required name="guardian_phone" class="form-control" placeholder="+254 XXX XXX XXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Email</label>
                        <input type="email" name="guardian_email" class="form-control" placeholder="guardian@example.com">
                    </div>
                </div>

                <h2 class="h6 text-uppercase text-muted mb-3">Academic Information</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Previous School <span class="text-danger">*</span></label>
                        <input required name="previous_school" class="form-control" placeholder="Name of previous school">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">KCSE Year <span class="text-danger">*</span></label>
                        <input required type="number" name="kcse_year" class="form-control" placeholder="YYYY" min="2000" max="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">KCSE Grade <span class="text-danger">*</span></label>
                        <select required name="kcse_grade" class="form-select">
                            <option value="">Select Grade</option>
                            <option value="A">A</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B">B</option>
                            <option value="B-">B-</option>
                            <option value="C+">C+</option>
                            <option value="C">C</option>
                            <option value="C-">C-</option>
                            <option value="D+">D+</option>
                            <option value="D">D</option>
                            <option value="D-">D-</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">KCSE Index Number</label>
                        <input name="kcse_index" class="form-control" placeholder="KCSE index number">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Programme <span class="text-danger">*</span></label>
                        <select required name="programme_id" class="form-select">
                            <option value="">Select Programme</option>
                            <?php foreach (($programmes ?? []) as $programme): ?>
                                <option value="<?= e((string)$programme['id']) ?>"><?= e($programme['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Preferred Intake <span class="text-danger">*</span></label>
                        <select required name="preferred_intake" class="form-select">
                            <option value="">Select Intake</option>
                            <?php foreach (['January', 'March', 'May', 'July', 'September', 'November'] as $intake): ?>
                                <option value="<?= e($intake) ?>"><?= e($intake) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h2 class="h6 text-uppercase text-muted mb-3">Additional Information</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Disability Status</label>
                        <select name="disability_status" class="form-select">
                            <option value="None">None</option>
                            <option value="Physical">Physical</option>
                            <option value="Visual">Visual</option>
                            <option value="Hearing">Hearing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Referral Source</label>
                        <input name="referral_source" class="form-control" placeholder="e.g. Social media, Friend, Radio">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Additional Notes</label>
                        <textarea name="additional_notes" rows="3" class="form-control" placeholder="Any additional information"></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-person-check me-2"></i>Admit Student</button>
                    <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
