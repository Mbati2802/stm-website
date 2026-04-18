<?php
require_once __DIR__ . '/bootstrap.php';

$config = require __DIR__ . '/config/config.php';
session_name($config['session_name']);
session_start();

$router = new Router();

$router->add('GET', '', [HomeController::class, 'index']);
$router->add('GET', 'about', [AboutController::class, 'about']);
$router->add('GET', 'uniqueness', [AboutController::class, 'uniqueness']);
$router->add('GET', 'principal', [AboutController::class, 'principal']);
$router->add('GET', 'registrar', [AboutController::class, 'registrar']);
$router->add('GET', 'contact', [AboutController::class, 'contact']);
$router->add('POST', 'contact', [AboutController::class, 'submitContact']);
$router->add('GET', 'contact-registrar', [AboutController::class, 'registrarContact']);
$router->add('POST', 'contact-registrar', [AboutController::class, 'submitRegistrarContact']);
$router->add('GET', 'faqs', [AboutController::class, 'faqs']);
$router->add('GET', 'portals', [AboutController::class, 'portals']);
$router->add('GET', 'programmes', [ProgrammesController::class, 'index']);
$router->add('GET', 'programmes/how-to-apply', [ProgrammesController::class, 'howToApply']);
$router->add('GET', 'programmes/apply', [ProgrammesController::class, 'applyForm']);
$router->add('POST', 'programmes/apply', [ProgrammesController::class, 'submitApplication']);
$router->add('GET', 'programmes/{slug}', [ProgrammesController::class, 'show']);
$router->add('GET', 'library', [LibraryController::class, 'index']);
$router->add('GET', 'media', [MediaController::class, 'index']);
$router->add('GET', 'gallery', [MediaController::class, 'gallery']);
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
$router->add('GET', 'staff/login', [AdminAuthController::class, 'login']);
$router->add('POST', 'staff/login', [AdminAuthController::class, 'authenticate']);

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

// Student Portal - Account Section
$router->add('GET', 'portal/profile', [StudentPortalController::class, 'profile']);
$router->add('GET', 'portal/settings', [StudentPortalController::class, 'settings']);

$router->add('GET', 'admin/login', [AdminAuthController::class, 'login']);
$router->add('POST', 'admin/login', [AdminAuthController::class, 'authenticate']);
$router->add('GET', 'admin/logout', [AdminAuthController::class, 'logout']);
$router->add('GET', 'admin', [AdminDashboardController::class, 'index']);
$router->add('GET', 'admin/list/{entity}', [AdminContentController::class, 'list']);
$router->add('GET', 'admin/create/{entity}', [AdminContentController::class, 'create']);
$router->add('POST', 'admin/create/{entity}', [AdminContentController::class, 'store']);
$router->add('GET', 'admin/edit/{entity}/{id}', [AdminContentController::class, 'edit']);
$router->add('POST', 'admin/edit/{entity}/{id}', [AdminContentController::class, 'update']);
$router->add('GET', 'admin/toggle/{entity}/{id}', [AdminContentController::class, 'toggleVisibility']);
$router->add('GET', 'admin/delete/{entity}/{id}', [AdminContentController::class, 'delete']);
$router->add('GET', 'admin/messages', [AdminContentController::class, 'messages']);
$router->add('GET', 'admin/messages/export', [AdminContentController::class, 'exportMessages']);
$router->add('GET', 'admin/event-registrations', [AdminContentController::class, 'eventRegistrations']);
$router->add('GET', 'admin/students', [AdminContentController::class, 'students']);
$router->add('POST', 'admin/students/assign/{id}', [AdminContentController::class, 'assignStudentAdmissionNumber']);
$router->add('POST', 'admin/students/bulk-assign', [AdminContentController::class, 'bulkAssignAdmissionNumbers']);
$router->add('GET', 'admin/media', [AdminContentController::class, 'mediaLibrary']);
$router->add('POST', 'admin/media/upload', [AdminContentController::class, 'uploadMedia']);
$router->add('GET', 'admin/media/delete/{id}', [AdminContentController::class, 'deleteMedia']);
$router->add('GET', 'admin/settings', [AdminContentController::class, 'settings']);
$router->add('POST', 'admin/settings', [AdminContentController::class, 'saveSettings']);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

$router->dispatch($_SERVER['REQUEST_METHOD'], trim($uri, '/'));
