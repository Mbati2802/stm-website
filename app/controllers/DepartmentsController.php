<?php
class DepartmentsController extends Controller
{
    public function index(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_programmes')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }

        $departments = $model->all('departments');
        $programmes = $model->getProgrammes();
        $counts = [];
        foreach ($programmes as $programme) {
            $departmentId = (int)($programme['department_id'] ?? 0);
            if ($departmentId > 0) {
                $counts[$departmentId] = (int)($counts[$departmentId] ?? 0) + 1;
            }
        }

        $this->view('pages/departments', [
            'metaTitle' => 'Departments',
            'metaDescription' => 'Explore academic departments and available programmes at St. Mary\'s MCHM College.',
            'departments' => $departments,
            'programmeCounts' => $counts,
        ]);
    }
}
