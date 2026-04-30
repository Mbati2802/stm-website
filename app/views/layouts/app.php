<?php
$settingsModel = new ContentModel($this->config);
$siteSettings = $settingsModel->getSettings();
$appName = $this->config['app_name'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$metaTitleValue = trim((string)($metaTitle ?? ''));
$metaDescriptionValue = trim((string)($metaDescription ?? 'Modern career-focused technical training institution in Kenya. Apply today.'));
$metaRobots = trim((string)($metaRobots ?? 'index, follow'));
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
$canonicalPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$canonicalUrl = $scheme . '://' . $host . $canonicalPath;
$openGraphImage = trim((string)($metaImage ?? ''));
if ($openGraphImage === '') {
    $openGraphImage = base_url('assets/images/logo.png');
}
$siteUrl = $scheme . '://' . $host;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="<?= e($metaRobots) ?>">
    <title><?= e($metaTitleValue !== '' ? ($metaTitleValue . ' | ' . $appName) : $appName) ?></title>
    <meta name="description" content="<?= e($metaDescriptionValue) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($appName) ?>">
    <meta property="og:title" content="<?= e($metaTitleValue !== '' ? ($metaTitleValue . ' | ' . $appName) : $appName) ?>">
    <meta property="og:description" content="<?= e($metaDescriptionValue) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= e($openGraphImage) ?>">
    <meta property="og:locale" content="en_KE">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTitleValue !== '' ? ($metaTitleValue . ' | ' . $appName) : $appName) ?>">
    <meta name="twitter:description" content="<?= e($metaDescriptionValue) ?>">
    <meta name="twitter:image" content="<?= e($openGraphImage) ?>">

    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/styles.css')) ?>" rel="stylesheet">

    <!-- Schema.org Structured Data - Organization / College -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "EducationalOrganization",
        "name": <?= json_encode($appName) ?>,
        "url": <?= json_encode($siteUrl) ?>,
        "logo": <?= json_encode(base_url('assets/images/logo.png')) ?>,
        "description": <?= json_encode($metaDescriptionValue) ?>,
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nairobi",
            "addressCountry": "KE"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+254-791-309011",
            "contactType": "admissions"
        },
        "sameAs": []
    }
    </script>
</head>
<body>
    <?php include __DIR__ . '/../partials/topbar.php'; ?>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <?php include __DIR__ . '/../partials/page_banner.php'; ?>

    <main>
        <?php include $viewPath; ?>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <a class="whatsapp-float" href="https://wa.me/254791309011" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js" defer></script>
    <?php if (str_contains((string)($siteSettings['events_social_updates_html'] ?? ''), 'elfsight-app-')): ?>
        <script src="https://elfsightcdn.com/platform.js" async></script>
    <?php endif; ?>
    <script src="<?= e(base_url('assets/js/app.js')) ?>" defer></script>

    <script>
    // Lazy load images that don't have loading attribute
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('img:not([loading])').forEach(function(img) {
            img.setAttribute('loading', 'lazy');
        });
    });
    </script>
</body>
</html>
