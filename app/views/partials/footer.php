<footer class="pt-0 pb-3 mt-0 footer-wrap">
    <div class="site-width boxed-section footer-box text-light">
        <div class="row g-4">
            <div class="col-md-4">
                <h5>St. Mary's Mother and Child Hospital Medical Training College</h5>
                <p class="text-white-50">Empowering Kenyan students with practical, accredited and employability-focused training.</p>
                <div class="footer-socials d-flex align-items-center gap-3">
                    <a href="https://www.facebook.com/profile.php?id=61587395616193" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://wa.me/254791309011" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a class="text-white-50" href="<?= e(base_url()) ?>">Home</a></li>
                    <li><a class="text-white-50" href="<?= e(base_url('about')) ?>">About</a></li>
                    <li><a class="text-white-50" href="<?= e(base_url('programmes')) ?>">Programmes</a></li>
                    <li><a class="text-white-50" href="<?= e(base_url('media')) ?>">Media Desk</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Contact</h6>
                <p class="mb-1 text-white-50">Phone: <?= e($siteSettings['phone'] ?? '+254 791 309011 or +254101711499') ?></p>
                <p class="mb-1 text-white-50">Email: <?= e($siteSettings['email'] ?? 'contact@stmarysmchmcollege.ac.ke') ?></p>
                <p class="text-white-50">Location: <?= e($siteSettings['location'] ?? 'Amani House, along Biashara Street, Kiambu Town') ?></p>
            </div>
            <div class="col-md-3">
                <h6>Map</h6>
                <iframe title="Google map" class="w-100 rounded" height="140" src="https://maps.google.com/maps?q=Nairobi%20Kenya&t=&z=12&ie=UTF8&iwloc=&output=embed"></iframe>
            </div>
        </div>
        <hr class="border-light-subtle">
        <p class="small text-center text-white-50 mb-0">© <?= date('Y') ?> St. Mary's Mother and Child Hospital Medical Training College. All rights reserved.</p>
    </div>
</footer>
