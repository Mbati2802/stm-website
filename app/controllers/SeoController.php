<?php
class SeoController extends Controller
{
    public function sitemap(): void
    {
        $model = new ContentModel($this->config);
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
        $baseUrl = $scheme . '://' . $host;

        $urls = [];

        // Static public pages with priorities
        $staticPages = [
            ''                        => ['priority' => '1.0', 'changefreq' => 'daily'],
            'about'                   => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'uniqueness'              => ['priority' => '0.6', 'changefreq' => 'monthly'],
            'principal'               => ['priority' => '0.6', 'changefreq' => 'monthly'],
            'registrar'               => ['priority' => '0.6', 'changefreq' => 'monthly'],
            'contact'                 => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'contact-admissions'      => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'contact-registrar'       => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'faqs'                    => ['priority' => '0.6', 'changefreq' => 'monthly'],
            'portals'                 => ['priority' => '0.5', 'changefreq' => 'monthly'],
            'programmes'              => ['priority' => '0.9', 'changefreq' => 'weekly'],
            'programmes/how-to-apply' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'departments'             => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'library'                 => ['priority' => '0.5', 'changefreq' => 'monthly'],
            'media'                   => ['priority' => '0.6', 'changefreq' => 'weekly'],
            'gallery'                 => ['priority' => '0.5', 'changefreq' => 'weekly'],
            'testimonials'            => ['priority' => '0.5', 'changefreq' => 'monthly'],
            'events'                  => ['priority' => '0.7', 'changefreq' => 'weekly'],
        ];

        foreach ($staticPages as $path => $meta) {
            $urls[] = [
                'loc'        => $baseUrl . '/' . ltrim($path, '/'),
                'priority'   => $meta['priority'],
                'changefreq' => $meta['changefreq'],
                'lastmod'    => date('Y-m-d'),
            ];
        }

        // Dynamic programme pages
        try {
            foreach ($model->getProgrammes() as $programme) {
                if (!empty($programme['slug'])) {
                    $urls[] = [
                        'loc'        => $baseUrl . '/programmes/' . $programme['slug'],
                        'priority'   => '0.8',
                        'changefreq' => 'monthly',
                        'lastmod'    => !empty($programme['updated_at']) ? substr((string)$programme['updated_at'], 0, 10) : date('Y-m-d'),
                    ];
                }
            }
        } catch (Throwable) {}

        // Dynamic media/news/careers/tenders pages
        try {
            foreach (['news', 'careers', 'tenders'] as $type) {
                $items = $model->paginate($type, 1, 500);
                foreach ($items['data'] as $item) {
                    if (!empty($item['slug'])) {
                        $urls[] = [
                            'loc'        => $baseUrl . '/media/' . $type . '/' . $item['slug'],
                            'priority'   => '0.6',
                            'changefreq' => 'monthly',
                            'lastmod'    => !empty($item['updated_at']) ? substr((string)$item['updated_at'], 0, 10) : date('Y-m-d'),
                        ];
                    }
                }
            }
        } catch (Throwable) {}

        // Dynamic event pages
        try {
            foreach ($model->all('events') as $event) {
                if (!empty($event['slug'])) {
                    $urls[] = [
                        'loc'        => $baseUrl . '/events/' . $event['slug'],
                        'priority'   => '0.7',
                        'changefreq' => 'weekly',
                        'lastmod'    => !empty($event['updated_at']) ? substr((string)$event['updated_at'], 0, 10) : date('Y-m-d'),
                    ];
                }
            }
        } catch (Throwable) {}

        // De-duplicate by URL
        $seen = [];
        $urls = array_values(array_filter($urls, function ($u) use (&$seen) {
            $key = $u['loc'];
            if (isset($seen[$key])) return false;
            $seen[$key] = true;
            return true;
        }));

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo '  <url>' . "\n";
            echo '    <loc>' . e($u['loc']) . '</loc>' . "\n";
            echo '    <lastmod>' . e($u['lastmod']) . '</lastmod>' . "\n";
            echo '    <changefreq>' . e($u['changefreq']) . '</changefreq>' . "\n";
            echo '    <priority>' . e($u['priority']) . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }
        echo '</urlset>';
        exit;
    }
}
