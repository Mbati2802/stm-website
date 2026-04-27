<?php
$toggleItems = [
    'show_page_about' => 'Show About Page',
    'show_page_programmes' => 'Show Programmes Page',
    'show_page_library' => 'Show Library Page',
    'show_page_media' => 'Show Media/Gallery Pages',
    'show_page_contact' => 'Show Contact Page',
    'show_page_faqs' => 'Show FAQs Page',
    'show_page_principal' => 'Show Principal Page',
    'show_home_hero' => 'Show Home Hero',
    'show_home_cards' => 'Show Home Intro Cards',
    'show_home_banner' => 'Show Home Banner Image',
    'show_home_why' => 'Show Why Choose Us',
    'show_home_courses' => 'Show Courses On Offer',
    'show_home_testimonials' => 'Show Testimonials',
    'show_home_events' => 'Show Events',
    'show_home_news' => 'Show Latest News',
    'show_home_cta' => 'Show Final CTA Block',
];
$ctaPageOptions = [
    '' => 'Select a page',
    'programmes' => 'Programmes',
    'programmes/how-to-apply' => 'How To Apply',
    'programmes/apply' => 'Apply Online',
    'about' => 'About',
    'contact' => 'Contact',
    'contact-registrar' => 'Contact Registrar',
    'events' => 'Events',
    'library' => 'Library',
    'media' => 'Media Desk',
    'portals' => 'Portals',
    'portal/login' => 'Student Portal',
    'staff/login' => 'Staff Portal',
];
$manageableEntities = [
    'programmes' => 'Programmes',
    'departments' => 'Departments',
    'news' => 'News',
    'careers' => 'Careers',
    'tenders' => 'Tenders',
    'events' => 'Events',
    'gallery' => 'Gallery',
    'library_resources' => 'Library Resources',
    'faqs' => 'FAQs',
    'pages' => 'Pages',
    'messages' => 'Messages',
    'students' => 'Students',
    'portal_courses' => 'Portal Courses',
    'programme_timetables' => 'Programme Timetables',
    'course_grades' => 'Course Grades',
    'grading_schemes' => 'Grading Schemes',
    'course_assignments' => 'Assignments',
    'study_materials' => 'Study Materials',
    'users' => 'Staff Users',
    'media' => 'Media Library',
];
$selectedSeniorPermissions = array_values(array_filter(array_map('trim', explode(',', (string)($settings['junior_admin_permissions'] ?? '')))));
$selectedTeacherPermissions = array_values(array_filter(array_map('trim', explode(',', (string)($settings['teacher_permissions'] ?? '')))));
?>

