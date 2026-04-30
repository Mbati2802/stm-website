<?php
$appName = $this->config['app_name'];
$adminPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$isAdminHome = $adminPath === 'admin';
$adminRole = Auth::role();
$roleLabels = [
    'super_admin' => 'Super Admin',
    'junior_admin' => 'Senior Admin',
    'editor' => 'Editor',
    'viewer' => 'Viewer',
    'registrar' => 'Registrar',
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
    if (Auth::canViewEntity('messages')) {
        $publicMessagesCount = $topbarModel->getUnreadPublicMessagesCount();
    }
    if (Auth::canViewEntity('students')) {
        $supportTicketsCount = count((new StudentPortalModel($this->config))->getAllSupportTickets());
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
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
                <div class="admin-nav-group-header" data-bs-toggle="collapse" data-bs-target="#contentGroup">
                    <span class="admin-nav-group-title">Content</span>
                    <i class="bi bi-chevron-down admin-nav-chevron"></i>
                </div>
                <div class="collapse <?= str_contains($adminPath, 'admin/list/programmes') || str_contains($adminPath, 'admin/list/departments') || str_contains($adminPath, 'admin/list/news') || str_contains($adminPath, 'admin/list/careers') || str_contains($adminPath, 'admin/list/tenders') || str_contains($adminPath, 'admin/list/events') || str_contains($adminPath, 'admin/list/faqs') || str_contains($adminPath, 'admin/list/pages') ? 'show' : '' ?>" id="contentGroup">
                    <?php if (Auth::canViewEntity('programmes')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/programmes') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/programmes')) ?>"><i class="bi bi-journal-text"></i><span>Programmes</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('departments')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/departments') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/departments')) ?>"><i class="bi bi-diagram-3"></i><span>Departments</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('news')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/news') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/news')) ?>"><i class="bi bi-newspaper"></i><span>News</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('careers')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/careers') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/careers')) ?>"><i class="bi bi-briefcase"></i><span>Careers</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('tenders')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/tenders') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/tenders')) ?>"><i class="bi bi-file-earmark-check"></i><span>Tenders</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('events')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/events') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/events')) ?>"><i class="bi bi-calendar-event"></i><span>Events</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('faqs')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/faqs') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/faqs')) ?>"><i class="bi bi-question-circle"></i><span>FAQs</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('pages')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/pages') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/pages')) ?>"><i class="bi bi-file-richtext"></i><span>Pages</span></a><?php endif; ?>
                </div>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-header" data-bs-toggle="collapse" data-bs-target="#academicGroup">
                    <span class="admin-nav-group-title">Academic Portal</span>
                    <i class="bi bi-chevron-down admin-nav-chevron"></i>
                </div>
                <div class="collapse <?= str_contains($adminPath, 'admin/list/portal_courses') || str_contains($adminPath, 'admin/list/programme_timetables') || str_contains($adminPath, 'admin/list/course_grades') || str_contains($adminPath, 'admin/list/grading_schemes') || str_contains($adminPath, 'admin/list/course_assignments') || str_contains($adminPath, 'admin/list/study_materials') ? 'show' : '' ?>" id="academicGroup">
                    <?php if (Auth::canViewEntity('portal_courses')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/portal_courses') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/portal_courses')) ?>"><i class="bi bi-journal-code"></i><span>Portal Courses</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('programme_timetables')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/programme_timetables') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/programme_timetables')) ?>"><i class="bi bi-calendar3"></i><span>Programme Timetables</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('course_grades')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/course_grades') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/course_grades')) ?>"><i class="bi bi-award"></i><span>Course Grades</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('grading_schemes')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/grading_schemes') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/grading_schemes')) ?>"><i class="bi bi-sliders2"></i><span>Grading Schemes</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('course_assignments')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/course_assignments') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/course_assignments')) ?>"><i class="bi bi-file-earmark-text"></i><span>Assignments</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('study_materials')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/study_materials') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/study_materials')) ?>"><i class="bi bi-folder"></i><span>Study Materials</span></a><?php endif; ?>
                </div>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-header" data-bs-toggle="collapse" data-bs-target="#mediaGroup">
                    <span class="admin-nav-group-title">Media and Communication</span>
                    <i class="bi bi-chevron-down admin-nav-chevron"></i>
                </div>
                <div class="collapse <?= str_contains($adminPath, 'admin/list/gallery') || str_contains($adminPath, 'admin/list/library_resources') || str_contains($adminPath, 'admin/messages') || str_contains($adminPath, 'admin/applications') || str_contains($adminPath, 'admin/internal-messages') || str_contains($adminPath, 'admin/support-tickets') || str_contains($adminPath, 'admin/students') || str_contains($adminPath, 'admin/media') || str_contains($adminPath, 'admin/event-registrations') || str_contains($adminPath, 'admin/list/testimonials') || str_contains($adminPath, 'admin/list/social_updates') ? 'show' : '' ?>" id="mediaGroup">
                    <?php if (Auth::canViewEntity('gallery')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/gallery') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/gallery')) ?>"><i class="bi bi-images"></i><span>Gallery</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('library_resources')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/library_resources') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/library_resources')) ?>"><i class="bi bi-book"></i><span>Library Materials</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('messages')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/messages') ? 'active' : '' ?>" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-envelope"></i><span>Messages</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('messages')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/applications') ? 'active' : '' ?>" href="<?= e(base_url('admin/applications')) ?>"><i class="bi bi-ui-checks-grid"></i><span>Applications</span></a><?php endif; ?>
                    <a class="nav-link <?= str_contains($adminPath, 'admin/internal-messages') ? 'active' : '' ?>" href="<?= e(base_url('admin/internal-messages')) ?>"><i class="bi bi-chat-left-dots"></i><span>Team Messages</span><?php if ($unreadTeamMessages > 0): ?><span class="badge rounded-pill text-bg-warning ms-auto"><?= (int)$unreadTeamMessages ?></span><?php endif; ?></a>
                    <?php if (Auth::canViewEntity('students')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/support-tickets') ? 'active' : '' ?>" href="<?= e(base_url('admin/support-tickets')) ?>"><i class="bi bi-headset"></i><span>Support Tickets</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('students')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/students') ? 'active' : '' ?>" href="<?= e(base_url('admin/students')) ?>"><i class="bi bi-people"></i><span>Student Accounts</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('media')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/media') ? 'active' : '' ?>" href="<?= e(base_url('admin/media')) ?>"><i class="bi bi-folder2-open"></i><span>Media Library</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('events')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/event-registrations') ? 'active' : '' ?>" href="<?= e(base_url('admin/event-registrations')) ?>"><i class="bi bi-calendar2-week"></i><span>Event Registrations</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('testimonials')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/testimonials') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/testimonials')) ?>"><i class="bi bi-chat-square-quote"></i><span>Testimonials</span></a><?php endif; ?>
                    <?php if (Auth::canViewEntity('social_updates')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/social_updates') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/social_updates')) ?>"><i class="bi bi-megaphone"></i><span>Social Updates</span></a><?php endif; ?>
                </div>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-header" data-bs-toggle="collapse" data-bs-target="#systemGroup">
                    <span class="admin-nav-group-title">System</span>
                    <i class="bi bi-chevron-down admin-nav-chevron"></i>
                </div>
                <div class="collapse <?= str_contains($adminPath, 'admin/list/users') || str_contains($adminPath, 'admin/settings') ? 'show' : '' ?>" id="systemGroup">
                    <?php if (Auth::canViewEntity('users')): ?><a class="nav-link <?= str_contains($adminPath, 'admin/list/users') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/users')) ?>"><i class="bi bi-person-badge"></i><span>Staff Users</span></a><?php endif; ?>
                    <?php if (!Auth::isTeacher()): ?><a class="nav-link <?= str_contains($adminPath, 'admin/settings') ? 'active' : '' ?>" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-sliders"></i><span>UI Content Settings</span></a><?php endif; ?>
                    <a class="nav-link text-danger" href="<?= e(base_url('admin/logout')) ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
                </div>
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
                <?php if (Auth::canViewEntity('messages')): ?>
                    <a class="btn btn-sm btn-light position-relative" href="<?= e(base_url('admin/messages')) ?>" title="Public messages">
                        <i class="bi bi-envelope"></i>
                        <?php if ($publicMessagesCount > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= (int)$publicMessagesCount ?></span><?php endif; ?>
                    </a>
                <?php endif; ?>
                <a class="btn btn-sm btn-light position-relative" href="<?= e(base_url('admin/internal-messages')) ?>" title="Team messages">
                    <i class="bi bi-chat-left-dots"></i>
                    <?php if ($unreadTeamMessages > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"><?= (int)$unreadTeamMessages ?></span><?php endif; ?>
                </a>
                <?php if (Auth::canViewEntity('students')): ?>
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

    /* --- Mobile sidebar overlay --- */
    const overlay = document.createElement('div');
    overlay.className = 'admin-sidebar-overlay';
    document.body.appendChild(overlay);

    function openMobileSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    function isMobile() { return window.innerWidth <= 991; }

    if (toggle) {
        toggle.addEventListener('click', function () {
            if (isMobile()) {
                sidebar.classList.contains('show') ? closeMobileSidebar() : openMobileSidebar();
            } else {
                document.body.classList.toggle('admin-sidebar-collapsed');
                try {
                    localStorage.setItem('stm_admin_sidebar_collapsed',
                        document.body.classList.contains('admin-sidebar-collapsed') ? '1' : '0');
                } catch (e) {}
            }
        });
    }

    overlay.addEventListener('click', closeMobileSidebar);

    /* Close sidebar on nav link click (mobile) */
    if (sidebar) {
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobile()) closeMobileSidebar();
            });
        });
    }

    /* Reset on resize */
    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMobileSidebar();
        }
    });

    /* --- Desktop collapsed state from localStorage --- */
    if (sidebar && toggle) {
        try {
            if (localStorage.getItem('stm_admin_sidebar_collapsed') === '1' && !isMobile()) {
                document.body.classList.add('admin-sidebar-collapsed');
            }
        } catch (e) {}
    }

    /* --- Clock --- */
    const clockEl = document.getElementById('adminClock');
    if (clockEl) {
        const tick = function () {
            clockEl.textContent = new Date().toLocaleTimeString();
        };
        tick();
        window.setInterval(tick, 1000);
    }

    /* --- Quill rich editor --- */
    const textareas = document.querySelectorAll('textarea.rich-editor');
    if (!textareas.length) return;

    const quills = [];
    textareas.forEach((textarea, idx) => {
        const wasRequired = textarea.hasAttribute('required');
        if (wasRequired) {
            textarea.removeAttribute('required');
        }
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
        quills.push({ textarea, quill, wasRequired, editorRoot: editor });
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (e) => {
            for (const { textarea, quill, wasRequired, editorRoot } of quills) {
                if (!form.contains(textarea)) continue;
                const html = quill.root.innerHTML;
                const text = (quill.getText() || '').trim();
                textarea.value = (text === '' ? '' : html);
                if (wasRequired && text === '') {
                    e.preventDefault();
                    e.stopPropagation();
                    editorRoot.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    quill.focus();
                    editorRoot.classList.add('is-invalid');
                    if (!editorRoot.nextElementSibling || !editorRoot.nextElementSibling.classList?.contains('quill-error')) {
                        const err = document.createElement('div');
                        err.className = 'invalid-feedback d-block quill-error';
                        err.textContent = 'This field is required.';
                        editorRoot.parentNode.insertBefore(err, editorRoot.nextSibling);
                    }
                    return;
                }
            }
        });
    });

    /* --- Sidebar accordion behavior --- */
    const navGroupHeaders = document.querySelectorAll('.admin-nav-group-header');
    navGroupHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            if (!targetId) return;
            const targetCollapse = document.querySelector(targetId);
            if (!targetCollapse) return;

            if (targetCollapse.classList.contains('show')) {
                return;
            }

            navGroupHeaders.forEach(otherHeader => {
                if (otherHeader === header) return;
                const otherTargetId = otherHeader.getAttribute('data-bs-target');
                if (!otherTargetId) return;
                const otherCollapse = document.querySelector(otherTargetId);
                if (otherCollapse && otherCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(otherCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    } else {
                        otherCollapse.classList.remove('show');
                    }
                }
            });
        });
    });
});
</script>
</body>
</html>
