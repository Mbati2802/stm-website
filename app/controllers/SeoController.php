<?php
class SeoController extends Controller
{
    public function sitemap(): void
    {
        $model = new ContentModel($this->config);
        $urls = [
            base_url(''),
            base_url('about'),
            base_url('programmes'),
            base_url('events'),
            base_url('library'),
            base_url('media'),
            base_url('gallery'),
            base_url('contact'),
            base_url('faqs'),
        ];

        foreach ($model->getProgrammes() as $programme) {
            if (!empty($programme['slug'])) {
                $urls[] = base_url('programmes/' . $programme['slug']);
            }
        }
        foreach (['news', 'careers', 'tenders'] as $type) {
            $items = $model->paginate($type, 1, 500);
            foreach ($items['data'] as $item) {
                if (!empty($item['slug'])) {
                    $urls[] = base_url('media/' . $type . '/' . $item['slug']);
                }
            }
        }
        foreach ($model->all('events') as $event) {
            if (!empty($event['slug'])) {
                $urls[] = base_url('events/' . $event['slug']);
            }
        }

        $urls = array_values(array_unique($urls));
        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            echo '<url><loc>' . e($url) . '</loc></url>';
        }
        echo '</urlset>';
        exit;
    }
}
