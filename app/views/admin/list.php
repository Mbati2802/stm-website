<section class="py-4">
    <div class="admin-content-wrap">
        <?php
        $columnClassMap = [
            'id' => 'col-xs',
            'name' => 'col-md',
            'abbreviation' => 'col-sm',
            'title' => 'col-lg',
            'email' => 'col-lg',
            'phone' => 'col-sm',
            'slug' => 'col-md',
            'category' => 'col-sm',
            'summary' => 'col-md',
            'body' => 'col-md',
            'message' => 'col-md',
            'description' => 'col-md',
            'content' => 'col-md',
            'answer' => 'col-md',
            'question' => 'col-lg',
            'created_at' => 'col-sm',
            'updated_at' => 'col-sm',
        ];
        // Columns that should have text truncated
        $truncateColumns = ['summary', 'body', 'message', 'description', 'content', 'answer', 'question'];
        // Maximum characters to display before truncating
        $maxLength = 60;
        // Columns to hide completely from the table (reduce row height, minimize scroll)
        $hiddenColumns = ['body', 'image_path', 'image', 'file_path', 'featured_image', 'social_updates_embed'];
        // Entity-specific hidden columns
        $entityHiddenColumns = [
            'portal_courses' => ['programme_id', 'teacher_id', 'created_at', 'description'],
        ];
        $entityDisplayNames = [
            'portal_courses' => 'Portal Units',
            'course_grades' => 'Unit Grades',
            'course_assignments' => 'Assignments',
        ];
        $columnHeaderMap = [
            'portal_courses' => [
                'code' => 'Unit Code',
                'title' => 'Unit Title',
                'course_id' => 'Unit',
                'programme_abbreviation' => 'Programme',
                'teacher_name' => 'Teacher',
            ],
            'course_grades' => [
                'course_id' => 'Unit',
            ],
            'course_assignments' => [
                'course_id' => 'Unit',
            ],
            'study_materials' => [
                'course_id' => 'Unit',
            ],
        ];
        $entitySettings = $settings ?? [];
        ?>
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0 text-capitalize">Manage <?= e($entityDisplayNames[$entity] ?? str_replace('_',' ',$entity)) ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($entity === 'testimonials'): ?>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#entitySettingsModal"><i class="bi bi-gear me-1"></i>Appearance Settings</button>
                    <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('testimonials')) ?>" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View Page</a>
                <?php elseif ($entity === 'social_updates'): ?>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#entitySettingsModal"><i class="bi bi-gear me-1"></i>Social Settings</button>
                <?php endif; ?>
                <?php if (Auth::canManageEntity($entity)): ?>
                    <a class="btn btn-primary" href="<?= e(base_url('admin/create/' . $entity)) ?>"><i class="bi bi-plus-circle me-1"></i>Add New</a>
                <?php endif; ?>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('admin')) ?>"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
            </div>
        </div>
        <?php if ($msg=flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg=flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <div class="table-responsive admin-table-card">
            <table class="table align-middle admin-table">
                <thead>
                <tr>
                    <?php if(!empty($rows)): foreach(array_keys($rows[0]) as $h): ?>
                        <?php
                        $headerClass = $columnClassMap[(string)$h] ?? 'col-md';
                        $skipColumn = (($entity === 'programmes') && in_array((string)$h, ['description', 'slug'], true))
                            || in_array((string)$h, $hiddenColumns, true)
                            || in_array((string)$h, $entityHiddenColumns[$entity] ?? [], true);
                        $headerText = $columnHeaderMap[$entity][(string)$h] ?? $h;
                        ?>
                        <?php if (!$skipColumn): ?>
                        <th class="<?= e($headerClass) ?>"><?= e($headerText) ?></th>
                        <?php endif; ?>
                    <?php endforeach; endif; ?>
                    <?php if ($entity === 'portal_courses'): ?>
                        <th class="col-sm"><?= e($columnHeaderMap[$entity]['programme_abbreviation'] ?? 'Programme') ?></th>
                        <th class="col-sm"><?= e($columnHeaderMap[$entity]['teacher_name'] ?? 'Teacher') ?></th>
                    <?php endif; ?>
                    <th class="col-sm">Visibility</th>
                    <th class="col-actions">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($rows as $row): ?>
                    <tr>
                    <?php foreach($row as $k => $v): ?>
                        <?php
                        $key = (string)$k;
                        $cellClass = $columnClassMap[$key] ?? 'col-md';
                        $fullValue = (string)$v;
                        $skipColumn = (($entity === 'programmes') && in_array($key, ['description', 'slug'], true))
                            || in_array($key, $hiddenColumns, true)
                            || in_array($key, $entityHiddenColumns[$entity] ?? [], true);
                        if ($skipColumn) continue;
                        ?>
                        <td class="<?= e($cellClass) ?>" title="<?= e($key === 'password' ? '' : $fullValue) ?>">
                            <?php if ((string)$k === 'password'): ?>
                                ••••••••
                            <?php elseif ($entity === 'programmes' && $key === 'abbreviation'): ?>
                                <strong><?= e((string)($row['abbreviation'] ?? '')) ?></strong>
                            <?php elseif ($entity === 'programmes' && $key === 'description'): ?>
                                <?= e(implode(' ', array_slice(explode(' ', $fullValue), 0, 3))) ?>...
                            <?php elseif (in_array($entity, ['course_grades', 'course_assignments', 'study_materials']) && $key === 'course_id' && isset($courses)): ?>
                                <?php foreach (($courses ?? []) as $course): ?>
                                    <?php if ((string)$course['id'] === (string)$v): ?>
                                        <?= e(trim((string)($course['code'] ?? '') . ' - ' . (string)$course['title'])) ?>
                                        <?php break; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (!isset($course)): ?>
                                    <?= e($fullValue) ?>
                                <?php endif; ?>
                            <?php elseif (in_array($key, $truncateColumns, true)):
                                // Strip HTML tags and truncate
                                $plainText = strip_tags($fullValue);
                                $plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                if (strlen($plainText) > $maxLength): ?>
                                    <span class="text-truncate-ellipsis" title="<?= e($plainText) ?>"><?= e(substr($plainText, 0, $maxLength)) ?>...</span>
                                <?php else: ?>
                                    <?= e($plainText) ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <?= e($fullValue) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if ($entity === 'portal_courses'): ?>
                        <td class="col-sm">
                            <?php
                            // Get all programme IDs linked to this course (many-to-many)
                            $linkedProgrammeIds = [];
                            foreach (($courseProgrammes ?? []) as $cp) {
                                if ((string)$cp['portal_course_id'] === (string)$row['id']) {
                                    $linkedProgrammeIds[] = $cp['programme_id'];
                                }
                            }
                            // Fallback to legacy single programme_id
                            if (empty($linkedProgrammeIds) && !empty($row['programme_id'])) {
                                $linkedProgrammeIds[] = $row['programme_id'];
                            }
                            // Build abbreviations list
                            $abbreviations = [];
                            foreach ($linkedProgrammeIds as $progId) {
                                foreach (($programmes ?? []) as $prog) {
                                    if ((string)$prog['id'] === (string)$progId) {
                                        $abbrev = $prog['abbreviation'] ?? substr($prog['name'], 0, 10) ?? '?';
                                        $abbreviations[] = e($abbrev);
                                        break;
                                    }
                                }
                            }
                            echo !empty($abbreviations) ? implode(', ', $abbreviations) : '-';
                            ?>
                        </td>
                        <td class="col-sm">
                            <?php
                            $teacherId = $row['teacher_id'] ?? 0;
                            $teacherName = '-';
                            foreach (($teachers ?? []) as $teacher) {
                                if ((string)$teacher['id'] === (string)$teacherId) {
                                    $teacherName = $teacher['name'] ?? '-';
                                    break;
                                }
                            }
                            echo e($teacherName);
                            ?>
                        </td>
                    <?php endif; ?>
                        <td class="col-sm"><?php
                            $hiddenIds = $hiddenIds ?? [];
                            // For entities with is_visible column, use that as source of truth
                            if (in_array($entity, ['testimonials', 'social_updates'], true) && array_key_exists('is_visible', $row)) {
                                $isVisible = (int)$row['is_visible'] === 1;
                            } else {
                                $isVisible = !in_array((int)$row['id'], $hiddenIds, true);
                            }
                        ?><?php if (Auth::canManageEntity($entity)): ?><a class="btn btn-sm btn-action-toggle" href="<?= e(base_url('admin/toggle/' . $entity . '/' . $row['id'])) ?>" title="<?= $isVisible ? 'Visible' : 'Hidden' ?>"><?= $isVisible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>' ?></a><?php endif; ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <?php if (Auth::canManageEntity($entity)): ?>
                                    <a class="btn btn-sm btn-action-edit" href="<?= e(base_url('admin/edit/' . $entity . '/' . $row['id'])) ?>" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <a class="btn btn-sm btn-action-delete" href="<?= e(base_url('admin/delete/' . $entity . '/' . $row['id'])) ?>" onclick="return confirm('Delete item?')" title="Delete"><i class="bi bi-trash"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php if ($entity === 'testimonials'): ?>
<div class="modal fade" id="entitySettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="<?= e(base_url('admin/settings/partial')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="_redirect" value="admin/list/testimonials">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-palette me-1"></i>Testimonial Appearance Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Template Style</label>
                            <select name="testimonial_template" class="form-select">
                                <?php $ct = $entitySettings['testimonial_template'] ?? 'carousel'; ?>
                                <option value="carousel" <?= $ct === 'carousel' ? 'selected' : '' ?>>Carousel (Classic)</option>
                                <option value="cards" <?= $ct === 'cards' ? 'selected' : '' ?>>Card Grid</option>
                                <option value="minimal" <?= $ct === 'minimal' ? 'selected' : '' ?>>Minimal Quotes</option>
                            </select>
                            <small class="text-muted"><strong>Carousel:</strong> One at a time with arrows. <strong>Grid:</strong> Multiple cards. <strong>Minimal:</strong> Text-only quotes.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Card Style</label>
                            <select name="testimonial_card_style" class="form-select">
                                <?php $cs = $entitySettings['testimonial_card_style'] ?? 'centered'; ?>
                                <option value="centered" <?= $cs === 'centered' ? 'selected' : '' ?>>Centered (Avatar top)</option>
                                <option value="left" <?= $cs === 'left' ? 'selected' : '' ?>>Left-aligned (Avatar left)</option>
                                <option value="bordered" <?= $cs === 'bordered' ? 'selected' : '' ?>>Bordered Accent</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="carouselOpts">
                            <label class="form-label fw-semibold">Items per Slide (Carousel)</label>
                            <select name="testimonial_items_per_slide" class="form-select">
                                <?php $ips = (int)($entitySettings['testimonial_items_per_slide'] ?? 1); ?>
                                <option value="1" <?= $ips === 1 ? 'selected' : '' ?>>1</option>
                                <option value="2" <?= $ips === 2 ? 'selected' : '' ?>>2</option>
                                <option value="3" <?= $ips === 3 ? 'selected' : '' ?>>3</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="carouselEffect">
                            <label class="form-label fw-semibold">Slide Effect (Carousel)</label>
                            <select name="testimonial_slide_effect" class="form-select">
                                <?php $se = $entitySettings['testimonial_slide_effect'] ?? 'slide'; ?>
                                <option value="slide" <?= $se === 'slide' ? 'selected' : '' ?>>Slide</option>
                                <option value="fade" <?= $se === 'fade' ? 'selected' : '' ?>>Fade</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="gridOpts">
                            <label class="form-label fw-semibold">Grid Columns (Card Grid/Page)</label>
                            <select name="testimonial_grid_count" class="form-select">
                                <?php $gc = (int)($entitySettings['testimonial_grid_count'] ?? 3); ?>
                                <option value="2" <?= $gc === 2 ? 'selected' : '' ?>>2 columns</option>
                                <option value="3" <?= $gc === 3 ? 'selected' : '' ?>>3 columns</option>
                                <option value="4" <?= $gc === 4 ? 'selected' : '' ?>>4 columns</option>
                                <option value="5" <?= $gc === 5 ? 'selected' : '' ?>>5 columns</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Accent Color</label>
                            <input name="testimonial_accent_color" type="color" class="form-control form-control-color" value="<?= e($entitySettings['testimonial_accent_color'] ?? '#5fc7e7') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Background Color</label>
                            <input name="testimonial_bg_color" type="color" class="form-control form-control-color" value="<?= e($entitySettings['testimonial_bg_color'] ?? '#f5f7fa') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Autoplay Speed (ms)</label>
                            <input name="testimonial_speed" type="number" min="2000" max="15000" step="500" class="form-control" value="<?= e($entitySettings['testimonial_speed'] ?? '5000') ?>">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="testimonial_autoplay" value="1" <?= (!isset($entitySettings['testimonial_autoplay']) || $entitySettings['testimonial_autoplay'] === '1') ? 'checked' : '' ?>>
                                <label class="form-check-label">Auto-rotate testimonials (carousel)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Appearance</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($entity === 'social_updates'): ?>
<div class="modal fade" id="entitySettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-megaphone me-1"></i>Social Updates Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="socialSettingsTabs">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#socialTabConfig">Configuration</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#socialTabAppearance">Appearance</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#socialTabDebug">Debug &amp; Preview</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="socialTabConfig">
                        <form id="socialSettingsForm" method="POST" action="<?= e(base_url('admin/settings/partial')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_redirect" value="admin/list/social_updates">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Section Title</label>
                                    <input name="social_updates_title" class="form-control" value="<?= e($entitySettings['social_updates_title'] ?? 'Social Updates') ?>" placeholder="Social Updates">
                                </div>
                                <div class="col-12"><hr class="my-1"><h6 class="text-uppercase text-muted mt-2 mb-0"><i class="bi bi-arrow-repeat me-1"></i>Auto-fetch from Facebook &amp; Instagram</h6><p class="small text-muted mb-2">Posts from your Facebook Page and linked Instagram account will be pulled automatically.</p></div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="social_auto_fetch_enabled" value="1" <?= (($entitySettings['social_auto_fetch_enabled'] ?? '1') === '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label">Enable auto-fetch</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Facebook Page ID</label>
                                    <input name="facebook_page_id" class="form-control" value="<?= e($entitySettings['facebook_page_id'] ?? '') ?>" placeholder="e.g. 123456789012345">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Facebook Page Access Token</label>
                                    <input name="facebook_page_access_token" type="password" class="form-control" value="<?= e($entitySettings['facebook_page_access_token'] ?? '') ?>" placeholder="EAAG..." autocomplete="new-password">
                                    <small class="text-muted">Generate at <a href="https://developers.facebook.com/tools/explorer/" target="_blank" rel="noopener">Graph API Explorer</a>. Permissions: <code>pages_read_engagement</code>, <code>pages_show_list</code>, <code>instagram_basic</code>.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Instagram Business Account ID <span class="text-muted">(optional)</span></label>
                                    <input name="instagram_business_account_id" class="form-control" value="<?= e($entitySettings['instagram_business_account_id'] ?? '') ?>" placeholder="e.g. 17841400000000000">
                                    <small class="text-muted">This is <strong>not</strong> the same as the Facebook Page ID. Find it via Graph API: <code>GET /{page-id}?fields=instagram_business_account</code>. Leave blank to skip Instagram.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Cron Token</label>
                                    <?php $cronToken = (string)($entitySettings['social_auto_fetch_cron_token'] ?? ''); ?>
                                    <input name="social_auto_fetch_cron_token" class="form-control" value="<?= e($cronToken) ?>" placeholder="random string 16+ chars">
                                </div>
                                <?php if ($cronToken !== ''): ?>
                                    <div class="col-12">
                                        <div class="alert alert-info small mb-0">
                                            <strong>Cron URL:</strong> <code><?= e(base_url('cron/social-fetch?token=' . urlencode($cronToken))) ?></code>
                                            <br>cPanel: <code>*/30 * * * * curl -s "<?= e(base_url('cron/social-fetch?token=' . urlencode($cronToken))) ?>" &gt;/dev/null</code>
                                            <?php if (!empty($entitySettings['social_auto_fetch_last_run'])): ?>
                                                <br><strong>Last run:</strong> <?= e((string)$entitySettings['social_auto_fetch_last_run']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-12 d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                                    <button type="button" class="btn btn-success" id="btnFetchNow"><i class="bi bi-arrow-clockwise me-1"></i>Fetch Now</button>
                                </div>
                            </div>
                        </form>
                        <!-- Fetch Now form OUTSIDE the settings form to avoid nesting -->
                        <form id="socialFetchNowForm" method="POST" action="<?= e(base_url('admin/social-fetch/run')) ?>" style="display:none;"><?= csrf_field() ?></form>
                    </div>
                    <div class="tab-pane fade" id="socialTabAppearance">
                        <form method="POST" action="<?= e(base_url('admin/settings/partial')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_redirect" value="admin/list/social_updates">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Display Template</label>
                                    <select name="social_updates_template" class="form-select">
                                        <?php $suTpl = (string)($entitySettings['social_updates_template'] ?? 'cards'); ?>
                                        <option value="cards" <?= $suTpl === 'cards' ? 'selected' : '' ?>>Card Grid</option>
                                        <option value="compact" <?= $suTpl === 'compact' ? 'selected' : '' ?>>Compact List</option>
                                        <option value="minimal" <?= $suTpl === 'minimal' ? 'selected' : '' ?>>Minimal (text only)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cards per Row</label>
                                    <select name="social_updates_cards_per_row" class="form-select">
                                        <?php $suCols = (int)($entitySettings['social_updates_cards_per_row'] ?? 3); ?>
                                        <option value="2" <?= $suCols === 2 ? 'selected' : '' ?>>2 columns</option>
                                        <option value="3" <?= $suCols === 3 ? 'selected' : '' ?>>3 columns</option>
                                        <option value="4" <?= $suCols === 4 ? 'selected' : '' ?>>4 columns</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rows on Home Page</label>
                                    <select name="social_updates_rows" class="form-select">
                                        <?php $suRows = (int)($entitySettings['social_updates_rows'] ?? 2); ?>
                                        <option value="1" <?= $suRows === 1 ? 'selected' : '' ?>>1 row</option>
                                        <option value="2" <?= $suRows === 2 ? 'selected' : '' ?>>2 rows</option>
                                        <option value="3" <?= $suRows === 3 ? 'selected' : '' ?>>3 rows</option>
                                        <option value="0" <?= $suRows === 0 ? 'selected' : '' ?>>Show all</option>
                                    </select>
                                    <small class="text-muted">Total items shown = rows × cards per row</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Content Lines</label>
                                    <select name="social_updates_content_lines" class="form-select">
                                        <?php $suLines = (int)($entitySettings['social_updates_content_lines'] ?? 3); ?>
                                        <option value="2" <?= $suLines === 2 ? 'selected' : '' ?>>2 lines</option>
                                        <option value="3" <?= $suLines === 3 ? 'selected' : '' ?>>3 lines</option>
                                        <option value="4" <?= $suLines === 4 ? 'selected' : '' ?>>4 lines</option>
                                        <option value="0" <?= $suLines === 0 ? 'selected' : '' ?>>Show all</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <?php $suShowImg = in_array(strtolower(trim((string)($entitySettings['social_updates_show_images'] ?? '1'))), ['1', 'true', 'yes', 'on'], true); ?>
                                        <input class="form-check-input" type="checkbox" name="social_updates_show_images" value="1" <?= $suShowImg ? 'checked' : '' ?> id="chkShowImages">
                                        <label class="form-check-label" for="chkShowImages">Show images on cards</label>
                                    </div>
                                </div>
                                <hr class="my-1">
                                <div class="col-12"><label class="form-label fw-semibold">Section Colors</label></div>
                                <div class="col-md-4">
                                    <label class="form-label">Background</label>
                                    <input type="color" name="social_updates_bg_color" class="form-control form-control-color w-100" value="<?= e($entitySettings['social_updates_bg_color'] ?? '#ffffff') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Card Background</label>
                                    <input type="color" name="social_updates_card_bg" class="form-control form-control-color w-100" value="<?= e($entitySettings['social_updates_card_bg'] ?? '#ffffff') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Accent</label>
                                    <input type="color" name="social_updates_accent_color" class="form-control form-control-color w-100" value="<?= e($entitySettings['social_updates_accent_color'] ?? '#5fc7e7') ?>">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Appearance</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="socialTabDebug">
                        <p class="text-muted small mb-3">Test your API connection and preview posts that would be fetched — <strong>nothing is saved</strong> until you click "Fetch Now" on the Configuration tab.</p>
                        <button type="button" class="btn btn-warning btn-sm mb-3" id="btnRunDebug"><i class="bi bi-bug me-1"></i>Run Debug &amp; Preview</button>
                        <div id="debugOutput" style="display:none;">
                            <h6 class="text-uppercase text-muted mt-2 mb-2"><i class="bi bi-wrench me-1"></i>Diagnostics</h6>
                            <div id="debugDiag" class="bg-light p-3 rounded small mb-3" style="max-height:200px;overflow:auto;font-family:monospace;white-space:pre-wrap;"></div>
                            <h6 class="text-uppercase text-muted mb-2"><i class="bi bi-eye me-1"></i>Preview Posts</h6>
                            <div id="debugPosts"></div>
                        </div>
                        <div id="debugLoading" class="text-center py-3" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Contacting Facebook/Instagram API...</span>
                        </div>
                        <div id="debugError" class="alert alert-danger small" style="display:none;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Wire Fetch Now button to the hidden form
    var fetchBtn = document.getElementById('btnFetchNow');
    var fetchForm = document.getElementById('socialFetchNowForm');
    if (fetchBtn && fetchForm) {
        fetchBtn.addEventListener('click', function(){ fetchForm.submit(); });
    }

    var btn = document.getElementById('btnRunDebug');
    if (!btn) return;
    btn.addEventListener('click', function(){
        var out = document.getElementById('debugOutput');
        var diag = document.getElementById('debugDiag');
        var posts = document.getElementById('debugPosts');
        var loading = document.getElementById('debugLoading');
        var errBox = document.getElementById('debugError');
        out.style.display = 'none';
        errBox.style.display = 'none';
        loading.style.display = 'block';
        btn.disabled = true;
        fetch('<?= e(base_url('admin/social-fetch/debug')) ?>', {credentials:'same-origin'})
        .then(function(r){ return r.json(); })
        .then(function(data){
            loading.style.display = 'none';
            btn.disabled = false;
            if (!data.ok && data.error) {
                errBox.textContent = data.error + (data.trace ? ' (' + data.trace + ')' : '');
                errBox.style.display = 'block';
                return;
            }
            // Show diagnostics
            var diagText = '';
            if (data.config) {
                diagText += '== Configuration ==\n';
                diagText += 'Page ID: ' + (data.config.page_id || '(not set)') + '\n';
                diagText += 'IG User ID: ' + (data.config.ig_user_id || '(not set)') + '\n';
                diagText += 'Token set: ' + (data.config.token_set ? 'YES (' + data.config.token_prefix + ')' : 'NO') + '\n';
                diagText += 'Auto-fetch: ' + (data.config.auto_fetch_enabled ? 'enabled' : 'disabled') + '\n';
                diagText += 'Last run: ' + (data.config.last_run || 'never') + '\n';
                diagText += 'Graph API: ' + (data.config.graph_version || '?') + '\n';
            }
            if (data.server) {
                diagText += '\n== Server ==\n';
                diagText += 'PHP: ' + data.server.php_version + '\n';
                diagText += 'cURL: ' + data.server.curl_version + '\n';
                diagText += 'SSL: ' + data.server.ssl_version + '\n';
            }
            if (data.result) {
                diagText += '\n== Fetch Result ==\n';
                diagText += 'Facebook posts: ' + (data.result.stats ? data.result.stats.facebook : 0) + '\n';
                diagText += 'Instagram posts: ' + (data.result.stats ? data.result.stats.instagram : 0) + '\n';
                diagText += 'Total: ' + (data.result.total || 0) + '\n';
                if (data.result.errors && data.result.errors.length) {
                    diagText += '\n== ERRORS ==\n';
                    data.result.errors.forEach(function(e){ diagText += '⚠ ' + e + '\n'; });
                }
            }
            diag.textContent = diagText;
            // Show preview posts
            var pp = data.preview_posts || [];
            if (pp.length === 0) {
                posts.innerHTML = '<div class="alert alert-warning small">No posts returned from API. Check errors above.</div>';
            } else {
                var html = '<div class="list-group">';
                pp.forEach(function(p){
                    html += '<div class="list-group-item">';
                    html += '<div class="d-flex justify-content-between"><span class="badge bg-secondary">' + (p.source||'') + '</span><small class="text-muted">' + (p.posted_at||'') + '</small></div>';
                    if (p.image) html += '<img src="' + p.image + '" class="mt-2 rounded" style="max-height:80px;max-width:120px;object-fit:cover;" loading="lazy">';
                    html += '<p class="mb-1 mt-1 small">' + (p.content||'').replace(/</g,'&lt;') + '</p>';
                    if (p.link) html += '<a href="' + p.link + '" target="_blank" class="small">View original</a>';
                    html += '</div>';
                });
                html += '</div>';
                posts.innerHTML = html;
            }
            out.style.display = 'block';
        })
        .catch(function(err){
            loading.style.display = 'none';
            btn.disabled = false;
            errBox.textContent = 'Network error: ' + err.message;
            errBox.style.display = 'block';
        });
    });
});
</script>
<?php endif; ?>