<section class="py-4">
    <div class="admin-content-wrap">
        <div class="settings-hero mb-3">
            <div>
                <h1 class="h4 fw-bold mb-1">UI Content Settings</h1>
                <p class="text-muted mb-0">Configure your public website content and role-based content governance from one place.</p>
            </div>
            <button form="settings-form" class="btn btn-primary">Save Settings</button>
        </div>
        <div class="soft-card p-3 mb-3">
            <div class="d-flex flex-wrap gap-2" id="settings-tabs">
                <button type="button" class="btn btn-sm btn-primary" data-settings-tab="general">General</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-settings-tab="home">Home</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-settings-tab="images">Programme & Banner Images</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-settings-tab="visibility">Visibility Controls</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-settings-tab="about">About Page</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-settings-tab="principal">Principal/Registrar</button>
            </div>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-danger"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php
        $diag = is_array($emailDiagnostics ?? null) ? $emailDiagnostics : [];
        $diagStatus = strtolower(trim((string)($diag['status'] ?? '')));
        $diagContext = is_array($diag['context'] ?? null) ? $diag['context'] : [];
        $diagTo = (string)($diagContext['to'] ?? '');
        $diagSubject = (string)($diagContext['subject'] ?? '');
        $diagError = (string)($diagContext['error'] ?? '');
        $diagTime = (string)($diagContext['time'] ?? '');
        $diagClass = 'secondary';
        $diagHistory = is_array($emailDiagnosticsHistory ?? null) ? $emailDiagnosticsHistory : [];
        if (str_contains($diagStatus, 'success')) {
            $diagClass = 'success';
        } elseif (str_contains($diagStatus, 'failed') || str_contains($diagStatus, 'exception') || str_contains($diagStatus, 'invalid')) {
            $diagClass = 'danger';
        }
        ?>
        <div class="soft-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 text-uppercase text-muted mb-0">Email Diagnostics</h2>
                <?php if ($diagStatus !== ''): ?>
                    <span class="badge bg-<?= e($diagClass) ?>"><?= e(strtoupper(str_replace('-', ' ', $diagStatus))) ?></span>
                <?php else: ?>
                    <span class="badge bg-secondary">NO ATTEMPTS YET</span>
                <?php endif; ?>
            </div>
            <?php if ($diagStatus === ''): ?>
                <p class="text-muted mb-0 small">No email delivery attempts recorded in this admin session yet.</p>
            <?php else: ?>
                <div class="row g-2 small">
                    <div class="col-md-6"><strong>Time:</strong> <?= e($diagTime !== '' ? $diagTime : 'N/A') ?></div>
                    <div class="col-md-6"><strong>Recipient:</strong> <?= e($diagTo !== '' ? $diagTo : 'N/A') ?></div>
                    <div class="col-md-12"><strong>Subject:</strong> <?= e($diagSubject !== '' ? $diagSubject : 'N/A') ?></div>
                    <div class="col-md-12"><strong>Reason:</strong> <?= e($diagError !== '' ? $diagError : 'No error reported.') ?></div>
                </div>
            <?php endif; ?>
            <hr class="my-3">
            <h3 class="h6 text-uppercase text-muted mb-2">Recent Delivery History</h3>
            <?php if ($diagHistory === []): ?>
                <p class="text-muted mb-0 small">No persisted email logs found yet. Run the latest SQL migration to enable database history.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Recipient</th>
                                <th>Subject</th>
                                <th>Reason</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($diagHistory as $entry): ?>
                                <?php
                                $entryStatus = strtolower(trim((string)($entry['status'] ?? '')));
                                $entryClass = 'secondary';
                                if (str_contains($entryStatus, 'success')) {
                                    $entryClass = 'success';
                                } elseif (str_contains($entryStatus, 'failed') || str_contains($entryStatus, 'exception') || str_contains($entryStatus, 'invalid')) {
                                    $entryClass = 'danger';
                                }
                                ?>
                                <tr>
                                    <td><span class="badge bg-<?= e($entryClass) ?>"><?= e(strtoupper(str_replace('-', ' ', $entryStatus !== '' ? $entryStatus : 'unknown'))) ?></span></td>
                                    <td title="<?= e((string)($entry['recipient_email'] ?? '')) ?>"><?= e((string)($entry['recipient_email'] ?? '')) ?></td>
                                    <td title="<?= e((string)($entry['subject'] ?? '')) ?>"><?= e((string)($entry['subject'] ?? '')) ?></td>
                                    <td title="<?= e((string)($entry['error_message'] ?? '')) ?>"><?= e((string)($entry['error_message'] ?? '')) ?></td>
                                    <td><?= e((string)($entry['created_at'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <form id="settings-form" method="POST" action="<?= e(base_url('admin/settings')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3 settings-layout">
                <div class="col-lg-6 d-grid gap-3">
                    <div class="soft-card p-4 settings-card" data-settings-section="general">
                        <h2 class="h6 text-uppercase text-primary settings-card-header mb-3">General</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control" value="<?= e($settings['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input name="email" class="form-control" value="<?= e($settings['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admissions Email</label>
                                <input name="admissions_email" class="form-control" value="<?= e($settings['admissions_email'] ?? 'admission@stmarysmchmcollege.ac.ke') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input name="location" class="form-control" value="<?= e($settings['location'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Top Bar Message</label>
                                <input name="top_message" class="form-control" value="<?= e($settings['top_message'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Programme Application Confirmation Email Message</label>
                                <textarea name="application_confirmation_message" rows="10" class="form-control" placeholder="Sent to the applicant after a programme application is submitted. Use {PHONE} and {EMAIL} placeholders."><?= e($settings['application_confirmation_message'] ?? '') ?></textarea>
                                <small class="text-muted">Placeholders supported: <code>{PHONE}</code>, <code>{EMAIL}</code>. You can paste HTML here for a richer email.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Current Intake</label>
                                <select name="current_intake" class="form-select">
                                    <?php foreach (['January', 'March', 'May', 'July', 'September', 'November'] as $intake): ?>
                                        <option value="<?= e($intake) ?>" <?= ($settings['current_intake'] ?? 'January') === $intake ? 'selected' : '' ?>><?= e($intake) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Admission Number Format</label>
                                <input name="admission_number_format" class="form-control" value="<?= e($settings['admission_number_format'] ?? 'STM/{YEAR}/{SEQ4}') ?>">
                                <small class="text-muted">Available placeholders: {YEAR}, {YY}, {MM}, {DD}, {SEQ4}, {SEQ5}, {SEQ6}, {ID}</small>
                            </div>
                        </div>
                    </div>

                    <div class="soft-card p-4 settings-card" data-settings-section="general">
                        <h2 class="h6 text-uppercase text-primary settings-card-header mb-3">Admin Reply Email Template</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Reply Heading</label>
                                <input name="admin_reply_email_heading" class="form-control" value="<?= e($settings['admin_reply_email_heading'] ?? 'Thank you for your email') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reply Subheading</label>
                                <input name="admin_reply_email_subheading" class="form-control" value="<?= e($settings['admin_reply_email_subheading'] ?? '') ?>" placeholder="Here is our response from the college">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Logo URL/Path</label>
                                <input name="admin_reply_email_logo_url" class="form-control" value="<?= e($settings['admin_reply_email_logo_url'] ?? '') ?>" placeholder="<?= e(base_url('assets/images/logo.png')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Footer Text</label>
                                <input name="admin_reply_email_footer_text" class="form-control" value="<?= e($settings['admin_reply_email_footer_text'] ?? '') ?>" placeholder="We value your message and are always ready to assist.">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Background Color</label>
                                <input name="admin_reply_email_bg_color" class="form-control" value="<?= e($settings['admin_reply_email_bg_color'] ?? '#ffffff') ?>" placeholder="#ffffff">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Card Color</label>
                                <input name="admin_reply_email_card_color" class="form-control" value="<?= e($settings['admin_reply_email_card_color'] ?? '#f5f6fb') ?>" placeholder="#f5f6fb">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Accent Color</label>
                                <input name="admin_reply_email_accent_color" class="form-control" value="<?= e($settings['admin_reply_email_accent_color'] ?? '#5fc7e7') ?>" placeholder="#5fc7e7">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Footer BG Color</label>
                                <input name="admin_reply_email_footer_bg_color" class="form-control" value="<?= e($settings['admin_reply_email_footer_bg_color'] ?? '#2c3653') ?>" placeholder="#2c3653">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="preview-reply-template-btn">Preview Reply Template</button>
                            </div>
                        </div>
                    </div>

                    <?php if (Auth::isSuperAdmin()): ?>
                    <div class="soft-card p-4 settings-card" data-settings-section="general">
                        <h2 class="h6 text-uppercase text-primary settings-card-header mb-3">Senior Admin Permissions</h2>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Senior Admin Permissions</label>
                                <input id="senior_permissions_input" name="junior_admin_permissions" class="form-control" value="<?= e($settings['junior_admin_permissions'] ?? '') ?>" placeholder="programmes,events,portal_courses,users,grading_schemes">
                                <div class="permission-grid mt-2">
                                    <?php foreach ($manageableEntities as $entityKey => $entityLabel): ?>
                                        <label class="form-check permission-item">
                                            <input class="form-check-input permission-checkbox" data-target-input="senior_permissions_input" type="checkbox" value="<?= e($entityKey) ?>" <?= in_array($entityKey, $selectedSeniorPermissions, true) ? 'checked' : '' ?>>
                                            <span class="form-check-label"><?= e($entityLabel) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted">Super Admin controls what Senior Admin can access/manage.</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="soft-card p-4 settings-card" data-settings-section="general">
                        <h2 class="h6 text-uppercase text-primary settings-card-header mb-3">Teacher Permissions</h2>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Teacher Permissions</label>
                                <input id="teacher_permissions_input" name="teacher_permissions" class="form-control" value="<?= e($settings['teacher_permissions'] ?? '') ?>" placeholder="portal_courses,course_grades,course_assignments,study_materials">
                                <div class="permission-grid mt-2">
                                    <?php foreach ($manageableEntities as $entityKey => $entityLabel): ?>
                                        <label class="form-check permission-item">
                                            <input class="form-check-input permission-checkbox" data-target-input="teacher_permissions_input" type="checkbox" value="<?= e($entityKey) ?>" <?= in_array($entityKey, $selectedTeacherPermissions, true) ? 'checked' : '' ?>>
                                            <span class="form-check-label"><?= e($entityLabel) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted">Senior Admin and Super Admin can control what teachers manage.</small>
                            </div>
                        </div>
                    </div>

                    <div class="soft-card p-4 settings-card settings-card-home settings-card-home-vertical" data-settings-section="home">
                        <h2 class="h6 text-uppercase text-muted mb-3">Homepage Vertical Cards</h2>
                        <label class="form-label">Cards JSON</label>
                        <textarea name="home_value_cards" rows="12" class="form-control" placeholder='[{\"title_primary\":\"Flexibility\",\"title_secondary\":\"That Fits You\",\"text\":\"...\",\"icon\":\"bi-calendar-check\",\"cta_label\":\"Apply\",\"cta_link\":\"programmes\"}]'><?= e($settings['home_value_cards'] ?? '') ?></textarea>
                        <small class="text-muted">Use Bootstrap Icons classes like <code>bi-heart-pulse</code>, <code>bi-people</code>, <code>bi-award</code>.</small>
                    </div>

                    <div class="soft-card p-4 settings-card settings-card-home settings-card-home-testimonials" data-settings-section="home">
                        <h2 class="h6 text-uppercase text-muted mb-3">Homepage Testimonials</h2>
                        <div class="mb-3">
                            <label class="form-label">Manage Testimonials</label>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/list/testimonials')) ?>"><i class="bi bi-people me-1"></i>Manage Testimonials</a>
                            <small class="text-muted d-block mt-1">Add, edit, reorder, and toggle visibility for each testimonial.</small>
                        </div>
                        <hr class="my-3">
                        <h3 class="h6 text-uppercase text-muted mb-3">Testimonial Appearance</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Template Style</label>
                                <select name="testimonial_template" class="form-select">
                                    <?php $currentTemplate = $settings['testimonial_template'] ?? 'carousel'; ?>
                                    <option value="carousel" <?= $currentTemplate === 'carousel' ? 'selected' : '' ?>>Carousel (Classic)</option>
                                    <option value="cards" <?= $currentTemplate === 'cards' ? 'selected' : '' ?>>Card Grid</option>
                                    <option value="minimal" <?= $currentTemplate === 'minimal' ? 'selected' : '' ?>>Minimal Quotes</option>
                                </select>
                                <small class="text-muted">
                                    <strong>Carousel:</strong> One at a time with navigation arrows.<br>
                                    <strong>Card Grid:</strong> Multiple cards in a responsive grid.<br>
                                    <strong>Minimal Quotes:</strong> Clean text-focused layout without photos.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Card Style</label>
                                <select name="testimonial_card_style" class="form-select">
                                    <?php $currentCardStyle = $settings['testimonial_card_style'] ?? 'centered'; ?>
                                    <option value="centered" <?= $currentCardStyle === 'centered' ? 'selected' : '' ?>>Centered (Avatar top)</option>
                                    <option value="left" <?= $currentCardStyle === 'left' ? 'selected' : '' ?>>Left-aligned (Avatar left)</option>
                                    <option value="bordered" <?= $currentCardStyle === 'bordered' ? 'selected' : '' ?>>Bordered Accent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Accent Color</label>
                                <input name="testimonial_accent_color" class="form-control" value="<?= e($settings['testimonial_accent_color'] ?? '#5fc7e7') ?>" placeholder="#5fc7e7">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Background Color</label>
                                <input name="testimonial_bg_color" class="form-control" value="<?= e($settings['testimonial_bg_color'] ?? '#f5f7fa') ?>" placeholder="#f5f7fa">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Autoplay Speed (ms)</label>
                                <input name="testimonial_speed" type="number" min="2000" max="15000" step="500" class="form-control" value="<?= e($settings['testimonial_speed'] ?? '5000') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Autoplay</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="testimonial_autoplay" value="1" <?= (!isset($settings['testimonial_autoplay']) || $settings['testimonial_autoplay'] === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Auto-rotate testimonials</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="soft-card p-4 settings-card settings-card-visibility" data-settings-section="visibility">
                        <h2 class="h6 text-uppercase text-muted mb-3">Visibility Controls</h2>
                        <div class="row g-2">
                            <?php foreach ($toggleItems as $k => $label): ?>
                                <?php $checked = !isset($settings[$k]) || in_array(strtolower((string)$settings[$k]), ['1', 'true', 'yes', 'on'], true); ?>
                                <div class="col-md-6">
                                    <label class="form-check-label d-flex align-items-center gap-2">
                                        <input class="form-check-input m-0" type="checkbox" name="<?= e($k) ?>" <?= $checked ? 'checked' : '' ?>>
                                        <?= e($label) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-grid gap-3">
                    <div class="soft-card p-4 settings-card settings-card-home" data-settings-section="home">
                        <h2 class="h6 text-uppercase text-muted mb-3">Social Updates</h2>
                        <div class="mb-3">
                            <label class="form-label">Manage Updates</label>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/list/social_updates')) ?>"><i class="bi bi-megaphone me-1"></i>Manage Social Updates</a>
                            <small class="text-muted d-block mt-1">Create or edit posts manually, or set up auto-fetch from Facebook/Instagram below.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section Title</label>
                            <input name="social_updates_title" class="form-control" value="<?= e($settings['social_updates_title'] ?? 'Social Updates') ?>" placeholder="Social Updates">
                        </div>

                        <hr class="my-3">
                        <h3 class="h6 text-uppercase text-muted mb-2"><i class="bi bi-arrow-repeat me-1"></i>Auto-fetch from Facebook &amp; Instagram</h3>
                        <p class="small text-muted mb-3">Posts from your Facebook Page and linked Instagram Business account will be pulled and shown automatically. Manually-created posts are kept untouched. Fetched posts refresh every cron run.</p>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="social_auto_fetch_enabled" value="1" id="autoFetchEnabled" <?= (($settings['social_auto_fetch_enabled'] ?? '1') === '1') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="autoFetchEnabled">Enable auto-fetch (cron will fetch new posts)</label>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Facebook Page ID <span class="text-muted">(numeric or @username)</span></label>
                                <input name="facebook_page_id" class="form-control" value="<?= e($settings['facebook_page_id'] ?? '') ?>" placeholder="e.g. 123456789012345">
                                <small class="text-muted">Find it at: <code>facebook.com/your-page/about</code> → "Page transparency".</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Facebook Page Access Token</label>
                                <input name="facebook_page_access_token" type="password" class="form-control" value="<?= e($settings['facebook_page_access_token'] ?? '') ?>" placeholder="EAAG..." autocomplete="new-password">
                                <small class="text-muted">Generate at <a href="https://developers.facebook.com/tools/explorer/" target="_blank" rel="noopener">Graph API Explorer</a>. Use a long-lived Page token (60 days) or System User token (no expiry). Required permissions: <code>pages_read_engagement</code>, <code>pages_show_list</code>, <code>instagram_basic</code> (for IG).</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Instagram Business Account ID <span class="text-muted">(optional)</span></label>
                                <input name="instagram_business_account_id" class="form-control" value="<?= e($settings['instagram_business_account_id'] ?? '') ?>" placeholder="e.g. 17841400000000000">
                                <small class="text-muted">Run in Graph Explorer: <code>/{your-page-id}?fields=instagram_business_account</code> to get this ID. Leave empty to skip Instagram.</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Cron Token</label>
                                <?php $cronToken = (string)($settings['social_auto_fetch_cron_token'] ?? ''); ?>
                                <input name="social_auto_fetch_cron_token" class="form-control" value="<?= e($cronToken) ?>" placeholder="auto-generated random string">
                                <small class="text-muted">Used to protect the cron URL. Set any random string (16+ chars).</small>
                            </div>
                            <?php if ($cronToken !== ''): ?>
                                <div class="col-12">
                                    <div class="alert alert-info small mb-0">
                                        <strong>Cron URL:</strong>
                                        <code><?= e(base_url('cron/social-fetch?token=' . urlencode($cronToken))) ?></code>
                                        <br>Schedule via cPanel → Cron Jobs to run every 30 minutes:
                                        <br><code>*/30 * * * * curl -s "<?= e(base_url('cron/social-fetch?token=' . urlencode($cronToken))) ?>" &gt;/dev/null</code>
                                        <?php if (!empty($settings['social_auto_fetch_last_run'])): ?>
                                            <br><strong>Last run:</strong> <?= e((string)$settings['social_auto_fetch_last_run']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success btn-sm" id="btnFetchSocialNow"><i class="bi bi-arrow-clockwise me-1"></i>Save then Fetch Now</button>
                        </div>
                        <form id="socialFetchNowForm" method="POST" action="<?= e(base_url('admin/social-fetch/run')) ?>" class="d-none"><?= csrf_field() ?></form>
                    </div>

                    <div class="soft-card p-4 settings-card settings-card-home settings-card-home-hero" data-settings-section="home">
                        <h2 class="h6 text-uppercase text-muted mb-3">Homepage Hero</h2>
                        <div class="mb-3">
                            <label class="form-label">Hero Title</label>
                            <input name="home_hero_title" class="form-control" value="<?= e($settings['home_hero_title'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero Description</label>
                            <textarea name="home_hero_description" rows="3" class="form-control"><?= e($settings['home_hero_description'] ?? '') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Primary CTA Label</label>
                                <input name="home_hero_primary_cta_label" class="form-control" value="<?= e($settings['home_hero_primary_cta_label'] ?? 'How to Apply') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Primary CTA Link (relative)</label>
                                <?php $primaryLink = (string)($settings['home_hero_primary_cta_link'] ?? 'programmes'); ?>
                                <select name="home_hero_primary_cta_link" class="form-select">
                                    <?php if (!array_key_exists($primaryLink, $ctaPageOptions)): ?>
                                        <option value="<?= e($primaryLink) ?>" selected><?= e($primaryLink) ?> (Current)</option>
                                    <?php endif; ?>
                                    <?php foreach ($ctaPageOptions as $value => $label): ?>
                                        <option value="<?= e($value) ?>" <?= $primaryLink === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Secondary CTA Label</label>
                                <input name="home_hero_secondary_cta_label" class="form-control" value="<?= e($settings['home_hero_secondary_cta_label'] ?? 'Downloads') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Secondary CTA Link (relative)</label>
                                <?php $secondaryLink = (string)($settings['home_hero_secondary_cta_link'] ?? 'about'); ?>
                                <select name="home_hero_secondary_cta_link" class="form-select">
                                    <?php if (!array_key_exists($secondaryLink, $ctaPageOptions)): ?>
                                        <option value="<?= e($secondaryLink) ?>" selected><?= e($secondaryLink) ?> (Current)</option>
                                    <?php endif; ?>
                                    <?php foreach ($ctaPageOptions as $value => $label): ?>
                                        <option value="<?= e($value) ?>" <?= $secondaryLink === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Hero Slider Images (JSON array of image paths/URLs)</label>
                            <textarea name="hero_images" rows="5" class="form-control" placeholder='[\"https://...\",\"https://...\"]'><?= e($settings['hero_images'] ?? '') ?></textarea>
                            <div class="mt-2">
                                <label class="form-label">Upload Hero Images</label>
                                <input type="file" name="hero_image_files[]" class="form-control" accept="image/png,image/jpeg,image/webp" multiple>
                                <small class="text-muted">Uploading files will replace the current hero image list and save file paths in the database.</small>
                            </div>
                        </div>
                    </div>

                    <div class="soft-card p-4 settings-card settings-card-images" data-settings-section="images">
                        <h2 class="h6 text-uppercase text-muted mb-3">Programme & Banner Images</h2>
                        <div class="mb-3">
                            <label class="form-label">Home Programme Images JSON (key: category/name, value: image URL/path)</label>
                            <textarea name="home_programme_images_json" rows="5" class="form-control" placeholder='{"Diploma":"/uploads/settings/diploma.jpg","Certificate":"/uploads/settings/certificate.jpg"}'><?= e($settings['home_programme_images_json'] ?? '') ?></textarea>
                            <div class="mt-2">
                                <label class="form-label">Upload Programme Card Images</label>
                                <input type="file" name="home_programme_image_files[]" class="form-control" accept="image/png,image/jpeg,image/webp" multiple>
                                <small class="text-muted">Uploaded images are appended into the JSON as uploaded_1, uploaded_2...</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Programme Details Page Main Image Path/URL</label>
                            <input name="programme_detail_image" class="form-control" value="<?= e($settings['programme_detail_image'] ?? '') ?>" placeholder="/uploads/settings/programme-detail.jpg">
                            <div class="mt-2">
                                <label class="form-label">Upload Programme Details Image</label>
                                <input type="file" name="programme_detail_image_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Programme Detail Sidebar Title</label>
                            <input name="programme_sidebar_title" class="form-control" value="<?= e($settings['programme_sidebar_title'] ?? 'Need Guidance?') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Programme Detail Sidebar Text</label>
                            <textarea name="programme_sidebar_text" rows="3" class="form-control"><?= e($settings['programme_sidebar_text'] ?? 'Kindly ask for a return call from our proficient consultants to have your inquiries addressed.') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Sidebar Primary Button Label</label><input name="programme_sidebar_primary_label" class="form-control" value="<?= e($settings['programme_sidebar_primary_label'] ?? 'Apply Now') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Sidebar Primary Button Link</label><input name="programme_sidebar_primary_link" class="form-control" value="<?= e($settings['programme_sidebar_primary_link'] ?? 'programmes/apply') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Sidebar Secondary Button Label</label><input name="programme_sidebar_secondary_label" class="form-control" value="<?= e($settings['programme_sidebar_secondary_label'] ?? 'Contact Registrar') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Sidebar Secondary Button Link</label><input name="programme_sidebar_secondary_link" class="form-control" value="<?= e($settings['programme_sidebar_secondary_link'] ?? 'contact-registrar') ?>"></div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Programme Detail Sidebar "Other Programmes" Title</label>
                            <input name="programme_sidebar_other_title" class="form-control" value="<?= e($settings['programme_sidebar_other_title'] ?? 'Other Programmes Offered') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Programme Detail Mosaic Images JSON</label>
                            <textarea name="programme_mosaic_images_json" rows="4" class="form-control" placeholder='["/uploads/settings/mosaic1.jpg","/uploads/settings/mosaic2.jpg"]'><?= e($settings['programme_mosaic_images_json'] ?? '') ?></textarea>
                            <div class="mt-2">
                                <label class="form-label">Upload Programme Detail Mosaic Images</label>
                                <input type="file" name="programme_mosaic_image_files[]" class="form-control" accept="image/png,image/jpeg,image/webp" multiple>
                            </div>
                        </div>
                        <div class="alert alert-info small mb-3">
                            Manage "Courses | Programmes On Offer" card images using <strong>Home Programme Images JSON</strong> or <strong>Upload Programme Card Images</strong>. These are separate from the details page image above.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Home Extra Sections JSON</label>
                            <textarea name="home_extra_sections_json" rows="5" class="form-control" placeholder='[{"title":"Scholarships","text":"Apply for support.","button_label":"Learn More","button_link":"contact","image":"/uploads/settings/section.jpg"}]'><?= e($settings['home_extra_sections_json'] ?? '') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Banner Height (px)</label><input name="banner_default_height" class="form-control" value="<?= e($settings['banner_default_height'] ?? '300') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner Home Path/URL</label><input name="banner_home" class="form-control" value="<?= e($settings['banner_home'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner Programmes Path/URL</label><input name="banner_programmes" class="form-control" value="<?= e($settings['banner_programmes'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner About Path/URL</label><input name="banner_about" class="form-control" value="<?= e($settings['banner_about'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner Contact Path/URL</label><input name="banner_contact" class="form-control" value="<?= e($settings['banner_contact'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner Events Path/URL</label><input name="banner_events" class="form-control" value="<?= e($settings['banner_events'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner Library Path/URL</label><input name="banner_library" class="form-control" value="<?= e($settings['banner_library'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Banner Media Path/URL</label><input name="banner_media" class="form-control" value="<?= e($settings['banner_media'] ?? '') ?>"></div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6"><label class="form-label">Upload Home Banner</label><input type="file" name="banner_home_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                            <div class="col-md-6"><label class="form-label">Upload Programmes Banner</label><input type="file" name="banner_programmes_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                            <div class="col-md-6"><label class="form-label">Upload About Banner</label><input type="file" name="banner_about_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                            <div class="col-md-6"><label class="form-label">Upload Contact Banner</label><input type="file" name="banner_contact_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                            <div class="col-md-6"><label class="form-label">Upload Events Banner</label><input type="file" name="banner_events_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                            <div class="col-md-6"><label class="form-label">Upload Library Banner</label><input type="file" name="banner_library_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                            <div class="col-md-6"><label class="form-label">Upload Media Banner</label><input type="file" name="banner_media_file" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
                        </div>
                    </div>

                    <div class="soft-card p-4 settings-card" data-settings-section="about">
                        <h2 class="h6 text-uppercase text-muted mb-3">About Page Content</h2>
                        <div class="mb-3">
                            <label class="form-label">About Title</label>
                            <input name="about_title" class="form-control" value="<?= e($settings['about_title'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">About Intro</label>
                            <textarea name="about_intro" rows="6" class="form-control rich-editor"><?= e($settings['about_intro'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mission</label>
                            <textarea name="about_mission" rows="4" class="form-control rich-editor"><?= e($settings['about_mission'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vision</label>
                            <textarea name="about_vision" rows="4" class="form-control rich-editor"><?= e($settings['about_vision'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Core Values (comma-separated)</label>
                            <input name="about_values" class="form-control" value="<?= e($settings['about_values'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Why Choose Us (use | between items)</label>
                            <textarea name="about_why_choose" rows="3" class="form-control"><?= e($settings['about_why_choose'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Snapshot Stats (format: value|label, separated by commas)</label>
                            <input name="about_stats" class="form-control" value="<?= e($settings['about_stats'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">What Makes Us Different (use | between items)</label>
                            <textarea name="about_differentiators" rows="3" class="form-control"><?= e($settings['about_differentiators'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Programmes List (use | between items)</label>
                            <textarea name="about_programmes" rows="3" class="form-control"><?= e($settings['about_programmes'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commitment Text</label>
                            <textarea name="about_commitment" rows="6" class="form-control rich-editor"><?= e($settings['about_commitment'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Short Tagline</label>
                            <textarea name="about_short_tagline" rows="4" class="form-control rich-editor"><?= e($settings['about_short_tagline'] ?? '') ?></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">About CTA 1 Label</label>
                                <input name="about_cta_primary_label" class="form-control" value="<?= e($settings['about_cta_primary_label'] ?? 'View Programmes') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">About CTA 1 Link (relative)</label>
                                <input name="about_cta_primary_link" class="form-control" value="<?= e($settings['about_cta_primary_link'] ?? 'programmes') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">About CTA 2 Label</label>
                                <input name="about_cta_secondary_label" class="form-control" value="<?= e($settings['about_cta_secondary_label'] ?? 'Contact Admissions') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">About CTA 2 Link (relative)</label>
                                <input name="about_cta_secondary_link" class="form-control" value="<?= e($settings['about_cta_secondary_link'] ?? 'contact') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="soft-card p-4 settings-card" data-settings-section="principal">
                        <h2 class="h6 text-uppercase text-muted mb-3">Principal Page Content</h2>
                        <div class="mb-3">
                            <label class="form-label">Principal Name</label>
                            <input name="principal_name" class="form-control" value="<?= e($settings['principal_name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Principal Title</label>
                            <input name="principal_title" class="form-control" value="<?= e($settings['principal_title'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Principal Image Path/URL</label>
                            <input name="principal_image" class="form-control" value="<?= e($settings['principal_image'] ?? '') ?>" placeholder="https://...">
                            <div class="mt-2">
                                <label class="form-label">Upload Principal Image</label>
                                <input type="file" name="principal_image_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                                <small class="text-muted">If you upload an image, it will be stored and this setting will be updated automatically.</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Principal Message</label>
                            <textarea name="principal_message" rows="6" class="form-control rich-editor"><?= e($settings['principal_message'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vision Priorities (use | between items)</label>
                            <textarea name="principal_vision_points" rows="3" class="form-control"><?= e($settings['principal_vision_points'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Focus Areas (use | between items)</label>
                            <textarea name="principal_focus_areas" rows="3" class="form-control"><?= e($settings['principal_focus_areas'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Closing Signature Line</label>
                            <input name="principal_signature" class="form-control" value="<?= e($settings['principal_signature'] ?? '') ?>">
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Principal Email</label>
                            <input name="principal_email" class="form-control" value="<?= e($settings['principal_email'] ?? 'principal@stmarysmchmcollege.ac.ke') ?>">
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label">Facebook Link</label>
                                <input name="principal_facebook" class="form-control" value="<?= e($settings['principal_facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">X (Twitter) Link</label>
                                <input name="principal_x" class="form-control" value="<?= e($settings['principal_x'] ?? '') ?>" placeholder="https://x.com/...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">LinkedIn Link</label>
                                <input name="principal_linkedin" class="form-control" value="<?= e($settings['principal_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/...">
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Registrar Email</label>
                                <input name="registrar_email" class="form-control" value="<?= e($settings['registrar_email'] ?? 'registrar@stmarysmchmcollege.ac.ke') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Registrar Image Path/URL</label>
                                <input name="registrar_image" class="form-control" value="<?= e($settings['registrar_image'] ?? '') ?>" placeholder="/uploads/settings/registrar.jpg or https://...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</section>
<div class="modal fade" id="reply-template-preview-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Reply Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="reply-template-preview-frame" title="Reply template preview" style="width:100%;height:75vh;border:0;"></iframe>
            </div>
        </div>
    </div>
</div>
<script>
// Fetch-now button and sessionStorage trigger — independent of tabs
document.addEventListener('DOMContentLoaded', function () {
    const fetchBtn = document.getElementById('btnFetchSocialNow');
    if (fetchBtn) {
        fetchBtn.addEventListener('click', function () {
            try { sessionStorage.setItem('triggerSocialFetch', '1'); } catch (ex) {}
            const mainForm = document.getElementById('settings-form');
            if (mainForm) { mainForm.submit(); }
        });
    }
    try {
        if (sessionStorage.getItem('triggerSocialFetch') === '1') {
            sessionStorage.removeItem('triggerSocialFetch');
            const fetchForm = document.getElementById('socialFetchNowForm');
            if (fetchForm) { setTimeout(function () { fetchForm.submit(); }, 200); }
        }
    } catch (ex) {}
});

document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('[data-settings-tab]');
    const cards = document.querySelectorAll('[data-settings-section]');
    if (!tabs.length || !cards.length) return;
    tabs.forEach((tab) => {
        tab.addEventListener('click', function () {
            const target = tab.getAttribute('data-settings-tab') || 'general';
            tabs.forEach((btn) => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            tab.classList.remove('btn-outline-primary');
            tab.classList.add('btn-primary');
            cards.forEach((card) => {
                const groups = (card.getAttribute('data-settings-section') || '').split(/\s+/);
                const visible = groups.includes(target);
                card.style.display = visible ? '' : 'none';
            });
        });
    });
    const syncPermissionInput = function(inputId){
        const input = document.getElementById(inputId);
        if (!input) return;
        const checked = Array.from(document.querySelectorAll('.permission-checkbox[data-target-input="' + inputId + '"]:checked'))
            .map((el) => (el.value || '').trim())
            .filter(Boolean);
        input.value = checked.join(',');
    };
    document.querySelectorAll('.permission-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', function(){
            const inputId = checkbox.getAttribute('data-target-input');
            if (inputId) syncPermissionInput(inputId);
        });
    });

    const previewBtn = document.getElementById('preview-reply-template-btn');
    const previewFrame = document.getElementById('reply-template-preview-frame');
    const previewModalEl = document.getElementById('reply-template-preview-modal');
    const buildPreviewHtml = function () {
        const getVal = function(name, fallback) {
            const input = document.querySelector('[name="' + name + '"]');
            const raw = input ? String(input.value || '').trim() : '';
            return raw !== '' ? raw : fallback;
        };
        const esc = function (value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };
        const sanitizeHex = function (value, fallback) {
            return /^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/.test(value) ? value : fallback;
        };

        const appName = 'College';
        const heading = getVal('admin_reply_email_heading', 'Thank you for your email');
        const subheading = getVal('admin_reply_email_subheading', 'Here is our response from ' + appName);
        const logoUrl = getVal('admin_reply_email_logo_url', <?= json_encode(base_url('assets/images/logo.png')) ?>);
        const footerText = getVal('admin_reply_email_footer_text', 'We value your message and are always ready to assist.');
        const contactEmail = getVal('email', 'contact@stmarysmchmcollege.ac.ke');
        const contactPhone = getVal('phone', '+254 791 309011');
        const contactPhone2 = '0101 711 499';
        const bgColor = sanitizeHex(getVal('admin_reply_email_bg_color', '#ffffff'), '#ffffff');
        const cardColor = sanitizeHex(getVal('admin_reply_email_card_color', '#f5f6fb'), '#f5f6fb');
        const accentColor = sanitizeHex(getVal('admin_reply_email_accent_color', '#5fc7e7'), '#5fc7e7');
        const footerBgColor = sanitizeHex(getVal('admin_reply_email_footer_bg_color', '#2c3653'), '#2c3653');

        return '<!doctype html><html><body style="margin:0;padding:24px;background:' + esc(bgColor) + ';">'
            + '<div style="max-width:760px;margin:0 auto;padding:0 12px;">'
            + '<div style="background:' + esc(cardColor) + ';border-top:4px solid ' + esc(accentColor) + ';border-bottom:4px solid ' + esc(accentColor) + ';">'
            + '<div style="padding:26px 34px 20px;text-align:center;">'
            + '<div style="margin:0 auto 14px;display:flex;align-items:center;justify-content:center;overflow:hidden;">'
            + '<img src="' + esc(logoUrl) + '" alt="' + esc(appName) + ' logo" style="display:block;margin:0 auto;width:64px;height:64px;object-fit:contain;">'
            + '</div>'
            + '<h1 style="margin:0;color:#1f2a44;font-family:Arial,sans-serif;font-size:42px;line-height:1.1;">' + esc(heading) + '</h1>'
            + '<p style="margin:8px 0 0;color:#6e7381;font-family:Arial,sans-serif;font-size:14px;">' + esc(subheading) + '</p>'
            + '</div>'
            + '<div style="padding:0 34px 28px;">'
            + '<table role="presentation" style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;color:#1f2a44;">'
            + '<tr><td style="padding:10px 0;border-top:3px solid #2e3448;border-bottom:1px solid #2e3448;font-weight:700;width:110px;">Title</td><td style="padding:10px 0;border-top:3px solid #2e3448;border-bottom:1px solid #2e3448;">Sample Reply Subject</td></tr>'
            + '<tr><td style="padding:10px 0;border-bottom:2px solid #2e3448;font-weight:700;">Regarding</td><td style="padding:10px 0;border-bottom:2px solid #2e3448;">Original enquiry subject</td></tr>'
            + '</table>'
            + '<div style="margin-top:18px;font-family:Arial,sans-serif;color:#20293f;font-size:15px;line-height:1.65;">'
            + '<p style="margin:0 0 12px;">Hi, Student Name</p>'
            + '<p style="margin:0 0 12px;">This is a preview of how your admin reply email will look to recipients.</p>'
            + '<p style="margin:16px 0 0;">Thank you,<br>' + esc(appName) + '</p>'
            + '</div>'
            + '</div>'
            + '<div style="background:' + esc(footerBgColor) + ';padding:18px 34px;color:#b9e7ff;font-family:Arial,sans-serif;font-size:13px;line-height:1.6;text-align:center;">'
            + '<strong style="color:#fff;">' + esc(appName) + '</strong><br>'
            + '<a href="mailto:' + esc(contactEmail) + '" style="color:#b9e7ff;text-decoration:none;">' + esc(contactEmail) + '</a>'
            + ' | <a href="tel:' + esc(contactPhone) + '" style="color:#b9e7ff;text-decoration:none;">' + esc(contactPhone) + '</a>'
            + ' | <a href="tel:' + esc(contactPhone2) + '" style="color:#b9e7ff;text-decoration:none;">' + esc(contactPhone2) + '</a><br>' + esc(footerText)
            + '</div>'
            + '</div></div></body></html>';
    };

    if (previewBtn && previewFrame && previewModalEl && window.bootstrap && window.bootstrap.Modal) {
        const modal = new window.bootstrap.Modal(previewModalEl);
        previewBtn.addEventListener('click', function () {
            const doc = previewFrame.contentWindow && previewFrame.contentWindow.document;
            if (!doc) return;
            doc.open();
            doc.write(buildPreviewHtml());
            doc.close();
            modal.show();
        });
    }

    if (tabs[0]) {
        tabs[0].click();
    }

});
</script>
