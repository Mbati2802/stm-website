<?php
$appName = $this->config['app_name'];
$adminPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
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
    <aside class="admin-sidebar">
        <h5 class="mb-3">Admin Panel</h5>
        <nav class="nav flex-column gap-1">
            <a class="nav-link <?= str_ends_with($adminPath, 'admin') ? 'active' : '' ?>" href="<?= e(base_url('admin')) ?>">Dashboard</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/programmes') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/programmes')) ?>">Programmes</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/departments') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/departments')) ?>">Departments</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/news') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/news')) ?>">News</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/careers') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/careers')) ?>">Careers</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/tenders') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/tenders')) ?>">Tenders</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/gallery') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/gallery')) ?>">Gallery</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/library_resources') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/library_resources')) ?>">Library</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/faqs') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/faqs')) ?>">FAQs</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/list/pages') ? 'active' : '' ?>" href="<?= e(base_url('admin/list/pages')) ?>">Pages</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/messages') ? 'active' : '' ?>" href="<?= e(base_url('admin/messages')) ?>">Messages</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/event-registrations') ? 'active' : '' ?>" href="<?= e(base_url('admin/event-registrations')) ?>">Event Registrations</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/students') ? 'active' : '' ?>" href="<?= e(base_url('admin/students')) ?>">Student Accounts</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/media') ? 'active' : '' ?>" href="<?= e(base_url('admin/media')) ?>">Media Library</a>
            <a class="nav-link <?= str_contains($adminPath, 'admin/settings') ? 'active' : '' ?>" href="<?= e(base_url('admin/settings')) ?>">UI Content Settings</a>
            <a class="nav-link text-danger" href="<?= e(base_url('admin/logout')) ?>">Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <?php include $viewPath; ?>
    </main>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
