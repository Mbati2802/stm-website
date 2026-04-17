<nav id="mainNav" class="navbar navbar-expand-lg bg-white shadow-sm sticky-top main-nav">
    <div class="site-width d-flex flex-wrap align-items-center justify-content-between">
        <?php $logoPublic = 'assets/images/logo.png'; ?>
        <?php
        $logoCandidates = [
            __DIR__ . '/../../../public/' . $logoPublic,
            __DIR__ . '/../../../' . $logoPublic,
        ];
        $logoFile = '';
        foreach ($logoCandidates as $candidate) {
            if (file_exists($candidate)) {
                $logoFile = $candidate;
                break;
            }
        }
        ?>
        <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= e(base_url()) ?>">
            <?php if ($logoFile !== ''): ?>
                <img src="<?= e(base_url($logoPublic)) ?>" alt="College logo" class="site-logo">
            <?php else: ?>
                St. Mary's Mother & Child Hospital MTC
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse flex-grow-1" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link <?= $path === '' ? 'active' : '' ?>" href="<?= e(base_url()) ?>">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($path, ['about', 'principal', 'registrar', 'contact', 'faqs']) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">About Us <i class="bi bi-chevron-down dropdown-arrow"></i></a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= e(base_url('about')) ?>">About St. Mary's</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('about')) ?>">College Uniqueness</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('principal')) ?>">The Principal</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('registrar')) ?>">Registrar</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('about')) ?>">Downloads</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('contact')) ?>">Contact Us</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('faqs')) ?>">FAQs</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= str_starts_with($path, 'programmes') ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">Programmes <i class="bi bi-chevron-down dropdown-arrow"></i></a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= e(base_url('programmes')) ?>">Departments</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('programmes')) ?>">Programmes</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('programmes')) ?>">Timetables</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('programmes/how-to-apply')) ?>">How To Apply</a></li>
                        <li><a class="dropdown-item" href="<?= e(base_url('programmes')) ?>">Short Courses</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link <?= $path === 'events' ? 'active' : '' ?>" href="<?= e(base_url('events')) ?>">Events</a></li>
                <li class="nav-item"><a class="nav-link <?= $path === 'library' ? 'active' : '' ?>" href="<?= e(base_url('library')) ?>">Library</a></li>
                <li class="nav-item"><a class="nav-link <?= in_array($path, ['media', 'gallery']) ? 'active' : '' ?>" href="<?= e(base_url('media')) ?>">Media Desk</a></li>
                <li class="nav-item"><a class="btn btn-primary ms-lg-2" href="<?= e(base_url('contact')) ?>">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>
