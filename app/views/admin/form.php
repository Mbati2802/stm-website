<?php $isEdit = $isEdit ?? false; $row = $row ?? []; $programmeContent = $programmeContent ?? []; ?>
<section class="py-4"><div class="admin-content-wrap"><div class="admin-page-head mb-3"><h1 class="h4 fw-bold mb-0 text-capitalize"><?= $isEdit ? 'Edit' : 'Create' ?> <?= e(str_replace('_',' ', $entity)) ?></h1><a class="btn btn-outline-secondary" href="<?= e(base_url('admin/list/' . $entity)) ?>"><i class="bi bi-arrow-left me-1"></i>Back to list</a></div><form method="POST" enctype="multipart/form-data" class="soft-card p-4"><?= csrf_field() ?><div class="row g-3">
<?php if($entity==='departments'): ?>
<div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required></div><div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control rich-editor"><?= e($row['description'] ?? '') ?></textarea></div>
<?php elseif($entity==='programmes'): ?>
<div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required></div><div class="col-md-3"><label class="form-label">Category</label><select name="category" class="form-select"><?php foreach(['Diploma','Certificate','Short Course','Artisan'] as $cat): ?><option <?= (($row['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select></div><div class="col-md-3"><label class="form-label">Terms</label><input name="terms" type="number" class="form-control" value="<?= e((string)($row['terms'] ?? 1)) ?>" required></div><div class="col-md-4"><label class="form-label">Department ID</label><input name="department_id" type="number" class="form-control" value="<?= e((string)($row['department_id'] ?? 1)) ?>"></div><div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control"><?= e($row['description'] ?? '') ?></textarea></div>
<div class="col-12"><hr class="my-2"></div>
<div class="col-12"><h2 class="h6 text-uppercase text-muted mb-2">Programme Detail Content</h2></div>
<div class="col-md-6"><label class="form-label">Course Family</label><input class="form-control" value="<?= e($programmeContent['family_name'] ?? '') ?>" readonly></div>
<div class="col-md-6"><label class="form-label">Content Scope</label><select name="programme_content_scope" class="form-select"><option value="shared" <?= (($programmeContent['content_scope'] ?? 'shared') === 'shared') ? 'selected' : '' ?>>Shared across all levels in this family</option><option value="level" <?= (($programmeContent['content_scope'] ?? '') === 'level') ? 'selected' : '' ?>>This level only (override)</option></select></div>
<div class="col-12">
  <div class="alert alert-info small mb-0">
    <strong>Editing tip:</strong> choose <strong>Shared</strong> to update all levels in this course family (e.g. Diploma/Certificate/Artisan together). Choose <strong>This level only</strong> to override just this specific programme.
  </div>
</div>
<div class="col-12"><label class="form-label">Course Overview</label><textarea name="programme_overview" rows="4" class="form-control rich-editor"><?= e($programmeContent['overview'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Course Objectives (one per line)</label><textarea name="programme_objectives" rows="6" class="form-control"><?= e($programmeContent['objectives'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Course Content Areas (one per line)</label><textarea name="programme_content_areas" rows="6" class="form-control"><?= e($programmeContent['content_areas'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Career Opportunities (one per line)</label><textarea name="programme_career_opportunities" rows="6" class="form-control"><?= e($programmeContent['career_opportunities'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Why Study This Course? (one per line)</label><textarea name="programme_why_study" rows="6" class="form-control"><?= e($programmeContent['why_study'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Duration Override (optional)</label><input name="programme_duration_override" class="form-control" value="<?= e($programmeContent['duration_override'] ?? '') ?>" placeholder="Leave blank to auto by level"></div>
<div class="col-md-6"><label class="form-label">Entry Requirement Override (optional)</label><input name="programme_entry_requirement_override" class="form-control" value="<?= e($programmeContent['entry_requirement_override'] ?? '') ?>" placeholder="Leave blank to auto by level"></div>
<div class="col-md-6"><label class="form-label">Homepage Card Image (optional)</label><input type="file" name="programme_home_card_image_file" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Current Homepage Card Image URL</label><input class="form-control" value="<?= e((string)($programmeHomeCardImage ?? 'Not set')) ?>" readonly></div>
<?php elseif($entity==='faqs'): ?>
<div class="col-12"><label class="form-label">Question</label><input name="question" class="form-control" value="<?= e($row['question'] ?? '') ?>" required></div><div class="col-12"><label class="form-label">Answer</label><textarea name="answer" class="form-control rich-editor" rows="4"><?= e($row['answer'] ?? '') ?></textarea></div>
<?php elseif($entity==='pages'): ?>
<div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div><div class="col-md-6"><label class="form-label">Slug</label><input name="slug" class="form-control" value="<?= e($row['slug'] ?? '') ?>" placeholder="about"></div><div class="col-12"><label class="form-label">Content</label><textarea name="content" class="form-control rich-editor" rows="8"><?= e($row['content'] ?? '') ?></textarea></div>
<?php elseif($entity==='gallery'): ?>
<div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div><div class="col-md-6"><label class="form-label">Category</label><select name="category" class="form-select"><?php foreach(['Events','Classes','Labs','Graduations'] as $cat): ?><option <?= (($row['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select></div><div class="col-12"><label class="form-label">Image <?= $isEdit ? '(optional to replace)' : '' ?></label><input type="file" name="image" accept="image/*" class="form-control" <?= $isEdit ? '' : 'required' ?>></div>
<?php elseif($entity==='library_resources'): ?>
<div class="col-md-8"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div><div class="col-12"><label class="form-label">Summary</label><textarea name="summary" class="form-control rich-editor"><?= e($row['summary'] ?? '') ?></textarea></div><div class="col-12"><label class="form-label">PDF File <?= $isEdit ? '(optional to replace)' : '' ?></label><input type="file" name="file_path" accept="application/pdf" class="form-control" <?= $isEdit ? '' : 'required' ?>></div>
<?php elseif($entity==='events'): ?>
<?php $startsAt = ($row['starts_at'] ?? '') !== '' ? date('Y-m-d\\TH:i', strtotime((string)$row['starts_at'])) : ''; ?>
<?php $endsAt = ($row['ends_at'] ?? '') !== '' ? date('Y-m-d\\TH:i', strtotime((string)$row['ends_at'])) : ''; ?>
<div class="col-md-8"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div>
<div class="col-md-4"><label class="form-label">Starts At</label><input name="starts_at" type="datetime-local" class="form-control" value="<?= e($startsAt) ?>" required></div>
<div class="col-md-4"><label class="form-label">Ends At (optional)</label><input name="ends_at" type="datetime-local" class="form-control" value="<?= e($endsAt) ?>"></div>
<div class="col-md-4"><label class="form-label">Category</label><select name="category" class="form-select"><?php foreach(['Academic Workshops','Clinical Training Sessions','Guest Lectures','Career Days','Community Outreach Programs','Student Life & Sports Events'] as $cat): ?><option value="<?= e($cat) ?>" <?= (($row['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Venue Type</label><select name="venue_type" class="form-select"><?php foreach(['Campus','Hospital','Online'] as $venue): ?><option value="<?= e($venue) ?>" <?= (($row['venue_type'] ?? 'Campus') === $venue) ? 'selected' : '' ?>><?= e($venue) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Time Label</label><input name="time_label" class="form-control" value="<?= e($row['time_label'] ?? '') ?>" placeholder="9:00 AM - EAT"></div>
<div class="col-md-4"><label class="form-label">Location</label><input name="location" class="form-control" value="<?= e($row['location'] ?? '') ?>" placeholder="Main Campus Hall"></div>
<div class="col-md-4"><label class="form-label">Registration Status</label><select name="registration_status" class="form-select"><?php foreach(['Open','Closing soon','Full'] as $status): ?><option value="<?= e($status) ?>" <?= (($row['registration_status'] ?? 'Open') === $status) ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><label class="form-label">Registration URL (optional)</label><input name="registration_url" class="form-control" value="<?= e($row['registration_url'] ?? '') ?>" placeholder="External registration link"></div>
<div class="col-md-4"><label class="form-label">Featured Event</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" <?= !empty($row['is_featured']) ? 'checked' : '' ?>><label class="form-check-label">Mark as featured</label></div></div>
<div class="col-12"><label class="form-label">Summary</label><textarea name="summary" class="form-control rich-editor"><?= e($row['summary'] ?? '') ?></textarea></div>
<div class="col-12"><label class="form-label">Body</label><textarea name="body" rows="8" class="form-control rich-editor"><?= e($row['body'] ?? '') ?></textarea></div>
<div class="col-12"><label class="form-label">Event Image Upload (recommended)</label><input type="file" name="event_image_file" accept="image/*" class="form-control"></div>
<div class="col-12"><label class="form-label">Featured Image URL (fallback)</label><input name="image_path" class="form-control" value="<?= e($row['image_path'] ?? '') ?>"></div>
<div class="col-md-4"><label class="form-label">Publish to Student Portal</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="publish_to_portal" value="1" <?= !empty($row['publish_to_portal']) ? 'checked' : '' ?>><label class="form-check-label">Show as student announcement</label></div></div>
<div class="col-md-8"><label class="form-label">Portal Announcement Text (optional)</label><textarea name="portal_announcement_text" rows="3" class="form-control" placeholder="This message appears in the student portal announcements list."><?= e($row['portal_announcement_text'] ?? '') ?></textarea></div>
<div class="col-12">
  <label class="form-label">Social Updates Feed Embed (Events page)</label>
  <textarea name="social_updates_embed" rows="5" class="form-control" placeholder="Paste social feed embed iframe/html snippet"><?= e((string)($siteSettings['events_social_updates_html'] ?? '')) ?></textarea>
  <div class="d-flex flex-wrap gap-2 mt-2">
    <button type="button" class="btn btn-sm btn-outline-primary" id="preview-social-embed-btn">Preview Embed</button>
  </div>
  <small class="text-muted">This appears on the public Events page under "Social Updates Feed".</small>
</div>
<div class="col-12">
  <div class="card border-0 soft-card" id="social-embed-preview-wrap" style="display:none;">
    <div class="card-header bg-light fw-semibold">Social Feed Preview</div>
    <div class="card-body p-0">
      <iframe id="social-embed-preview-frame" title="Social embed preview" style="width:100%;height:360px;border:0;"></iframe>
    </div>
  </div>
</div>
<?php elseif($entity==='users'): ?>
<div class="col-md-6"><label class="form-label">Full Name</label><input name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required></div>
<div class="col-md-6"><label class="form-label">Email Address</label><input name="email" type="email" class="form-control" value="<?= e($row['email'] ?? '') ?>" required></div>
<div class="col-md-4"><label class="form-label">Role</label><select name="role" class="form-select"><?php foreach(['super_admin' => 'Super Admin', 'junior_admin' => 'Senior Admin', 'teacher' => 'Teacher'] as $roleValue => $roleLabel): ?><option value="<?= e($roleValue) ?>" <?= (($row['role'] ?? 'teacher') === $roleValue) ? 'selected' : '' ?>><?= e($roleLabel) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach(['active' => 'Active', 'disabled' => 'Disabled'] as $statusValue => $statusLabel): ?><option value="<?= e($statusValue) ?>" <?= (($row['status'] ?? 'active') === $statusValue) ? 'selected' : '' ?>><?= e($statusLabel) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Created</label><input class="form-control" value="<?= e((string)($row['created_at'] ?? 'Auto')) ?>" readonly></div>
<div class="col-md-6"><label class="form-label">Password <?= $isEdit ? '(leave blank to keep current)' : '' ?></label><input name="password" type="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6"></div>
<div class="col-md-6"><label class="form-label">Confirm Password</label><input name="password_confirm" type="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6"></div>
<?php elseif($entity==='portal_courses'): ?>
<div class="col-md-6"><label class="form-label">Programme</label><select name="programme_id" class="form-select" required><option value="">Select programme</option><?php foreach(($programmes ?? []) as $programme): ?><option value="<?= e((string)$programme['id']) ?>" <?= ((string)($row['programme_id'] ?? '') === (string)$programme['id']) ? 'selected' : '' ?>><?= e((string)$programme['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Teacher</label><select name="teacher_id" class="form-select"><option value="">Select teacher (optional)</option><?php foreach(($teachers ?? []) as $teacher): ?><option value="<?= e((string)$teacher['id']) ?>" <?= ((string)($row['teacher_id'] ?? '') === (string)$teacher['id']) ? 'selected' : '' ?>><?= e((string)$teacher['name']) ?> (<?= e((string)$teacher['email']) ?>)</option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Course Code</label><input name="code" class="form-control" value="<?= e($row['code'] ?? '') ?>" placeholder="MCH-101"></div>
<div class="col-12"><label class="form-label">Course Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control rich-editor"><?= e($row['description'] ?? '') ?></textarea></div>
<?php elseif($entity==='programme_timetables'): ?>
<div class="col-md-6"><label class="form-label">Programme</label><select name="programme_id" class="form-select" required><option value="">Select programme</option><?php foreach(($programmes ?? []) as $programme): ?><option value="<?= e((string)$programme['id']) ?>" <?= ((string)($row['programme_id'] ?? '') === (string)$programme['id']) ? 'selected' : '' ?>><?= e((string)$programme['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div>
<div class="col-12"><label class="form-label">Details</label><textarea name="details" rows="4" class="form-control rich-editor"><?= e($row['details'] ?? '') ?></textarea></div>
<div class="col-12"><label class="form-label">PDF File <?= $isEdit ? '(optional to replace)' : '' ?></label><input type="file" name="file_path" accept="application/pdf" class="form-control"></div>
<input type="hidden" name="current_file_path" value="<?= e((string)($row['file_path'] ?? '')) ?>">
<?php elseif($entity==='course_grades'): ?>
<div class="col-md-4"><label class="form-label">Student</label><select name="student_id" class="form-select" required><option value="">Select student</option><?php foreach(($students ?? []) as $student): ?><option value="<?= e((string)$student['id']) ?>" <?= ((string)($row['student_id'] ?? '') === (string)$student['id']) ? 'selected' : '' ?>><?= e((string)$student['name']) ?> (<?= e((string)($student['admission_number'] ?? ('ID ' . $student['id']))) ?>)</option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Course</label><select name="course_id" class="form-select" required><option value="">Select course</option><?php foreach(($courses ?? []) as $course): ?><option value="<?= e((string)$course['id']) ?>" <?= ((string)($row['course_id'] ?? '') === (string)$course['id']) ? 'selected' : '' ?>><?= e(trim((string)($course['code'] ?? '') . ' ' . (string)$course['title'])) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Marks (%)</label><input name="marks" type="number" min="0" max="100" step="0.01" class="form-control" value="<?= e((string)($row['marks'] ?? '')) ?>" placeholder="e.g 72.5"></div>
<div class="col-md-4"><label class="form-label">Grade Scheme (optional)</label><select name="grading_scheme_id" class="form-select"><option value="">Auto by marks range</option><?php foreach(($gradingSchemes ?? []) as $scheme): ?><option value="<?= e((string)$scheme['id']) ?>" <?= ((string)($row['grading_scheme_id'] ?? '') === (string)$scheme['id']) ? 'selected' : '' ?>><?= e((string)$scheme['name']) ?> - <?= e((string)$scheme['grade_label']) ?> (<?= e((string)$scheme['min_score']) ?>-<?= e((string)$scheme['max_score']) ?>)</option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Grade</label><input name="grade" class="form-control" value="<?= e($row['grade'] ?? '') ?>" placeholder="Auto-filled from marks" required></div>
<div class="col-12"><label class="form-label">Remarks</label><input name="remarks" class="form-control" value="<?= e($row['remarks'] ?? '') ?>" placeholder="Optional notes"></div>
<?php elseif($entity==='course_assignments'): ?>
<?php $dueAt = ($row['due_at'] ?? '') !== '' ? date('Y-m-d\\TH:i', strtotime((string)$row['due_at'])) : ''; ?>
<div class="col-md-4"><label class="form-label">Course</label><select name="course_id" class="form-select" required><option value="">Select course</option><?php foreach(($courses ?? []) as $course): ?><option value="<?= e((string)$course['id']) ?>" <?= ((string)($row['course_id'] ?? '') === (string)$course['id']) ? 'selected' : '' ?>><?= e(trim((string)($course['code'] ?? '') . ' ' . (string)$course['title'])) ?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div>
<div class="col-md-6"><label class="form-label">Due Date</label><input name="due_at" type="datetime-local" class="form-control" value="<?= e($dueAt) ?>"></div>
<div class="col-md-6"><label class="form-label">File Attachment</label><input type="file" name="file_path" class="form-control"></div>
<div class="col-12"><label class="form-label">Instructions</label><textarea name="instructions" rows="5" class="form-control rich-editor"><?= e($row['instructions'] ?? '') ?></textarea></div>
<input type="hidden" name="current_file_path" value="<?= e((string)($row['file_path'] ?? '')) ?>">
<?php elseif($entity==='study_materials'): ?>
<div class="col-md-4"><label class="form-label">Course</label><select name="course_id" class="form-select" required><option value="">Select course</option><?php foreach(($courses ?? []) as $course): ?><option value="<?= e((string)$course['id']) ?>" <?= ((string)($row['course_id'] ?? '') === (string)$course['id']) ? 'selected' : '' ?>><?= e(trim((string)($course['code'] ?? '') . ' ' . (string)$course['title'])) ?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div>
<div class="col-12"><label class="form-label">Summary</label><textarea name="summary" rows="4" class="form-control rich-editor"><?= e($row['summary'] ?? '') ?></textarea></div>
<div class="col-12"><label class="form-label">File Upload</label><input type="file" name="file_path" class="form-control"></div>
<input type="hidden" name="current_file_path" value="<?= e((string)($row['file_path'] ?? '')) ?>">
<?php elseif($entity==='grading_schemes'): ?>
<div class="col-md-6"><label class="form-label">Scheme Name</label><input name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" placeholder="Main Grading System" required></div>
<div class="col-md-6"><label class="form-label">Grade Label</label><input name="grade_label" class="form-control text-uppercase" value="<?= e($row['grade_label'] ?? '') ?>" placeholder="A, B+, C" required></div>
<div class="col-md-6"><label class="form-label">Minimum Score</label><input name="min_score" type="number" min="0" max="100" step="0.01" class="form-control" value="<?= e((string)($row['min_score'] ?? '')) ?>" required></div>
<div class="col-md-6"><label class="form-label">Maximum Score</label><input name="max_score" type="number" min="0" max="100" step="0.01" class="form-control" value="<?= e((string)($row['max_score'] ?? '')) ?>" required></div>
<div class="col-12"><label class="form-label">Remarks (optional)</label><input name="remarks" class="form-control" value="<?= e((string)($row['remarks'] ?? '')) ?>" placeholder="Excellent, Good, Pass..."></div>
<?php elseif($entity==='testimonials'): ?>
<div class="col-md-6"><label class="form-label">Student Name</label><input name="name" class="form-control" value="<?= e($row['name'] ?? '') ?>" required placeholder="e.g. Jane Wanjiku"></div>
<div class="col-md-6"><label class="form-label">Course / Role</label><input name="course" class="form-control" value="<?= e($row['course'] ?? '') ?>" placeholder="e.g. Diploma in Nursing, Parent, Alumni"></div>
<div class="col-12"><label class="form-label">Testimonial Message</label><textarea name="message" rows="5" class="form-control" required placeholder="The supportive environment and hands-on training prepared me well for my career..."><?= e($row['message'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Photo Upload</label><input type="file" name="image_file" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Photo URL (fallback)</label><input name="image_path" class="form-control" value="<?= e($row['image_path'] ?? '') ?>" placeholder="https://..."></div>
<?php if (!empty($row['image_path'])): ?><div class="col-12"><img src="<?= e($row['image_path']) ?>" alt="Preview" style="width:60px;height:60px;object-fit:cover;border-radius:50%;"></div><?php endif; ?>
<div class="col-md-4"><label class="form-label">Sort Order</label><input name="sort_order" type="number" min="0" class="form-control" value="<?= e((string)($row['sort_order'] ?? '0')) ?>"></div>
<div class="col-md-4"><label class="form-label">Visible</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_visible" value="1" <?= empty($row['is_visible']) || (!isset($row['is_visible'])) ? '' : 'checked' ?>><label class="form-check-label">Show on website</label></div></div>
<?php elseif($entity==='social_updates'): ?>
<div class="col-12"><label class="form-label">Update Content</label><textarea name="content" rows="4" class="form-control" required placeholder="Share news, announcements, or updates..."><?= e($row['content'] ?? '') ?></textarea></div>
<div class="col-md-6"><label class="form-label">Image Upload</label><input type="file" name="image_file" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Image URL (fallback)</label><input name="image_path" class="form-control" value="<?= e($row['image_path'] ?? '') ?>" placeholder="https://..."></div>
<div class="col-md-6"><label class="form-label">Link URL (optional)</label><input name="link_url" class="form-control" value="<?= e($row['link_url'] ?? '') ?>" placeholder="https://..."></div>
<div class="col-md-6"><label class="form-label">Source</label><select name="source" class="form-select"><?php foreach(['general'=>'General','announcement'=>'Announcement','event'=>'Event','news'=>'News','social'=>'Social Media'] as $sv=>$sl): ?><option value="<?= e($sv) ?>" <?= (($row['source'] ?? 'general') === $sv) ? 'selected' : '' ?>><?= e($sl) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Pinned</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_pinned" value="1" <?= !empty($row['is_pinned']) ? 'checked' : '' ?>><label class="form-check-label">Pin to top</label></div></div>
<div class="col-md-3"><label class="form-label">Visible</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_visible" value="1" <?= empty($row['is_visible']) || (!isset($row['is_visible'])) ? '' : 'checked' ?>><label class="form-check-label">Show on website</label></div></div>
<?php else: ?>
<div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= e($row['title'] ?? '') ?>" required></div><div class="col-12"><label class="form-label">Summary</label><textarea name="summary" class="form-control rich-editor"><?= e($row['summary'] ?? '') ?></textarea></div><div class="col-12"><label class="form-label">Body</label><textarea name="body" rows="6" class="form-control rich-editor"><?= e($row['body'] ?? '') ?></textarea></div><div class="col-12"><label class="form-label">Featured Image URL (optional)</label><input name="image_path" class="form-control" value="<?= e($row['image_path'] ?? '') ?>"></div>
<div class="col-12"><label class="form-label">Upload Image (optional)</label><input type="file" name="image_file" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
<?php endif; ?>
</div><div class="mt-3"><button class="btn btn-primary"><?= $isEdit ? 'Update' : 'Save' ?></button> <a class="btn btn-outline-secondary" href="<?= e(base_url('admin/list/' . $entity)) ?>">Cancel</a></div></form></div></section>
<?php if ($entity === 'events'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('preview-social-embed-btn');
  const frame = document.getElementById('social-embed-preview-frame');
  const wrap = document.getElementById('social-embed-preview-wrap');
  const source = document.querySelector('textarea[name="social_updates_embed"]');
  if (!btn || !frame || !wrap || !source) return;

  btn.addEventListener('click', function () {
    const html = String(source.value || '').trim();
    if (html === '') {
      wrap.style.display = 'none';
      return;
    }
    wrap.style.display = '';
    const doc = frame.contentWindow && frame.contentWindow.document;
    if (!doc) return;
    doc.open();
    doc.write('<!doctype html><html><body style="margin:0;padding:14px;font-family:Arial,sans-serif;background:#fff;">' + html + '</body></html>');
    doc.close();
  });
});
</script>
<?php endif; ?>
