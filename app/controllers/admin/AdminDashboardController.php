<?php
class AdminDashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $model = new ContentModel($this->config);
        $portalModel = new StudentPortalModel($this->config);
        $trafficTrend = $model->getDailyTrend('page_visits', 30);
        $trafficHits30 = array_sum(array_map(static fn($d) => (int)($d['total'] ?? 0), $trafficTrend));
        $applicationTrend = $model->getDailyTrend('programme_applications', 30);

        // Build stats based on permissions
        $stats = [];
        if (Auth::canViewEntity('programmes')) {
            $stats['Programmes'] = $model->countAll('programmes');
        }
        if (Auth::canViewEntity('departments')) {
            $stats['Departments'] = $model->countAll('departments');
        }
        if (Auth::canViewEntity('news') || Auth::canViewEntity('careers') || Auth::canViewEntity('tenders')) {
            $mediaCount = 0;
            if (Auth::canViewEntity('news')) $mediaCount += $model->countAll('news');
            if (Auth::canViewEntity('careers')) $mediaCount += $model->countAll('careers');
            if (Auth::canViewEntity('tenders')) $mediaCount += $model->countAll('tenders');
            $stats['Media Posts'] = $mediaCount;
        }
        if (Auth::canViewEntity('messages')) {
            $stats['Unread Messages'] = $model->getUnreadPublicMessagesCount();
        }
        if (Auth::canViewEntity('students')) {
            $stats['Students'] = count($portalModel->allStudents());
        }
        if (Auth::canViewEntity('users')) {
            $stats['Team Users'] = $model->countAll('users');
        }
        if (Auth::canManageEntity('messages')) {
            $stats['Applications'] = $model->countAll('programme_applications');
        }
        $stats['Traffic Hits'] = $trafficHits30;

        // Build content breakdown based on permissions
        $contentBreakdown = [];
        if (Auth::canViewEntity('news')) $contentBreakdown['News'] = $model->countAll('news');
        if (Auth::canViewEntity('careers')) $contentBreakdown['Careers'] = $model->countAll('careers');
        if (Auth::canViewEntity('tenders')) $contentBreakdown['Tenders'] = $model->countAll('tenders');
        if (Auth::canViewEntity('events')) $contentBreakdown['Events'] = $model->countAll('events');
        if (Auth::canViewEntity('gallery')) $contentBreakdown['Gallery'] = $model->countAll('gallery');
        if (Auth::canViewEntity('library_resources')) $contentBreakdown['Library'] = $model->countAll('library_resources');
        
        // Build engagement breakdown based on permissions
        $engagementBreakdown = [];
        if (Auth::canViewEntity('messages')) {
            $engagementBreakdown['Public Messages'] = $model->countAll('messages');
        }
        if (Auth::canViewEntity('students')) {
            $engagementBreakdown['Support Tickets'] = count($portalModel->getAllSupportTickets());
        }
        if (Auth::canManageEntity('events')) {
            $engagementBreakdown['Event Registrations'] = $model->countAll('event_registrations');
        }

        $users = $model->all('users');
        $roleCounts = ['Super Admin' => 0, 'Senior Admin' => 0, 'Editor' => 0, 'Viewer' => 0, 'Registrar' => 0, 'Teacher' => 0];
        foreach ($users as $user) {
            $role = (string)($user['role'] ?? '');
            if ($role === 'super_admin') $roleCounts['Super Admin']++;
            if ($role === 'junior_admin') $roleCounts['Senior Admin']++;
            if ($role === 'editor') $roleCounts['Editor']++;
            if ($role === 'viewer') $roleCounts['Viewer']++;
            if ($role === 'registrar') $roleCounts['Registrar']++;
            if ($role === 'teacher') $roleCounts['Teacher']++;
        }

        $this->view('admin/dashboard', [
            'metaTitle' => 'Admin Dashboard',
            'stats' => $stats,
            'contentBreakdown' => $contentBreakdown,
            'engagementBreakdown' => $engagementBreakdown,
            'roleCounts' => $roleCounts,
            'recentMessages' => array_slice($model->all('messages'), 0, 5),
            'recentEvents' => array_slice($model->all('events'), 0, 5),
            'applicationTrend' => $applicationTrend,
            'trafficTrend' => $trafficTrend,
            'topPages' => $model->getTopVisitedPages(8, false),
            'topCourses' => $model->getTopCourseViews(8),
            'recentBlockedLogins' => $model->getRecentBlockedLoginAttempts(5),
        ]);
    }
}
