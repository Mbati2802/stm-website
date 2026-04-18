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
?>

<section class="py-4">
    <div class="admin-content-wrap">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1">Site Settings</h1>
                <p class="text-muted mb-0">Manage global content, homepage sections, and page visibility.</p>
            </div>
            <button form="settings-form" class="btn settings-save-button"><i class="bi bi-save me-2"></i>Save Settings</button>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>

        <form id="settings-form" method="POST" enctype="multipart/form-data">
            <div class="settings-section-card">
                <h6><i class="bi bi-sliders me-2"></i>General Settings</h6>
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

            <div class="settings-section-card" data-settings-section="home">
                <h6><i class="bi bi-layout-three-columns me-2"></i>Homepage Vertical Cards</h6>
                <label class="form-label">Cards JSON</label>
                <textarea name="home_value_cards" rows="12" class="form-control" placeholder='[{\"title_primary\":\"Flexibility\",\"title_secondary\":\"That Fits You\",\"text\":\"...\",\"icon\":\"bi-calendar-check\",\"cta_label\":\"Apply\",\"cta_link\":\"programmes\"}]'><?= e($settings['home_value_cards'] ?? '') ?></textarea>
                <small class="text-muted">Use Bootstrap Icons classes like <code>bi-heart-pulse</code>, <code>bi-people</code>, <code>bi-award</code>.</small>
            </div>

            <div class="settings-section-card" data-settings-section="home">
                <h6><i class="bi bi-eye me-2"></i>Visibility Controls</h6>
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

            <div class="settings-section-card">
                <h6><i class="bi bi-stars me-2"></i>Homepage Hero</h6>
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
                        <input name="home_hero_primary_cta_link" class="form-control" value="<?= e($settings['home_hero_primary_cta_link'] ?? 'programmes') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secondary CTA Label</label>
                        <input name="home_hero_secondary_cta_label" class="form-control" value="<?= e($settings['home_hero_secondary_cta_label'] ?? 'Downloads') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secondary CTA Link (relative)</label>
                        <input name="home_hero_secondary_cta_link" class="form-control" value="<?= e($settings['home_hero_secondary_cta_link'] ?? 'about') ?>">
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

            <div class="settings-section-card">
                <h6><i class="bi bi-images me-2"></i>Programme Card Images</h6>
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Images for course cards on homepage. Map categories or specific programme names to image URLs.</small>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Diploma Category Image</label>
                        <input type="text" name="programme_image_diploma" class="form-control" value="<?= e($settings['home_programme_images_json']['Diploma'] ?? '') ?>" placeholder="/uploads/settings/diploma.jpg">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Certificate Category Image</label>
                        <input type="text" name="programme_image_certificate" class="form-control" value="<?= e($settings['home_programme_images_json']['Certificate'] ?? '') ?>" placeholder="/uploads/settings/certificate.jpg">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Short Course Category Image</label>
                        <input type="text" name="programme_image_short_course" class="form-control" value="<?= e($settings['home_programme_images_json']['Short Course'] ?? '') ?>" placeholder="/uploads/settings/short-course.jpg">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Artisan Category Image</label>
                        <input type="text" name="programme_image_artisan" class="form-control" value="<?= e($settings['home_programme_images_json']['Artisan'] ?? '') ?>" placeholder="/uploads/settings/artisan.jpg">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Custom Programme Images (JSON for specific programmes)</label>
                    <textarea name="home_programme_images_json" rows="4" class="form-control" placeholder='{"Nursing":"/uploads/settings/nursing.jpg","Clinical Medicine":"/uploads/settings/clinical.jpg"}'><?= e($settings['home_programme_images_json'] ?? '') ?></textarea>
                    <small class="text-muted">Override category images for specific programmes by name.</small>
                </div>
                <div class="mt-2">
                    <label class="form-label">Upload Programme Card Images</label>
                    <input type="file" name="home_programme_image_files[]" class="form-control" accept="image/png,image/jpeg,image/webp" multiple>
                    <small class="text-muted">Uploaded images are appended as uploaded_1, uploaded_2...</small>
                </div>
            </div>

            <div class="settings-section-card">
                <h6><i class="bi bi-file-earmark-image me-2"></i>Programme Details Page</h6>
                <div class="mb-3">
                    <label class="form-label">Programme Details Page Main Image Path/URL</label>
                    <input name="programme_detail_image" class="form-control" value="<?= e($settings['programme_detail_image'] ?? '') ?>" placeholder="/uploads/settings/programme-detail.jpg">
                    <div class="mt-2">
                        <label class="form-label">Upload Programme Details Image</label>
                        <input type="file" name="programme_detail_image_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                </div>
            </div>

            <div class="settings-section-card">
                <h6><i class="bi bi-layers me-2"></i>Home Extra Sections</h6>
                <div class="mb-3">
                    <label class="form-label">Home Extra Sections JSON</label>
                    <textarea name="home_extra_sections_json" rows="5" class="form-control" placeholder='[{"title":"Scholarships","text":"Apply for support.","button_label":"Learn More","button_link":"contact","image":"/uploads/settings/section.jpg"}]'><?= e($settings['home_extra_sections_json'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="settings-section-card">
                <h6><i class="bi bi-image me-2"></i>Page Banner Images</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Banner Height (px)</label>
                        <input name="banner_default_height" class="form-control" value="<?= e($settings['banner_default_height'] ?? '300') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Home Path/URL</label>
                        <input name="banner_home" class="form-control" value="<?= e($settings['banner_home'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Programmes Path/URL</label>
                        <input name="banner_programmes" class="form-control" value="<?= e($settings['banner_programmes'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner About Path/URL</label>
                        <input name="banner_about" class="form-control" value="<?= e($settings['banner_about'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Contact Path/URL</label>
                        <input name="banner_contact" class="form-control" value="<?= e($settings['banner_contact'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Events Path/URL</label>
                        <input name="banner_events" class="form-control" value="<?= e($settings['banner_events'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Library Path/URL</label>
                        <input name="banner_library" class="form-control" value="<?= e($settings['banner_library'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banner Media Path/URL</label>
                        <input name="banner_media" class="form-control" value="<?= e($settings['banner_media'] ?? '') ?>">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Upload Home Banner</label>
                        <input type="file" name="banner_home_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Programmes Banner</label>
                        <input type="file" name="banner_programmes_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload About Banner</label>
                        <input type="file" name="banner_about_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Contact Banner</label>
                        <input type="file" name="banner_contact_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Events Banner</label>
                        <input type="file" name="banner_events_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Library Banner</label>
                        <input type="file" name="banner_library_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Media Banner</label>
                        <input type="file" name="banner_media_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                </div>
            </div>

            <div class="settings-section-card">
                <h6><i class="bi bi-info-circle me-2"></i>About Page Content</h6>
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

            <div class="settings-section-card">
                <h6><i class="bi bi-person-badge me-2"></i>Principal Page Content</h6>
                <div class="mb-3">
                    <label class="form-label">Principal Name</label>
                    <input name="principal_name" class="form-control" value="<?= e($settings['principal_name'] ?? '') ?>">
                </div>
                <div class="mb-3">
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
        </form>
    </div>
</section>
