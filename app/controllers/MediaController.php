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
            'metaDescription' => 'Latest news, careers, tenders, and institutional updates from St. Mary\'s MCHM College.',
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
            'metaDescription' => 'Photo gallery of student life, training, events, and milestones at St. Mary\'s MCHM College.',
            'items' => $items,
            'category' => $category,
        ]);
    }

    public function show(string $type, string $slug): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_media')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        if (!in_array($type, ['news', 'careers', 'tenders'], true)) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }
        $item = $model->getBySlug($type, $slug);
        if ($item === null) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }
        $summary = trim((string)($item['summary'] ?? ''));
        $this->view('pages/media_detail', [
            'metaTitle' => (string)($item['title'] ?? ucfirst($type)),
            'metaDescription' => $summary !== '' ? plain_text($summary) : ('Read the latest ' . $type . ' update from St. Mary\'s MCHM College.'),
            'type' => $type,
            'item' => $item,
        ]);
    }
}
