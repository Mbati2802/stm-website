<?php
$phone = $siteSettings['phone'] ?? '+254 700 000 000';
$email = $siteSettings['email'] ?? 'admissions@stm.ac.ke';
$location = $siteSettings['location'] ?? 'Nairobi, Kenya';
$topMessage = $siteSettings['top_message'] ?? 'Admissions Open - Apply Today';
?>
<div class="topbar py-2">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2 small">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <span><i class="bi bi-telephone-fill me-1"></i><?= e($phone) ?></span>
            <span><i class="bi bi-envelope-fill me-1"></i><?= e($email) ?></span>
            <span><i class="bi bi-geo-alt-fill me-1"></i><?= e($location) ?></span>
        </div>
        <div class="ticker-wrap"><span class="ticker"><?= e($topMessage) ?> • Applications for May intake ongoing • Scholarships available</span></div>
        <div class="d-flex align-items-center gap-2">
            <a href="https://www.facebook.com/profile.php?id=61587395616193" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://wa.me/254700000000" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
    </div>
</div>
