<?php
/**
 * SocialFetcher - pulls live posts from Facebook Pages and Instagram Business
 * accounts via the Meta Graph API and upserts them into the social_updates
 * table. Safe to call repeatedly (idempotent) thanks to the
 * (external_source, external_id) unique index.
 */
class SocialFetcher
{
    private const GRAPH_VERSION = 'v19.0';
    private const GRAPH_BASE = 'https://graph.facebook.com/';
    private array $config;
    private PDO $pdo;
    private array $errors = [];
    private array $stats = ['facebook' => 0, 'instagram' => 0];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = Database::getInstance($config['db']);
    }

    private bool $dryRun = false;
    private array $previewPosts = [];

    public function syncAll(int $limitPerSource = 12): array
    {
        $settings = (new ContentModel($this->config))->getSettings();
        $token = trim((string)($settings['facebook_page_access_token'] ?? ''));
        $pageId = trim((string)($settings['facebook_page_id'] ?? ''));
        $igUserId = trim((string)($settings['instagram_business_account_id'] ?? ''));

        if ($token === '') {
            $this->errors[] = 'Facebook page access token is not configured.';
            return $this->result();
        }

        if ($pageId !== '') {
            try {
                $this->stats['facebook'] = $this->fetchFacebookPagePosts($pageId, $token, $limitPerSource);
            } catch (\Throwable $e) {
                $this->errors[] = 'Facebook fetch failed: ' . $e->getMessage();
            }
        }

        if ($igUserId !== '' && $igUserId !== $pageId) {
            try {
                $this->stats['instagram'] = $this->fetchInstagramMedia($igUserId, $token, $limitPerSource);
            } catch (\Throwable $e) {
                $this->errors[] = 'Instagram fetch failed: ' . $e->getMessage();
            }
        } elseif ($igUserId !== '' && $igUserId === $pageId) {
            $this->errors[] = 'Instagram Business Account ID is the same as Facebook Page ID — skipped. Use the Graph API to look up the correct IG ID.';
        }

        if (!$this->dryRun) {
            $this->updateLastRun();
        }
        return $this->result();
    }

    /**
     * Preview mode: fetch posts from API but do NOT save to database.
     * Returns full diagnostics + post previews.
     */
    public function preview(int $limitPerSource = 5): array
    {
        $this->dryRun = true;
        $this->previewPosts = [];
        $settings = (new ContentModel($this->config))->getSettings();

        $diag = [
            'config' => [
                'page_id' => trim((string)($settings['facebook_page_id'] ?? '')),
                'ig_user_id' => trim((string)($settings['instagram_business_account_id'] ?? '')),
                'token_set' => trim((string)($settings['facebook_page_access_token'] ?? '')) !== '',
                'token_prefix' => mb_substr(trim((string)($settings['facebook_page_access_token'] ?? '')), 0, 12) . '...',
                'auto_fetch_enabled' => ($settings['social_auto_fetch_enabled'] ?? '1') === '1',
                'last_run' => (string)($settings['social_auto_fetch_last_run'] ?? 'never'),
                'graph_version' => self::GRAPH_VERSION,
            ],
            'server' => [
                'curl_version' => function_exists('curl_version') ? curl_version()['version'] ?? 'unknown' : 'not installed',
                'ssl_version' => function_exists('curl_version') ? curl_version()['ssl_version'] ?? 'unknown' : 'n/a',
                'php_version' => PHP_VERSION,
            ],
        ];

        $result = $this->syncAll($limitPerSource);
        $diag['result'] = $result;
        $diag['preview_posts'] = $this->previewPosts;

        $this->dryRun = false;
        return $diag;
    }

    private function fetchFacebookPagePosts(string $pageId, string $token, int $limit): int
    {
        $fields = 'id,message,full_picture,picture,permalink_url,created_time,attachments{media_type,media,url,subattachments{media_type,media,url}}';
        $url = self::GRAPH_BASE . self::GRAPH_VERSION . '/' . rawurlencode($pageId) . '/posts'
            . '?fields=' . rawurlencode($fields)
            . '&limit=' . (int)$limit
            . '&access_token=' . rawurlencode($token);
        $data = $this->httpGetJson($url);
        $posts = is_array($data['data'] ?? null) ? $data['data'] : [];
        $count = 0;
        foreach ($posts as $post) {
            $message = trim((string)($post['message'] ?? ''));
            if ($message === '') {
                // Skip posts with no text (e.g., shared photos with no caption)
                continue;
            }
            $externalId = (string)($post['id'] ?? '');
            if ($externalId === '') {
                continue;
            }
            // Try full_picture first, then picture, then attachments
            $image = (string)($post['full_picture'] ?? '');
            if ($image === '') {
                $image = (string)($post['picture'] ?? '');
            }
            if ($image === '' && isset($post['attachments']['data'][0])) {
                $att = $post['attachments']['data'][0];
                if (!empty($att['media']['image']['src'])) {
                    $image = (string)$att['media']['image']['src'];
                } elseif (!empty($att['url']) && strpos($att['media_type'] ?? '', 'photo') !== false) {
                    $image = (string)$att['url'];
                }
            }
            $link = (string)($post['permalink_url'] ?? '');
            $postedAt = isset($post['created_time']) ? date('Y-m-d H:i:s', strtotime((string)$post['created_time'])) : null;
            $this->upsert($externalId, 'facebook', $message, $image, $link, $postedAt);
            $count++;
        }
        return $count;
    }

    private function fetchInstagramMedia(string $igUserId, string $token, int $limit): int
    {
        $fields = 'id,caption,media_type,media_url,permalink,timestamp,thumbnail_url';
        $url = self::GRAPH_BASE . self::GRAPH_VERSION . '/' . rawurlencode($igUserId) . '/media'
            . '?fields=' . rawurlencode($fields)
            . '&limit=' . (int)$limit
            . '&access_token=' . rawurlencode($token);
        $data = $this->httpGetJson($url);
        $items = is_array($data['data'] ?? null) ? $data['data'] : [];
        $count = 0;
        foreach ($items as $item) {
            $caption = trim((string)($item['caption'] ?? ''));
            $externalId = (string)($item['id'] ?? '');
            if ($externalId === '') {
                continue;
            }
            $mediaType = (string)($item['media_type'] ?? '');
            $image = '';
            if ($mediaType === 'IMAGE' || $mediaType === 'CAROUSEL_ALBUM') {
                $image = (string)($item['media_url'] ?? '');
            } elseif ($mediaType === 'VIDEO') {
                $image = (string)($item['thumbnail_url'] ?? $item['media_url'] ?? '');
            }
            $link = (string)($item['permalink'] ?? '');
            $postedAt = isset($item['timestamp']) ? date('Y-m-d H:i:s', strtotime((string)$item['timestamp'])) : null;
            // For pure-image posts with no caption, still show with a placeholder note
            $content = $caption !== '' ? $caption : '(Photo posted on Instagram)';
            $this->upsert($externalId, 'instagram', $content, $image, $link, $postedAt);
            $count++;
        }
        return $count;
    }

    private function upsert(string $externalId, string $source, string $content, string $image, string $link, ?string $postedAt): void
    {
        if ($this->dryRun) {
            $this->previewPosts[] = [
                'external_id' => $externalId,
                'source' => $source,
                'content' => mb_substr($content, 0, 200),
                'image' => $image,
                'link' => $link,
                'posted_at' => $postedAt,
            ];
            return;
        }
        $sql = 'INSERT INTO social_updates
                    (content, image_path, link_url, source, is_pinned, is_visible,
                     external_id, external_source, auto_fetched, posted_at, fetched_at, created_at)
                VALUES
                    (:content, :image_path, :link_url, :source, 0, 1,
                     :external_id, :external_source, 1, :posted_at, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    content = VALUES(content),
                    image_path = VALUES(image_path),
                    link_url = VALUES(link_url),
                    posted_at = VALUES(posted_at),
                    fetched_at = NOW()';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'content' => $content,
            'image_path' => $image !== '' ? $image : null,
            'link_url' => $link !== '' ? $link : null,
            'source' => $source,
            'external_id' => $externalId,
            'external_source' => $source,
            'posted_at' => $postedAt,
        ]);
    }

    private function updateLastRun(): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO settings(setting_key, setting_value) VALUES(:k,:v) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        $stmt->execute(['k' => 'social_auto_fetch_last_run', 'v' => date('Y-m-d H:i:s')]);
    }

    private function httpGetJson(string $url): array
    {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'STM-Website/1.0',
        ];
        // cPanel shared hosting: try common CA bundle paths
        foreach (['/etc/ssl/certs/ca-certificates.crt', '/etc/pki/tls/certs/ca-bundle.crt', '/etc/ssl/cert.pem'] as $ca) {
            if (file_exists($ca)) {
                $opts[CURLOPT_CAINFO] = $ca;
                break;
            }
        }
        curl_setopt_array($ch, $opts);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        $errno = curl_errno($ch);

        // SSL handshake failure fallback: retry without peer verification
        if ($body === false && in_array($errno, [CURLE_SSL_CONNECT_ERROR, CURLE_SSL_CERTPROBLEM, CURLE_PEER_FAILED_VERIFICATION, 60, 77], true)) {
            $this->errors[] = 'SSL verify failed, retrying without verify: ' . $err;
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $body = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
        }

        curl_close($ch);
        if ($body === false) {
            throw new RuntimeException('cURL error (' . $errno . '): ' . $err);
        }
        $json = json_decode((string)$body, true);
        if (!is_array($json)) {
            throw new RuntimeException('Invalid JSON response (HTTP ' . $code . '): ' . mb_substr((string)$body, 0, 200));
        }
        if (isset($json['error'])) {
            $msg = (string)($json['error']['message'] ?? 'Graph API error');
            $type = (string)($json['error']['type'] ?? '');
            throw new RuntimeException($msg . ' [' . $type . '] (code ' . (int)($json['error']['code'] ?? 0) . ')');
        }
        if ($code >= 400) {
            throw new RuntimeException('HTTP ' . $code . ': ' . mb_substr((string)$body, 0, 200));
        }
        return $json;
    }

    private function result(): array
    {
        return [
            'stats' => $this->stats,
            'errors' => $this->errors,
            'success' => $this->errors === [],
            'total' => array_sum($this->stats),
        ];
    }
}
