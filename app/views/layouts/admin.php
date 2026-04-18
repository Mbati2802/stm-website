<?php
$appName = $this->config['app_name'];
$adminPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$isAdminHome = $adminPath === 'admin';
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
<body class="admin-theme">
<?php if (str_ends_with($viewPath, 'admin/login.php')): ?>
<main class="admin-main">
    <?php include $viewPath; ?>
</main>
<?php else: ?>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-brand">
            <span class="admin-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
            <span class="fw-bold">STM Admin</span>
        </div>
        <nav class="nav flex-column gap-2 mt-3">
            <a class="nav-link <?= $isAdminHome ? 'active' : '' ?>" href="<?= e(base_url('admin')) ?>"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">Content</div>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/programmes') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/programmes')) ?>"><i class="bi bi-journal-text"></i><span>Programmes</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/departments') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/departments')) ?>"><i class="bi bi-diagram-3"></i><span>Departments</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/news') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/news')) ?>"><i class="bi bi-newspaper"></i><span>News</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/careers') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/careers')) ?>"><i class="bi bi-briefcase"></i><span>Careers</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/tenders') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/tenders')) ?>"><i class="bi bi-file-earmark-check"></i><span>Tenders</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/faqs') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/faqs')) ?>"><i class="bi bi-question-circle"></i><span>FAQs</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/pages') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/pages')) ?>"><i class="bi bi-file-richtext"></i><span>Pages</span></a>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">Media and Events</div>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/gallery') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/gallery')) ?>"><i class="bi bi-images"></i><span>Gallery</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/list/library_resources') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/library_resources')) ?>"><i class="bi bi-book"></i><span>Library</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/media') ? 'active' : '' ?>" href="<?= e(base_url('admin/media')) ?>"><i class="bi bi-folder2-open"></i><span>Media Library</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/event-registrations') ? 'active' : '' ?>" href="<?= e(base_url('admin/event-registrations')) ?>"><i class="bi bi-calendar2-week"></i><span>Event Registrations</span></a>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">Communication and Users</div>
                <a class="nav-link <?= str_contains($adminPath, 'admin/messages') ? 'active' : '' ?>" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-envelope"></i><span>Messages</span></a>
                <a class="nav-link <?= str_contains($adminPath, 'admin/students') ? 'active' : '' ?>" href="<?= e(base_url('admin/students')) ?>"><i class="bi bi-people"></i><span>Student Accounts</span></a>
            </div>

            <div class="admin-nav-group">
                <div class="admin-nav-group-title">System</div>
                <a class="nav-link <?= str_contains($adminPath, 'admin/settings') ? 'active' : '' ?>" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-sliders"></i><span>UI Content Settings</span></a>
                <a class="nav-link text-danger" href="<?= e(base_url('admin/logout')) ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </div>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <button class="btn btn-outline-primary btn-sm" type="button" id="adminSidebarToggle"><i class="bi bi-list"></i></button>
            <div class="admin-topbar-title">
                <strong><?= e($metaTitle ?? 'Admin') ?></strong>
                <span class="text-muted">Content Management</span>
            </div>
            <a class="btn btn-sm btn-primary" href="<?= e(base_url('admin/settings')) ?>"><i class="bi bi-gear me-1"></i>Settings</a>
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
        toggle.addEventListener('click', function () {
            document.body.classList.toggle('admin-sidebar-collapsed');
        });
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
