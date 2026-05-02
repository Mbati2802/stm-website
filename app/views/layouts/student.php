<?php
$appName = $this->config['app_name'];
$studentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($metaTitle ?? 'Student Portal') ?> | <?= e($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/styles.css')) ?>" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/student-portal.css')) ?>" rel="stylesheet">
    <link rel="icon" href="<?= e(base_url('assets/images/logo.png')) ?>" type="image/png">
</head>
<body class="student-portal">
<?php if (str_ends_with($viewPath, 'student/login.php') || str_ends_with($viewPath, 'student/register.php')): ?>
<main class="student-auth-main">
    <?php include $viewPath; ?>
</main>
<?php else: ?>
<div class="student-portal-shell">
    <!-- Student Sidebar -->
    <aside class="student-sidebar" id="studentSidebar">
        <div class="student-sidebar-brand">
            <span class="student-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
            <div>
                <span class="fw-bold">Student Portal</span>
                <div class="student-user-info">
                    <small class="student-user-name"><?= e($_SESSION['student_name'] ?? 'Student') ?></small>
                </div>
            </div>
        </div>
        
        <nav class="nav flex-column gap-1 mt-2">
            <!-- Academic Section -->
            <div class="student-nav-group">
                <button class="student-nav-group-title student-nav-group-toggle" type="button">Academic <i class="bi bi-chevron-down"></i></button>
                <div class="student-nav-group-links">
                    <a class="nav-link <?= str_contains($studentPath, 'portal/dashboard') ? 'active' : '' ?>" href="<?= e(base_url('portal/dashboard')) ?>">
                        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/courses') ? 'active' : '' ?>" href="<?= e(base_url('portal/courses')) ?>">
                        <i class="bi bi-book"></i><span>My Courses</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/grades') ? 'active' : '' ?>" href="<?= e(base_url('portal/grades')) ?>">
                        <i class="bi bi-award"></i><span>Grades & Results</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/attendance') ? 'active' : '' ?>" href="<?= e(base_url('portal/attendance')) ?>">
                        <i class="bi bi-calendar-check"></i><span>Attendance</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/timetable') ? 'active' : '' ?>" href="<?= e(base_url('portal/timetable')) ?>">
                        <i class="bi bi-calendar-week"></i><span>Timetable</span>
                    </a>
                </div>
            </div>
            
            <!-- Resources Section -->
            <div class="student-nav-group">
                <button class="student-nav-group-title student-nav-group-toggle" type="button">Resources <i class="bi bi-chevron-down"></i></button>
                <div class="student-nav-group-links">
                    <a class="nav-link <?= str_contains($studentPath, 'portal/library') ? 'active' : '' ?>" href="<?= e(base_url('portal/library')) ?>">
                        <i class="bi bi-journal-text"></i><span>Library</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/assignments') ? 'active' : '' ?>" href="<?= e(base_url('portal/assignments')) ?>">
                        <i class="bi bi-file-earmark-text"></i><span>Assignments</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/resources') ? 'active' : '' ?>" href="<?= e(base_url('portal/resources')) ?>">
                        <i class="bi bi-folder"></i><span>Study Materials</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/exams') ? 'active' : '' ?>" href="<?= e(base_url('portal/exams')) ?>">
                        <i class="bi bi-clipboard-check"></i><span>Exams</span>
                    </a>
                </div>
            </div>
            
            <!-- Campus Life Section -->
            <div class="student-nav-group">
                <button class="student-nav-group-title student-nav-group-toggle" type="button">Campus Life <i class="bi bi-chevron-down"></i></button>
                <div class="student-nav-group-links">
                    <a class="nav-link <?= str_contains($studentPath, 'portal/events') ? 'active' : '' ?>" href="<?= e(base_url('portal/events')) ?>">
                        <i class="bi bi-calendar-event"></i><span>Events</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/clubs') ? 'active' : '' ?>" href="<?= e(base_url('portal/clubs')) ?>">
                        <i class="bi bi-people"></i><span>Clubs & Societies</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/announcements') ? 'active' : '' ?>" href="<?= e(base_url('portal/announcements')) ?>">
                        <i class="bi bi-megaphone"></i><span>Announcements</span>
                    </a>
                </div>
            </div>
            
            <!-- Services Section -->
            <div class="student-nav-group">
                <button class="student-nav-group-title student-nav-group-toggle" type="button">Services <i class="bi bi-chevron-down"></i></button>
                <div class="student-nav-group-links">
                    <a class="nav-link <?= str_contains($studentPath, 'portal/fees') ? 'active' : '' ?>" href="<?= e(base_url('portal/fees')) ?>">
                        <i class="bi bi-credit-card"></i><span>Fee Statement</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/clearance') ? 'active' : '' ?>" href="<?= e(base_url('portal/clearance')) ?>">
                        <i class="bi bi-check-circle"></i><span>Clearance</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/certificates') ? 'active' : '' ?>" href="<?= e(base_url('portal/certificates')) ?>">
                        <i class="bi bi-patch-check"></i><span>Certificates</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/support') ? 'active' : '' ?>" href="<?= e(base_url('portal/support')) ?>">
                        <i class="bi bi-headset"></i><span>IT Support</span>
                    </a>
                </div>
            </div>
            
            <!-- Account Section -->
            <div class="student-nav-group">
                <button class="student-nav-group-title student-nav-group-toggle" type="button">Account <i class="bi bi-chevron-down"></i></button>
                <div class="student-nav-group-links">
                    <a class="nav-link <?= str_contains($studentPath, 'portal/profile') ? 'active' : '' ?>" href="<?= e(base_url('portal/profile')) ?>">
                        <i class="bi bi-person"></i><span>My Profile</span>
                    </a>
                    <a class="nav-link <?= str_contains($studentPath, 'portal/settings') ? 'active' : '' ?>" href="<?= e(base_url('portal/settings')) ?>">
                        <i class="bi bi-gear"></i><span>Settings</span>
                    </a>
                    <a class="nav-link text-danger" href="<?= e(base_url('portal/logout')) ?>">
                        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                    </a>
                </div>
            </div>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="student-main">
        <header class="student-topbar">
            <button class="btn btn-outline-primary btn-sm" type="button" id="studentSidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="student-topbar-title">
                <strong><?= e($metaTitle ?? 'Student Portal') ?></strong>
                <span class="text-muted"><?= date('l, F j, Y') ?></span>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <div class="student-notifications">
                    <button class="btn btn-sm btn-outline-secondary position-relative">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                </div>
                <a class="btn btn-sm btn-outline-secondary" href="<?= e(base_url('/')) ?>" target="_blank">
                    <i class="bi bi-globe me-1"></i>Main Site
                </a>
            </div>
        </header>
        
        <?php include $viewPath; ?>
    </main>
</div>
<div class="student-sidebar-overlay" id="studentSidebarOverlay"></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('studentSidebar');
    const toggle = document.getElementById('studentSidebarToggle');
    const overlay = document.getElementById('studentSidebarOverlay');
    
    if (sidebar && toggle) {
        toggle.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                document.body.classList.toggle('student-sidebar-collapsed');
            }
        });
    }
    
    // Close sidebar when clicking overlay on mobile
    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    });

    // Compact group toggles on sidebar
    document.querySelectorAll('.student-nav-group').forEach(function (group, index) {
        const toggleBtn = group.querySelector('.student-nav-group-toggle');
        const links = group.querySelector('.student-nav-group-links');
        if (!toggleBtn || !links) return;
        const hasActive = links.querySelector('.nav-link.active') !== null;
        if (!hasActive && index > 0) {
            group.classList.add('collapsed');
        }
        toggleBtn.addEventListener('click', function () {
            group.classList.toggle('collapsed');
        });
    });
});
</script>
</body>
</html>
