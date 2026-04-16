<?php
$settingsModel = new ContentModel($this->config);
$siteSettings = $settingsModel->getSettings();
$appName = $this->config['app_name'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? '') ?> | <?= e($appName) ?></title>
    <meta name="description" content="Modern career-focused technical training institution in Kenya. Apply today.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/styles.css')) ?>" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../partials/topbar.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <main>
        <?php include $viewPath; ?>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <a class="whatsapp-float" href="https://wa.me/254700000000" target="_blank" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="<?= e(base_url('assets/js/app.js')) ?>"></script>
</body>
</html>
