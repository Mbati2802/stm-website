<?php
/**
 * CLI cron script to fetch social media updates.
 *
 * Usage (cPanel cron):
 *   /usr/local/bin/php /home/USER/public_html/cron/fetch-social.php
 *
 * Or schedule a curl hit on cron/social-fetch?token=... (see admin settings).
 */
require_once __DIR__ . '/../bootstrap.php';

$config = require __DIR__ . '/../config/config.php';
$model = new ContentModel($config);
$settings = $model->getSettings();

if (($settings['social_auto_fetch_enabled'] ?? '1') !== '1') {
    fwrite(STDOUT, "Auto-fetch disabled. Skipping.\n");
    exit(0);
}

$fetcher = new SocialFetcher($config);
$result = $fetcher->syncAll(12);

fwrite(STDOUT, sprintf(
    "[%s] Fetched: FB=%d, IG=%d, total=%d. Errors: %s\n",
    date('Y-m-d H:i:s'),
    (int)$result['stats']['facebook'],
    (int)$result['stats']['instagram'],
    (int)$result['total'],
    $result['errors'] === [] ? 'none' : implode(' | ', $result['errors'])
));

exit($result['success'] ? 0 : 1);
