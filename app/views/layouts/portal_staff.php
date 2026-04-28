<?php
$appName = $this->config['app_name'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Staff Portal') ?> | <?= e($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/staff-portal.css')) ?>" rel="stylesheet">
</head>
<body class="portal-body staff-portal-body">
    <header class="portal-topbar staff-topbar">
        <div class="portal-shell d-flex align-items-center justify-content-between gap-2">
            <a class="portal-brand" href="<?= e(base_url('staff/login')) ?>">
                <i class="bi bi-person-badge"></i> Staff Portal
            </a>
            <a class="btn btn-sm btn-outline-light" href="<?= e(base_url()) ?>">Back to Website</a>
        </div>
    </header>
    <main class="portal-shell py-4">
        <?php include $viewPath; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
