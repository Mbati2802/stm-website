<?php
class AdminDashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $model = new ContentModel($this->config);

        $stats = [
            'Programmes' => count($model->all('programmes')),
            'Departments' => count($model->all('departments')),
            'Media Posts' => count($model->all('news')) + count($model->all('careers')) + count($model->all('tenders')),
            'Messages' => count($model->all('messages')),
        ];

        $this->view('admin/dashboard', ['metaTitle' => 'Admin Dashboard', 'stats' => $stats]);
    }
}
