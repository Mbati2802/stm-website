<?php
class LibraryController extends Controller
{
    public function index(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_library')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');
        $result = $model->paginate('library_resources', $page, 8, $search ?: null);

        $this->view('pages/library', [
            'metaTitle' => 'Library',
            'items' => $result['data'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => 8,
            'search' => $search,
        ]);
    }
}
