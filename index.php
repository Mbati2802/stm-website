<?php
require_once __DIR__ . '/bootstrap.php';

require_once __DIR__ . '/app/controllers/admin/AdmissionNumberFormatsController.php';

$config = require __DIR__ . '/config/config.php';
try {
    $settings = (new ContentModel($config))->getSettings();
    if (is_array($settings)) {
        $config = array_merge($config, $settings);
    }
} catch (Throwable) {
    // Settings table may not exist yet.
}
session_name($config['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$router = new Router();
$adminLoginSlug = trim((string)($config['admin_login_slug'] ?? 'admin/login'), '/');
if ($adminLoginSlug === '') {
    $adminLoginSlug = 'admin/login';
}

$router->add('GET', 'sitemap.xml', [SeoController::class, 'sitemap']);
$router->add('GET', '', [HomeController::class, 'index']);
$router->add('GET', 'about', [AboutController::class, 'about']);
$router->add('GET', 'uniqueness', [AboutController::class, 'uniqueness']);
$router->add('GET', 'principal', [AboutController::class, 'principal']);
$router->add('GET', 'registrar', [AboutController::class, 'registrar']);
$router->add('GET', 'contact', [AboutController::class, 'contact']);
$router->add('POST', 'contact', [AboutController::class, 'submitContact']);
$router->add('GET', 'contact-registrar', [AboutController::class, 'registrarContact']);
$router->add('POST', 'contact-registrar', [AboutController::class, 'submitRegistrarContact']);
$router->add('GET', 'contact-admissions', [AboutController::class, 'admissionsContact']);
$router->add('POST', 'contact-admissions', [AboutController::class, 'submitAdmissionsContact']);
$router->add('GET', 'faqs', [AboutController::class, 'faqs']);
$router->add('GET', 'portals', [AboutController::class, 'portals']);
$router->add('GET', 'programmes', [ProgrammesController::class, 'index']);
$router->add('GET', 'departments', [DepartmentsController::class, 'index']);
$router->add('GET', 'programmes/how-to-apply', [ProgrammesController::class, 'howToApply']);
$router->add('GET', 'programmes/apply', [ProgrammesController::class, 'applyForm']);
$router->add('POST', 'programmes/apply', [ProgrammesController::class, 'submitApplication']);
$router->add('GET', 'programmes/{slug}', [ProgrammesController::class, 'show']);
$router->add('GET', 'library', [LibraryController::class, 'index']);
$router->add('GET', 'media', [MediaController::class, 'index']);
$router->add('GET', 'media/{type}/{slug}', [MediaController::class, 'show']);
$router->add('GET', 'gallery', [MediaController::class, 'gallery']);
$router->add('GET', 'testimonials', [HomeController::class, 'testimonials']);
$router->add('GET', 'events', [EventsController::class, 'index']);
$router->add('GET', 'events/{slug}', [EventsController::class, 'show']);
$router->add('GET', 'events/{slug}/register', [EventsController::class, 'registerForm']);
$router->add('POST', 'events/{slug}/register', [EventsController::class, 'submitRegistration']);

$router->add('GET', 'portal/register', [StudentPortalController::class, 'registerForm']);
$router->add('POST', 'portal/register', [StudentPortalController::class, 'register']);
$router->add('GET', 'portal/login', [StudentPortalController::class, 'loginForm']);
$router->add('POST', 'portal/login', [StudentPortalController::class, 'login']);
$router->add('GET', 'portal/logout', [StudentPortalController::class, 'logout']);
$router->add('GET', 'portal/dashboard', [StudentPortalController::class, 'dashboard']);
$router->add('GET', 'portal/forgot-password', [StudentPortalController::class, 'forgotPasswordForm']);
$router->add('POST', 'portal/forgot-password', [StudentPortalController::class, 'sendResetCode']);
$router->add('GET', 'portal/reset-password', [StudentPortalController::class, 'resetPasswordForm']);
$router->add('POST', 'portal/reset-password', [StudentPortalController::class, 'resetPassword']);

// Staff Portal (branded login page)
$router->add('GET', 'staff/login', [StaffAuthController::class, 'login']);
$router->add('POST', 'staff/login', [StaffAuthController::class, 'authenticate']);
$router->add('GET', 'staff/dashboard', [StaffAuthController::class, 'dashboard']);
$router->add('GET', 'staff/logout', [StaffAuthController::class, 'logout']);

// Student Portal - Academic Section
$router->add('GET', 'portal/courses', [StudentPortalController::class, 'courses']);
$router->add('GET', 'portal/grades', [StudentPortalController::class, 'grades']);
$router->add('GET', 'portal/attendance', [StudentPortalController::class, 'attendance']);
$router->add('GET', 'portal/timetable', [StudentPortalController::class, 'timetable']);

// Student Portal - Resources Section
$router->add('GET', 'portal/library', [StudentPortalController::class, 'library']);
$router->add('GET', 'portal/assignments', [StudentPortalController::class, 'assignments']);
$router->add('GET', 'portal/resources', [StudentPortalController::class, 'resources']);
$router->add('GET', 'portal/exams', [StudentPortalController::class, 'exams']);

// Student Portal - Campus Life Section
$router->add('GET', 'portal/events', [StudentPortalController::class, 'events']);
$router->add('GET', 'portal/clubs', [StudentPortalController::class, 'clubs']);
$router->add('GET', 'portal/announcements', [StudentPortalController::class, 'announcements']);

// Student Portal - Services Section
$router->add('GET', 'portal/fees', [StudentPortalController::class, 'fees']);
$router->add('GET', 'portal/clearance', [StudentPortalController::class, 'clearance']);
$router->add('GET', 'portal/certificates', [StudentPortalController::class, 'certificates']);
$router->add('GET', 'portal/support', [StudentPortalController::class, 'support']);
$router->add('POST', 'portal/support', [StudentPortalController::class, 'submitSupportTicket']);

// Student Portal - Account Section
$router->add('GET', 'portal/profile', [StudentPortalController::class, 'profile']);
$router->add('GET', 'portal/settings', [StudentPortalController::class, 'settings']);

$router->add('GET', $adminLoginSlug, [AdminAuthController::class, 'login']);
$router->add('POST', $adminLoginSlug, [AdminAuthController::class, 'authenticate']);
$router->add('GET', 'admin/logout', [AdminAuthController::class, 'logout']);
$router->add('GET', 'admin', [AdminDashboardController::class, 'index']);
$router->add('GET', 'admin/admission-number-formats', ['AdmissionNumberFormatsController', 'index']);
$router->add('GET', 'admin/admission-number-formats/create', ['AdmissionNumberFormatsController', 'create']);
$router->add('POST', 'admin/admission-number-formats/create', ['AdmissionNumberFormatsController', 'create']);
$router->add('GET', 'admin/admission-number-formats/edit/{id}', ['AdmissionNumberFormatsController', 'edit']);
$router->add('POST', 'admin/admission-number-formats/edit/{id}', ['AdmissionNumberFormatsController', 'edit']);
$router->add('GET', 'admin/admission-number-formats/delete/{id}', ['AdmissionNumberFormatsController', 'delete']);
$router->add('GET', 'admin/admission-number-formats/set-default/{id}', ['AdmissionNumberFormatsController', 'setDefault']);
$router->add('GET', 'admin/list/{entity}', [AdminContentController::class, 'list']);
$router->add('GET', 'admin/create/{entity}', [AdminContentController::class, 'create']);
$router->add('POST', 'admin/create/{entity}', [AdminContentController::class, 'store']);
$router->add('GET', 'admin/edit/{entity}/{id}', [AdminContentController::class, 'edit']);
$router->add('POST', 'admin/edit/{entity}/{id}', [AdminContentController::class, 'update']);
$router->add('GET', 'admin/toggle/{entity}/{id}', [AdminContentController::class, 'toggleVisibility']);
$router->add('GET', 'admin/delete/{entity}/{id}', [AdminContentController::class, 'delete']);
$router->add('GET', 'admin/messages', [AdminContentController::class, 'messages']);
$router->add('GET', 'admin/messages/view/{id}', [AdminContentController::class, 'viewMessage']);
$router->add('POST', 'admin/messages/reply/{id}', [AdminContentController::class, 'replyMessage']);
$router->add('GET', 'admin/support-tickets', [AdminContentController::class, 'supportTickets']);
$router->add('GET', 'admin/messages/export', [AdminContentController::class, 'exportMessages']);
$router->add('GET', 'admin/event-registrations', [AdminContentController::class, 'eventRegistrations']);
$router->add('POST', 'admin/event-registrations/email/{id}', [AdminContentController::class, 'emailEventRegistrant']);
$router->add('GET', 'admin/students', [AdminContentController::class, 'students']);
$router->add('GET', 'admin/students/view', [AdminContentController::class, 'viewStudent']);
$router->add('GET', 'admin/students/edit-form', [AdminContentController::class, 'editStudentForm']);
$router->add('POST', 'admin/students/edit', [AdminContentController::class, 'editStudent']);
$router->add('POST', 'admin/students/reset-password', [AdminContentController::class, 'resetStudentPassword']);
$router->add('POST', 'admin/students/suspend', [AdminContentController::class, 'suspendStudent']);
$router->add('POST', 'admin/students/delete', [AdminContentController::class, 'deleteStudent']);
$router->add('POST', 'admin/students/assign', [AdminContentController::class, 'assignStudentAdmissionNumber']);
$router->add('POST', 'admin/students/assign/{id}', [AdminContentController::class, 'assignStudentAdmissionNumber']);
$router->add('POST', 'admin/students/bulk-assign', [AdminContentController::class, 'bulkAssignAdmissionNumbers']);
$router->add('GET', 'admin/admission', [AdminContentController::class, 'admission']);
$router->add('POST', 'admin/admission', [AdminContentController::class, 'submitAdmission']);
$router->add('GET', 'admin/deleted-records', [AdminContentController::class, 'deletedRecords']);
$router->add('POST', 'admin/deleted-records/restore', [AdminContentController::class, 'restoreRecord']);
$router->add('GET', 'admin/grading', [GradingController::class, 'index']);
$router->add('POST', 'admin/grading/exam-type/create', [GradingController::class, 'createExamType']);
$router->add('POST', 'admin/grading/exam-type/edit', [GradingController::class, 'editExamType']);
$router->add('POST', 'admin/grading/exam-type/delete', [GradingController::class, 'deleteExamType']);
$router->add('POST', 'admin/grading/grading-system/create', [GradingController::class, 'createGradingSystem']);
$router->add('POST', 'admin/grading/grading-system/edit', [GradingController::class, 'editGradingSystem']);
$router->add('POST', 'admin/grading/grading-system/delete', [GradingController::class, 'deleteGradingSystem']);
$router->add('POST', 'admin/grading/grade-range/add', [GradingController::class, 'addGradeRange']);
$router->add('POST', 'admin/grading/grade-range/edit', [GradingController::class, 'editGradeRange']);
$router->add('POST', 'admin/grading/grade-range/delete', [GradingController::class, 'deleteGradeRange']);
$router->add('GET', 'admin/grading/grade-ranges', [GradingController::class, 'getGradeRanges']);
$router->add('GET', 'admin/grading/calculate-grade', [GradingController::class, 'calculateGrade']);
$router->add('GET', 'admin/media', [AdminContentController::class, 'mediaLibrary']);
$router->add('POST', 'admin/media/upload', [AdminContentController::class, 'uploadMedia']);
$router->add('GET', 'admin/media/delete/{id}', [AdminContentController::class, 'deleteMedia']);
$router->add('GET', 'admin/settings', [AdminContentController::class, 'settings']);
$router->add('POST', 'admin/settings', [AdminContentController::class, 'saveSettings']);
$router->add('POST', 'admin/settings/partial', [AdminContentController::class, 'savePartialSettings']);
$router->add('POST', 'admin/social-fetch/run', [AdminContentController::class, 'runSocialFetch']);
$router->add('GET', 'admin/social-fetch/debug', [AdminContentController::class, 'debugSocialFetch']);
$router->add('GET', 'cron/social-fetch', [AdminContentController::class, 'cronSocialFetch']);
$router->add('GET', 'admin/internal-messages', [AdminContentController::class, 'internalMessages']);
$router->add('POST', 'admin/internal-messages/send', [AdminContentController::class, 'sendInternalMessage']);
$router->add('GET', 'admin/applications', [AdminContentController::class, 'applications']);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

$router->dispatch($_SERVER['REQUEST_METHOD'], trim($uri, '/'));
