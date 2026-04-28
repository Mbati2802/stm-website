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

        $stats = [
            'Programmes' => $model->countAll('programmes'),
            'Departments' => $model->countAll('departments'),
            'Media Posts' => $model->countAll('news') + $model->countAll('careers') + $model->countAll('tenders'),
            'Unread Messages' => $model->getUnreadPublicMessagesCount(),
            'Students' => count($portalModel->allStudents()),
            'Team Users' => $model->countAll('users'),
            'Applications' => $model->countAll('programme_applications'),
            'Traffic Hits' => $trafficHits30,
        ];

        $contentBreakdown = [
            'News' => $model->countAll('news'),
            'Careers' => $model->countAll('careers'),
            'Tenders' => $model->countAll('tenders'),
            'Events' => $model->countAll('events'),
            'Gallery' => $model->countAll('gallery'),
            'Library' => $model->countAll('library_resources'),
        ];
        $engagementBreakdown = [
            'Public Messages' => $model->countAll('messages'),
            'Support Tickets' => count($portalModel->getAllSupportTickets()),
            'Event Registrations' => $model->countAll('event_registrations'),
        ];

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
