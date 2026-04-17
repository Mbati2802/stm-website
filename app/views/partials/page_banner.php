<?php
$pathKey = $path === '' ? 'home' : explode('/', $path)[0];
$map = [
    'home' => 'banner_home',
    'programmes' => 'banner_programmes',
    'about' => 'banner_about',
    'contact' => 'banner_contact',
    'events' => 'banner_events',
    'library' => 'banner_library',
    'media' => 'banner_media',
    'gallery' => 'banner_media',
];
$settingKey = $map[$pathKey] ?? null;
$bannerImage = $settingKey !== null ? trim((string)($siteSettings[$settingKey] ?? '')) : '';
$bannerHeight = (int)($siteSettings['banner_default_height'] ?? 300);
if ($bannerHeight < 120) {
    $bannerHeight = 120;
}
if ($bannerHeight > 700) {
    $bannerHeight = 700;
}
?>
<?php if ($bannerImage !== ''): ?>
<section class="section-stack">
    <div class="site-width boxed-section py-2">
        <img src="<?= e($bannerImage) ?>" alt="Page banner" class="img-fluid w-100 dynamic-page-banner" style="max-height: <?= e((string)$bannerHeight) ?>px;">
    </div>
</section>
<?php endif; ?>
