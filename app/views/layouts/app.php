<?php
$settingsModel = new ContentModel($this->config);
$siteSettings = $settingsModel->getSettings();
$appName = $this->config['app_name'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$metaTitleValue = trim((string)($metaTitle ?? ''));
$metaDescriptionValue = trim((string)($metaDescription ?? 'Modern career-focused technical training institution in Kenya. Apply today.'));
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
$canonicalPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$canonicalUrl = $scheme . '://' . $host . $canonicalPath;
$openGraphImage = trim((string)($metaImage ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitleValue !== '' ? ($metaTitleValue . ' | ' . $appName) : $appName) ?></title>
    <meta name="description" content="<?= e($metaDescriptionValue) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($metaTitleValue !== '' ? ($metaTitleValue . ' | ' . $appName) : $appName) ?>">
    <meta property="og:description" content="<?= e($metaDescriptionValue) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <?php if ($openGraphImage !== ''): ?><meta property="og:image" content="<?= e($openGraphImage) ?>"><?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTitleValue !== '' ? ($metaTitleValue . ' | ' . $appName) : $appName) ?>">
    <meta name="twitter:description" content="<?= e($metaDescriptionValue) ?>">
    <?php if ($openGraphImage !== ''): ?><meta name="twitter:image" content="<?= e($openGraphImage) ?>"><?php endif; ?>
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
    <?php include __DIR__ . '/../partials/page_banner.php'; ?>

    <main>
        <?php include $viewPath; ?>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <a class="whatsapp-float" href="https://wa.me/254791309011" target="_blank" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="<?= e(base_url('assets/js/app.js')) ?>"></script>
</body>
</html>
