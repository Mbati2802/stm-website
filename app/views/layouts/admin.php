<?php
$appName = $this->config['app_name'];
$adminPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$isAdminHome = $adminPath === 'admin';
$adminRole = Auth::role();
$roleLabels = [
    'super_admin' => 'Super Admin',
    'junior_admin' => 'Senior Admin',
    'teacher' => 'Teacher',
];
$adminRoleLabel = $roleLabels[$adminRole] ?? ucwords(str_replace('_', ' ', $adminRole));
$adminId = (int)($_SESSION['admin_id'] ?? 0);
$unreadTeamMessages = 0;
$publicMessagesCount = 0;
$supportTicketsCount = 0;
if ($adminId > 0 && !str_ends_with($viewPath, 'admin/login.php')) {
    $topbarModel = new ContentModel($this->config);
    $unreadTeamMessages = $topbarModel->getUnreadAdminMessageCount($adminId);
    if (Auth::canManageEntity('messages')) {
        $publicMessagesCount = $topbarModel->getUnreadPublicMessagesCount();
    }
    if (Auth::canManageEntity('students')) {
        $supportTicketsCount = count((new StudentPortalModel($this->config))->getAllSupportTickets());
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Admin') ?> | <?= e($appName) ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/styles.css')) ?>" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/admin.css')) ?>" rel="stylesheet">
</head>
<body class="admin-theme role-<?= e($adminRole) ?>">
<?php if (str_ends_with($viewPath, 'admin/login.php')): ?>
<main class="admin-main">
    <?php include $viewPath; ?>
