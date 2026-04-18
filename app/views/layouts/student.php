<?php
$appName = $this->config['app_name'];
$studentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Student Portal') ?> | <?= e($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/styles.css')) ?>" rel="stylesheet">
    <link href="<?= e(base_url('assets/css/student-portal.css')) ?>" rel="stylesheet">
    <link rel="icon" href="<?= e(base_url('assets/images/favicon.svg')) ?>" type="image/svg+xml">
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
                    <small class="text-muted"><?= e($_SESSION['student_name'] ?? 'Student') ?></small>
                </div>
            </div>
        </div>
        
        <nav class="nav flex-column gap-1 mt-3">
            <!-- Academic Section -->
            <div class="student-nav-group">
                <div class="student-nav-group-title">Academic</div>
                <a class="nav-link <?= str_contains($studentPath, 'student/dashboard') ? 'active' : '' ?>" href="<?= e(base_url('student/dashboard')) ?>">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/courses') ? 'active' : '' ?>" href="<?= e(base_url('student/courses')) ?>">
                    <i class="bi bi-book"></i><span>My Courses</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/grades') ? 'active' : '' ?>" href="<?= e(base_url('student/grades')) ?>">
                    <i class="bi bi-award"></i><span>Grades & Results</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/attendance') ? 'active' : '' ?>" href="<?= e(base_url('student/attendance')) ?>">
                    <i class="bi bi-calendar-check"></i><span>Attendance</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/timetable') ? 'active' : '' ?>" href="<?= e(base_url('student/timetable')) ?>">
                    <i class="bi bi-calendar-week"></i><span>Timetable</span>
                </a>
            </div>
            
            <!-- Resources Section -->
            <div class="student-nav-group">
                <div class="student-nav-group-title">Resources</div>
                <a class="nav-link <?= str_contains($studentPath, 'student/library') ? 'active' : '' ?>" href="<?= e(base_url('student/library')) ?>">
                    <i class="bi bi-journal-text"></i><span>Library</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/assignments') ? 'active' : '' ?>" href="<?= e(base_url('student/assignments')) ?>">
                    <i class="bi bi-file-earmark-text"></i><span>Assignments</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/resources') ? 'active' : '' ?>" href="<?= e(base_url('student/resources')) ?>">
                    <i class="bi bi-folder"></i><span>Study Materials</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/exams') ? 'active' : '' ?>" href="<?= e(base_url('student/exams')) ?>">
                    <i class="bi bi-clipboard-check"></i><span>Exams</span>
                </a>
            </div>
            
            <!-- Campus Life Section -->
            <div class="student-nav-group">
                <div class="student-nav-group-title">Campus Life</div>
                <a class="nav-link <?= str_contains($studentPath, 'student/events') ? 'active' : '' ?>" href="<?= e(base_url('student/events')) ?>">
                    <i class="bi bi-calendar-event"></i><span>Events</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/clubs') ? 'active' : '' ?>" href="<?= e(base_url('student/clubs')) ?>">
                    <i class="bi bi-people"></i><span>Clubs & Societies</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/announcements') ? 'active' : '' ?>" href="<?= e(base_url('student/announcements')) ?>">
                    <i class="bi bi-megaphone"></i><span>Announcements</span>
                </a>
            </div>
            
            <!-- Services Section -->
            <div class="student-nav-group">
                <div class="student-nav-group-title">Services</div>
                <a class="nav-link <?= str_contains($studentPath, 'student/fees') ? 'active' : '' ?>" href="<?= e(base_url('student/fees')) ?>">
                    <i class="bi bi-credit-card"></i><span>Fee Statement</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/clearance') ? 'active' : '' ?>" href="<?= e(base_url('student/clearance')) ?>">
                    <i class="bi bi-check-circle"></i><span>Clearance</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/certificates') ? 'active' : '' ?>" href="<?= e(base_url('student/certificates')) ?>">
                    <i class="bi bi-patch-check"></i><span>Certificates</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/support') ? 'active' : '' ?>" href="<?= e(base_url('student/support')) ?>">
                    <i class="bi bi-headset"></i><span>IT Support</span>
                </a>
            </div>
            
            <!-- Account Section -->
            <div class="student-nav-group">
                <div class="student-nav-group-title">Account</div>
                <a class="nav-link <?= str_contains($studentPath, 'student/profile') ? 'active' : '' ?>" href="<?= e(base_url('student/profile')) ?>">
                    <i class="bi bi-person"></i><span>My Profile</span>
                </a>
                <a class="nav-link <?= str_contains($studentPath, 'student/settings') ? 'active' : '' ?>" href="<?= e(base_url('student/settings')) ?>">
                    <i class="bi bi-gear"></i><span>Settings</span>
                </a>
                <a class="nav-link text-danger" href="<?= e(base_url('student/logout')) ?>">
                    <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                </a>
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
});
</script>
</body>
</html>
