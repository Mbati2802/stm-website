<?php
class MediaController extends Controller
{
    public function index(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_media')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $type = $_GET['type'] ?? 'news';
        if (!in_array($type, ['news', 'careers', 'tenders'], true)) {
            $type = 'news';
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');
        $result = $model->paginate($type, $page, 6, $search ?: null);

        $this->view('pages/media', [
            'metaTitle' => 'Media Desk',
            'type' => $type,
            'items' => $result['data'],
            'total' => $result['total'],
            'page' => $page,
            'perPage' => 6,
            'search' => $search,
        ]);
    }

    public function gallery(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_media')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $category = trim($_GET['category'] ?? '');
        $all = $model->all('gallery');
        $items = $category ? array_values(array_filter($all, fn($row) => $row['category'] === $category)) : $all;

        $this->view('pages/gallery', [
            'metaTitle' => 'Gallery',
            'items' => $items,
            'category' => $category,
        ]);
    }
}