</main>
<?php else: ?>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-brand">
            <span class="admin-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
            <div class="d-flex flex-column">
                <span class="fw-bold">STM Admin</span>
                <small class="text-white-50 text-uppercase"><?= e($adminRoleLabel) ?></small>
            </div>
        </div>
        <nav class="nav flex-column gap-2 mt-3">
            <a class="nav-link <?= $isAdminHome ? 'active' : '' ?>" href="<?= e(base_url('admin')) ?>"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">Content</div>
                <?php if (Auth::canManageEntity('programmes')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/programmes') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/programmes')) ?>"><i class="bi bi-journal-text"></i><span>Programmes</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('departments')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/departments') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/departments')) ?>"><i class="bi bi-diagram-3"></i><span>Departments</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('news')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/news') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/news')) ?>"><i class="bi bi-newspaper"></i><span>News</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('careers')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/careers') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/careers')) ?>"><i class="bi bi-briefcase"></i><span>Careers</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('tenders')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/tenders') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/tenders')) ?>"><i class="bi bi-file-earmark-check"></i><span>Tenders</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('faqs')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/faqs') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/faqs')) ?>"><i class="bi bi-question-circle"></i><span>FAQs</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('pages')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/pages') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/pages')) ?>"><i class="bi bi-file-richtext"></i><span>Pages</span></a><?php endif; ?>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">Academic Portal</div>
                <?php if (Auth::canManageEntity('portal_courses')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/portal_courses') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/portal_courses')) ?>"><i class="bi bi-journal-code"></i><span>Portal Courses</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('programme_timetables')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/programme_timetables') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/programme_timetables')) ?>"><i class="bi bi-calendar3"></i><span>Programme Timetables</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('course_grades')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/course_grades') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/course_grades')) ?>"><i class="bi bi-award"></i><span>Course Grades</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('grading_schemes')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/grading_schemes') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/grading_schemes')) ?>"><i class="bi bi-sliders2"></i><span>Grading Schemes</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('course_assignments')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/course_assignments') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/course_assignments')) ?>"><i class="bi bi-file-earmark-text"></i><span>Assignments</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('study_materials')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/study_materials') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/study_materials')) ?>"><i class="bi bi-folder"></i><span>Study Materials</span></a><?php endif; ?>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">Media and Communication</div>
                <?php if (Auth::canManageEntity('gallery')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/gallery') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/gallery')) ?>"><i class="bi bi-images"></i><span>Gallery</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('library_resources')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/library_resources') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/library_resources')) ?>"><i class="bi bi-book"></i><span>Library Materials</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('messages')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/messages') ? 'active' : '' ?>" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-envelope"></i><span>Messages</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('messages')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/applications') ? 'active' : '' ?>" href="<?= e(base_url('admin/applications')) ?>"><i class="bi bi-ui-checks-grid"></i><span>Applications</span></a><?php endif; ?>
                <a class="nav-link <?= str_contains($adminPath, 'admin/internal-messages') ? 'active' : '' ?>" href="<?= e(base_url('admin/internal-messages')) ?>"><i class="bi bi-chat-left-dots"></i><span>Team Messages</span><?php if ($unreadTeamMessages > 0): ?><span class="badge rounded-pill text-bg-warning ms-auto"><?= (int)$unreadTeamMessages ?></span><?php endif; ?></a>
                <?php if (Auth::canManageEntity('students')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/support-tickets') ? 'active' : '' ?>" href="<?= e(base_url('admin/support-tickets')) ?>"><i class="bi bi-headset"></i><span>Support Tickets</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('students')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/students') ? 'active' : '' ?>" href="<?= e(base_url('admin/students')) ?>"><i class="bi bi-people"></i><span>Student Accounts</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('media')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/media') ? 'active' : '' ?>" href="<?= e(base_url('admin/media')) ?>"><i class="bi bi-folder2-open"></i><span>Media Library</span></a><?php endif; ?>
                <?php if (Auth::canManageEntity('events')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/event-registrations') ? 'active' : '' ?>" href="<?= e(base_url('admin/event-registrations')) ?>"><i class="bi bi-calendar2-week"></i><span>Event Registrations</span></a><?php endif; ?>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">System</div>
                <?php if (Auth::isSuperAdmin() || Auth::isJuniorAdmin()): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/users') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/users')) ?>"><i class="bi bi-person-badge"></i><span>Staff Users</span></a><?php endif; ?>
                <?php if (!Auth::isTeacher()): ?><a class="nav-link <?= str_contains($adminPath, 'admin/settings') ? 'active' : '' ?>" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-sliders"></i><span>UI Content Settings</span></a><?php endif; ?>
                <a class="nav-link text-danger" href="<?= e(base_url('admin/logout')) ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </div>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <button class="btn btn-sm admin-sidebar-toggle-btn" type="button" id="adminSidebarToggle"><i class="bi bi-list"></i></button>
            <div class="admin-topbar-title">
                <strong><?= e($metaTitle ?? 'Admin') ?></strong>
                <span class="topbar-sep"></span>
                <span>Content Management</span>
            </div>
            <div class="admin-topbar-tools">
                <span class="admin-clock" id="adminClock">--:--:--</span>
                <?php if (Auth::canManageEntity('messages')): ?>
                    <a class="btn btn-sm btn-light position-relative" href="<?= e(base_url('admin/messages')) ?>" title="Public messages">
                        <i class="bi bi-envelope"></i>
                        <?php if ($publicMessagesCount > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= (int)$publicMessagesCount ?></span><?php endif; ?>
                    </a>
                <?php endif; ?>
                <a class="btn btn-sm btn-light position-relative" href="<?= e(base_url('admin/internal-messages')) ?>" title="Team messages">
                    <i class="bi bi-chat-left-dots"></i>
                    <?php if ($unreadTeamMessages > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"><?= (int)$unreadTeamMessages ?></span><?php endif; ?>
                </a>
                <?php if (Auth::canManageEntity('students')): ?>
                    <a class="btn btn-sm btn-light position-relative" href="<?= e(base_url('admin/support-tickets')) ?>" title="Support tickets">
                        <i class="bi bi-headset"></i>
                        <?php if ($supportTicketsCount > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info text-dark"><?= (int)$supportTicketsCount ?></span><?php endif; ?>
                    </a>
                <?php endif; ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button">
                        <i class="bi bi-person-circle me-1"></i><?= e($adminRoleLabel) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (!Auth::isTeacher()): ?><li><a class="dropdown-item" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-gear me-2"></i>Settings</a></li><?php endif; ?>
                        <li><a class="dropdown-item" href="<?= e(base_url('admin/internal-messages')) ?>"><i class="bi bi-chat-left-dots me-2"></i>Team Messages</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= e(base_url('admin/logout')) ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <?php include $viewPath; ?>
    </main>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('adminSidebarToggle');
    if (sidebar && toggle) {
        const storageKey = 'stm_admin_sidebar_collapsed';
        try {
            if (localStorage.getItem(storageKey) === '1') {
                document.body.classList.add('admin-sidebar-collapsed');
            }
        } catch (e) {}

        toggle.addEventListener('click', function () {
            document.body.classList.toggle('admin-sidebar-collapsed');
            try {
                localStorage.setItem(storageKey, document.body.classList.contains('admin-sidebar-collapsed') ? '1' : '0');
            } catch (e) {}
        });
    }

    const clockEl = document.getElementById('adminClock');
    if (clockEl) {
        const tick = function () {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString();
        };
        tick();
        window.setInterval(tick, 1000);
    }

    const textareas = document.querySelectorAll('textarea.rich-editor');
    if (!textareas.length) return;

    const quills = [];
    textareas.forEach((textarea, idx) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'mb-3';
        const editor = document.createElement('div');
        editor.id = 'quill-editor-' + idx;
        editor.style.minHeight = '220px';
        wrapper.appendChild(editor);
        textarea.insertAdjacentElement('afterend', wrapper);
        textarea.style.display = 'none';

        const quill = new Quill(editor, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ align: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            }
        });

        const current = textarea.value || '';
        quill.clipboard.dangerouslyPasteHTML(current);
        quills.push({ textarea, quill });
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            quills.forEach(({ textarea, quill }) => {
                textarea.value = quill.root.innerHTML;
            });
        });
    });
});
</script>
</body>
</html>
