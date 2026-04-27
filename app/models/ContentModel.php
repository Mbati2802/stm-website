<?php
class ContentModel
{
    private PDO $pdo;
    private const PROGRAMME_TEMPLATE_KEY_PREFIX = 'programme_template_';
    private const PROGRAMME_OVERRIDE_KEY_PREFIX = 'programme_override_';

    public function __construct(array $config)
    {
        $this->pdo = Database::getInstance($config['db']);
        $this->ensureAnalyticsStorage();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    public function getSettings(): array
    {
        $rows = $this->pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    public function getFeaturedProgrammes(int $limit = 6): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM programmes ORDER BY created_at DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->filterHidden('programmes', $stmt->fetchAll());
    }

    public function getTrendingProgrammes(int $limit = 6): array
    {
        $all = $this->getProgrammes();
        if ($all === []) {
            return [];
        }

        $metrics = $this->programmeMetricsMap();
        $scored = [];
        foreach ($all as $programme) {
            $slug = (string)($programme['slug'] ?? '');
            $views = (int)($metrics[$slug]['views'] ?? 0);
            $applications = (int)($metrics[$slug]['applications'] ?? 0);
            $baseScore = ($applications * 5) + $views;
            // Keep top performers visible while rotating similar-score cards.
            $dynamicScore = $baseScore + (mt_rand(0, 25) / 100);
            $programme['_trend_score'] = $dynamicScore;
            $scored[] = $programme;
        }

        usort($scored, static function (array $a, array $b): int {
            return ($b['_trend_score'] <=> $a['_trend_score']);
        });
        $scored = array_slice($scored, 0, max(1, $limit));
        return array_map(static function (array $row): array {
            unset($row['_trend_score']);
            return $row;
        }, $scored);
    }

    public function incrementProgrammeMetric(string $programmeSlug, string $metric): void
    {
        $slug = trim($programmeSlug);
        $metricName = strtolower(trim($metric));
        if ($slug === '' || !in_array($metricName, ['views', 'applications'], true)) {
            return;
        }
        $safeSlug = preg_replace('/[^a-z0-9\-_]/i', '', $slug);
        if ($safeSlug === null || $safeSlug === '') {
            return;
        }

        $key = 'programme_metric_' . $safeSlug . '_' . $metricName;
        $current = (int)($this->getSettingValue($key) ?? '0');
        $this->setSettingValue($key, (string)($current + 1));
    }

    public function getProgrammes(?string $type = null, ?string $search = null): array
    {
        $sql = 'SELECT p.*, d.name department_name FROM programmes p LEFT JOIN departments d ON d.id = p.department_id WHERE 1=1';
        $params = [];

        if ($type) {
            $sql .= ' AND p.category = :category';
            $params['category'] = $type;
        }

        if ($search) {
            $sql .= ' AND p.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.name ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->filterHidden('programmes', $stmt->fetchAll());
    }

    public function getProgrammeBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT p.*, d.name department_name FROM programmes p LEFT JOIN departments d ON d.id = p.department_id WHERE p.slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch() ?: null;
        if ($row === null) {
            return null;
        }
        $filtered = $this->filterHidden('programmes', [$row]);
        return $filtered[0] ?? null;
    }

    public function getSettingValue(string $key): ?string
    {
        $stmt = $this->pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1');
        $stmt->execute(['k' => $key]);
        $value = $stmt->fetch()['setting_value'] ?? null;
        return $value === null ? null : (string)$value;
    }

    public function setSettingValue(string $key, string $value): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO settings(setting_key, setting_value) VALUES(:k,:v) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        $stmt->execute(['k' => $key, 'v' => trim($value)]);
    }

    public function deleteSetting(string $key): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM settings WHERE setting_key = :k');
        $stmt->execute(['k' => $key]);
    }

    public function getProgrammeContentForView(array $programme): array
    {
        $template = $this->getDecodedProgrammeTemplate($programme['name'] ?? '');
        $override = $this->getDecodedProgrammeOverride($programme['slug'] ?? '');
        $merged = array_merge($template, $override);

        return [
            'overview' => trim((string)($merged['overview'] ?? '')),
            'objectives' => $this->decodeLines($merged['objectives'] ?? ''),
            'content_areas' => $this->decodeLines($merged['content_areas'] ?? ''),
            'career_opportunities' => $this->decodeLines($merged['career_opportunities'] ?? ''),
            'why_study' => $this->decodeLines($merged['why_study'] ?? ''),
            'duration_override' => trim((string)($merged['duration_override'] ?? '')),
            'entry_requirement_override' => trim((string)($merged['entry_requirement_override'] ?? '')),
        ];
    }

    public function getProgrammeContentForEditor(array $programme): array
    {
        $template = $this->getDecodedProgrammeTemplate($programme['name'] ?? '');
        $override = $this->getDecodedProgrammeOverride($programme['slug'] ?? '');
        $hasOverride = $override !== [];
        $source = $hasOverride ? $override : $template;

        $defaultOverview = ($programme['name'] ?? 'This programme') . ' is designed to equip students with practical, career-focused skills and real-world competencies.';
        $defaultObjectives = implode("\n", [
            'Understand core concepts and applied practice in this field',
            'Develop communication and professional skills',
            'Apply ethical and practical standards in real settings',
        ]);
        $defaultContent = implode("\n", [
            'Introduction and fundamentals',
            'Applied practical skills',
            'Professional standards and ethics',
        ]);
        $defaultCareers = implode("\n", [
            'Hospitals and health centers',
            'Schools and training institutions',
            'NGOs and community organizations',
        ]);
        $defaultWhy = implode("\n", [
            'High market demand',
            'Hands-on practical learning',
            'Pathway to further professional growth',
        ]);

        return [
            'family_name' => $this->extractProgrammeFamilyName((string)($programme['name'] ?? '')),
            'content_scope' => $hasOverride ? 'level' : 'shared',
            'overview' => trim((string)($source['overview'] ?? $defaultOverview)),
            'objectives' => $this->encodeLines($source['objectives'] ?? $defaultObjectives),
            'content_areas' => $this->encodeLines($source['content_areas'] ?? $defaultContent),
            'career_opportunities' => $this->encodeLines($source['career_opportunities'] ?? $defaultCareers),
            'why_study' => $this->encodeLines($source['why_study'] ?? $defaultWhy),
            'duration_override' => trim((string)($source['duration_override'] ?? '')),
            'entry_requirement_override' => trim((string)($source['entry_requirement_override'] ?? '')),
        ];
    }

    public function saveProgrammeContentFromEditor(string $programmeName, string $programmeSlug, array $data): void
    {
        $content = [
            'overview' => trim((string)($data['overview'] ?? '')),
            'objectives' => $this->encodeLines($data['objectives'] ?? ''),
            'content_areas' => $this->encodeLines($data['content_areas'] ?? ''),
            'career_opportunities' => $this->encodeLines($data['career_opportunities'] ?? ''),
            'why_study' => $this->encodeLines($data['why_study'] ?? ''),
            'duration_override' => trim((string)($data['duration_override'] ?? '')),
            'entry_requirement_override' => trim((string)($data['entry_requirement_override'] ?? '')),
        ];

        $scope = ($data['content_scope'] ?? 'shared') === 'level' ? 'level' : 'shared';
        $templateKey = $this->programmeTemplateSettingKey($programmeName);
        $overrideKey = $this->programmeOverrideSettingKey($programmeSlug);

        if ($scope === 'shared') {
            $this->setSettingValue($templateKey, json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->deleteSetting($overrideKey);
            return;
        }

        $this->setSettingValue($overrideKey, json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function latest(string $table, int $limit = 3): array
    {
        $allowed = ['news', 'careers', 'tenders'];
        if (!in_array($table, $allowed, true)) {
            return [];
        }
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->filterHidden($table, $stmt->fetchAll());
    }

    public function getUpcomingEvents(int $limit = 3): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM events WHERE starts_at >= NOW() ORDER BY starts_at ASC LIMIT :lim');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $this->filterHidden('events', $stmt->fetchAll());
        } catch (PDOException) {
            return [];
        }
    }

    public function getFeaturedEvent(): ?array
    {
        try {
            // Prefer explicit featured flag if present, otherwise pick the next upcoming event.
            try {
                $stmt = $this->pdo->query('SELECT * FROM events WHERE starts_at >= NOW() AND is_featured = 1 ORDER BY starts_at ASC LIMIT 1');
                $row = $stmt->fetch() ?: null;
            } catch (PDOException) {
                $row = null;
            }

            if (!$row) {
                $stmt = $this->pdo->query('SELECT * FROM events WHERE starts_at >= NOW() ORDER BY starts_at ASC LIMIT 1');
                $row = $stmt->fetch() ?: null;
            }

            if (!$row) return null;
            $filtered = $this->filterHidden('events', [$row]);
            return $filtered[0] ?? null;
        } catch (PDOException) {
            return null;
        }
    }

    public function getPastEvents(int $limit = 12): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM events WHERE starts_at < NOW() ORDER BY starts_at DESC LIMIT :lim');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $this->filterHidden('events', $stmt->fetchAll());
        } catch (PDOException) {
            return [];
        }
    }

    public function getEventBySlug(string $slug): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM events WHERE slug = :slug LIMIT 1');
            $stmt->execute(['slug' => $slug]);
            $row = $stmt->fetch() ?: null;
            if ($row === null) {
                return null;
            }
            $filtered = $this->filterHidden('events', [$row]);
            return $filtered[0] ?? null;
        } catch (PDOException) {
            return null;
        }
    }

    public function getBySlug(string $table, string $slug): ?array
    {
        $allowed = ['news', 'careers', 'tenders'];
        if (!in_array($table, $allowed, true)) {
            return null;
        }
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE slug = :slug LIMIT 1");
            $stmt->execute(['slug' => trim($slug)]);
            $row = $stmt->fetch() ?: null;
            if ($row === null) {
                return null;
            }
            $filtered = $this->filterHidden($table, [$row]);
            return $filtered[0] ?? null;
        } catch (PDOException) {
            return null;
        }
    }

    public function paginate(string $table, int $page = 1, int $perPage = 6, ?string $search = null): array
    {
        $allowed = ['news', 'careers', 'tenders', 'library_resources', 'gallery'];
        if (!in_array($table, $allowed, true)) {
            return ['data' => [], 'total' => 0];
        }

        $offset = ($page - 1) * $perPage;
        $where = '';
        $params = [];

        if ($search && $table !== 'gallery') {
            $where = ' WHERE title LIKE :search OR summary LIKE :search ';
            $params['search'] = '%' . $search . '%';
        }

        if ($search && $table === 'gallery') {
            $where = ' WHERE title LIKE :search OR category LIKE :search ';
            $params['search'] = '%' . $search . '%';
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) total FROM {$table} {$where}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch()['total'];

        $stmt = $this->pdo->prepare("SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT :lim OFFSET :off");
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $this->filterHidden($table, $stmt->fetchAll());
        return ['data' => $rows, 'total' => $total];
    }

    public function faqs(): array
    {
        return $this->filterHidden('faqs', $this->pdo->query('SELECT * FROM faqs ORDER BY id DESC')->fetchAll());
    }

    public function page(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function saveMessage(array $data): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO messages(name, email, phone, subject, message, created_at) VALUES(:name, :email, :phone, :subject, :message, NOW())');
        return $stmt->execute($data);
    }

    public function getUnreadPublicMessagesCount(): int
    {
        try {
            return (int)($this->pdo->query('SELECT COUNT(*) AS total FROM messages WHERE read_at IS NULL')->fetch()['total'] ?? 0);
        } catch (PDOException) {
            try {
                return (int)($this->pdo->query('SELECT COUNT(*) AS total FROM messages')->fetch()['total'] ?? 0);
            } catch (PDOException) {
                return 0;
            }
        }
    }

    public function markPublicMessageRead(int $id): void
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE messages SET read_at = NOW() WHERE id = :id AND read_at IS NULL');
            $stmt->execute(['id' => $id]);
        } catch (PDOException) {
            // no-op
        }
    }

    public function saveProgrammeApplication(array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO programme_applications(
                    name, email, phone, guardian_name, guardian_phone, county, course_selection,
                    grade, level, preferred_intake, referral_source, created_at
                ) VALUES(
                    :name, :email, :phone, :guardian_name, :guardian_phone, :county, :course_selection,
                    :grade, :level, :preferred_intake, :referral_source, NOW()
                )
            ');
            return $stmt->execute($data);
        } catch (PDOException) {
            $today = date('Ymd');
            $total = (int)($this->getSettingValue('applications_total') ?? '0');
            $daily = (int)($this->getSettingValue('applications_day_' . $today) ?? '0');
            $this->setSettingValue('applications_total', (string)($total + 1));
            $this->setSettingValue('applications_day_' . $today, (string)($daily + 1));
            return false;
        }
    }

    public function getProgrammeApplications(int $limit = 100): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM programme_applications ORDER BY id DESC LIMIT :lim');
            $stmt->bindValue(':lim', max(1, min($limit, 500)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            try {
                $stmt = $this->pdo->prepare("
                    SELECT
                        id,
                        name,
                        email,
                        phone,
                        '' AS guardian_name,
                        '' AS guardian_phone,
                        '' AS county,
                        'Programme Application' AS course_selection,
                        '' AS grade,
                        '' AS level,
                        '' AS preferred_intake,
                        '' AS referral_source,
                        created_at
                    FROM messages
                    WHERE subject = 'Programme Application'
                    ORDER BY id DESC
                    LIMIT :lim
                ");
                $stmt->bindValue(':lim', max(1, min($limit, 500)), PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (PDOException) {
                return [];
            }
        }
    }

    public function getDailyTrend(string $table, int $days = 14): array
    {
        $allowed = ['programme_applications', 'page_visits'];
        if (!in_array($table, $allowed, true)) {
            return [];
        }
        try {
            $stmt = $this->pdo->prepare("
                SELECT DATE(created_at) AS day, COUNT(*) AS total
                FROM {$table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            $stmt->bindValue(':days', max(1, min(90, $days)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            if ($table === 'programme_applications') {
                try {
                    $stmt = $this->pdo->prepare("
                        SELECT DATE(created_at) AS day, COUNT(*) AS total
                        FROM messages
                        WHERE subject = 'Programme Application'
                          AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                        GROUP BY DATE(created_at)
                        ORDER BY DATE(created_at) ASC
                    ");
                    $stmt->bindValue(':days', max(1, min(90, $days)), PDO::PARAM_INT);
                    $stmt->execute();
                    return $stmt->fetchAll();
                } catch (PDOException) {
                    return $this->getDailyTrendFromSettings('applications_day_', $days);
                }
            }
            if ($table === 'page_visits') {
                return $this->getDailyTrendFromSettings('page_visit_day_', $days);
            }
            return [];
        }
    }

    public function logPageVisit(string $path, bool $isAdmin = false): void
    {
        $cleanPath = trim($path);
        if ($cleanPath === '') {
            $cleanPath = '/';
        }
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO page_visits(path, user_role, is_admin, session_id, ip_address, user_agent, created_at)
                VALUES(:path, :user_role, :is_admin, :session_id, :ip_address, :user_agent, NOW())
            ');
            $stmt->execute([
                'path' => substr($cleanPath, 0, 255),
                'user_role' => substr((string)(Auth::role() ?? ''), 0, 40),
                'is_admin' => $isAdmin ? 1 : 0,
                'session_id' => substr((string)session_id(), 0, 128),
                'ip_address' => substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 64),
                'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);
        } catch (Throwable) {
            // no-op
        }
        $today = date('Ymd');
        $dailyKey = 'page_visit_day_' . $today;
        $total = (int)($this->getSettingValue('page_visit_total') ?? '0');
        $daily = (int)($this->getSettingValue($dailyKey) ?? '0');
        $pathKey = 'page_visit_path_' . slugify($cleanPath);
        $pathCount = (int)($this->getSettingValue($pathKey) ?? '0');
        $this->setSettingValue('page_visit_total', (string)($total + 1));
        $this->setSettingValue($dailyKey, (string)($daily + 1));
        $this->setSettingValue($pathKey, (string)($pathCount + 1));
    }

    public function getTopVisitedPages(int $limit = 10, bool $adminOnly = false): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT path, COUNT(*) AS visits
                FROM page_visits
                WHERE is_admin = :is_admin
                GROUP BY path
                ORDER BY visits DESC
                LIMIT :lim
            ');
            $stmt->bindValue(':is_admin', $adminOnly ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':lim', max(1, min($limit, 50)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            try {
                $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'page_visit_path_%'");
                $rows = $stmt->fetchAll();
                $mapped = [];
                foreach ($rows as $row) {
                    $key = (string)($row['setting_key'] ?? '');
                    $count = (int)($row['setting_value'] ?? 0);
                    $slug = str_replace('page_visit_path_', '', $key);
                    if ($slug === '') {
                        continue;
                    }
                    $mapped[] = ['path' => '/' . str_replace('-', '/', $slug), 'visits' => $count];
                }
                usort($mapped, static fn(array $a, array $b) => ((int)$b['visits'] <=> (int)$a['visits']));
                return array_slice($mapped, 0, max(1, min($limit, 50)));
            } catch (PDOException) {
                return [];
            }
        }
    }

    public function getTopCourseViews(int $limit = 8): array
    {
        $metrics = $this->programmeMetricsMap();
        $rows = [];
        foreach ($metrics as $slug => $vals) {
            $rows[] = [
                'slug' => (string)$slug,
                'views' => (int)($vals['views'] ?? 0),
                'applications' => (int)($vals['applications'] ?? 0),
            ];
        }
        usort($rows, static fn(array $a, array $b) => ($b['views'] <=> $a['views']));
        return array_slice($rows, 0, max(1, min($limit, 30)));
    }

    public function countAll(string $table): int
    {
        $allowed = [
            'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
            'library_resources', 'faqs', 'messages', 'pages', 'users',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments',
            'study_materials', 'grading_schemes', 'event_registrations', 'programme_applications', 'page_visits',
            'testimonials', 'social_updates'
        ];
        if (!in_array($table, $allowed, true)) {
            return 0;
        }
        try {
            return (int)($this->pdo->query("SELECT COUNT(*) AS total FROM {$table}")->fetch()['total'] ?? 0);
        } catch (PDOException) {
            if ($table === 'programme_applications') {
                try {
                    return (int)($this->pdo->query("SELECT COUNT(*) AS total FROM messages WHERE subject = 'Programme Application'")->fetch()['total'] ?? 0);
                } catch (PDOException) {
                    return 0;
                }
            }
            return 0;
        }
    }

    private function ensureAnalyticsStorage(): void
    {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS programme_applications (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(190) NOT NULL,
                email VARCHAR(190) NOT NULL,
                phone VARCHAR(60) NOT NULL,
                guardian_name VARCHAR(190) NULL,
                guardian_phone VARCHAR(60) NULL,
                county VARCHAR(120) NULL,
                course_selection VARCHAR(190) NOT NULL,
                grade VARCHAR(80) NULL,
                level VARCHAR(80) NULL,
                preferred_intake VARCHAR(80) NULL,
                referral_source VARCHAR(190) NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
            )");
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS page_visits (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                path VARCHAR(255) NOT NULL,
                user_role VARCHAR(40) NULL,
                is_admin TINYINT(1) NOT NULL DEFAULT 0,
                session_id VARCHAR(128) NULL,
                ip_address VARCHAR(64) NULL,
                user_agent VARCHAR(255) NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
            )");
        } catch (Throwable) {
            // no-op
        }
    }

    private function getDailyTrendFromSettings(string $prefix, int $days): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE :prefix");
            $stmt->execute(['prefix' => $prefix . '%']);
            $rows = $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
        $map = [];
        foreach ($rows as $row) {
            $key = (string)($row['setting_key'] ?? '');
            $value = (int)($row['setting_value'] ?? 0);
            $dateRaw = str_replace($prefix, '', $key);
            if (!preg_match('/^\d{8}$/', $dateRaw)) {
                continue;
            }
            $day = substr($dateRaw, 0, 4) . '-' . substr($dateRaw, 4, 2) . '-' . substr($dateRaw, 6, 2);
            $map[$day] = $value;
        }
        $out = [];
        $days = max(1, min(90, $days));
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime('-' . $i . ' day'));
            $out[] = ['day' => $day, 'total' => (int)($map[$day] ?? 0)];
        }
        return $out;
    }

    public function getAdminUsers(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name, email, role, status FROM users WHERE role IN ('super_admin','junior_admin','teacher') AND status='active' ORDER BY name ASC");
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function sendAdminMessage(int $senderId, int $recipientId, string $subject, string $body): bool
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO admin_messages(sender_id, recipient_id, subject, body, created_at)
                VALUES(:sender_id, :recipient_id, :subject, :body, NOW())
            ');
            return $stmt->execute([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'subject' => trim($subject),
                'body' => trim($body),
            ]);
        } catch (PDOException) {
            return false;
        }
    }

    public function getAdminInbox(int $adminId, int $limit = 50): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT am.*, u.name AS sender_name, u.role AS sender_role
                FROM admin_messages am
                INNER JOIN users u ON u.id = am.sender_id
                WHERE am.recipient_id = :admin_id
                ORDER BY am.id DESC
                LIMIT :lim
            ');
            $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', max(1, min(200, $limit)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getAdminSentMessages(int $adminId, int $limit = 50): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT am.*, u.name AS recipient_name, u.role AS recipient_role
                FROM admin_messages am
                INNER JOIN users u ON u.id = am.recipient_id
                WHERE am.sender_id = :admin_id
                ORDER BY am.id DESC
                LIMIT :lim
            ');
            $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', max(1, min(200, $limit)), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function getUnreadAdminMessageCount(int $adminId): int
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) AS total FROM admin_messages WHERE recipient_id = :admin_id AND read_at IS NULL');
            $stmt->execute(['admin_id' => $adminId]);
            return (int)($stmt->fetch()['total'] ?? 0);
        } catch (PDOException) {
            return 0;
        }
    }

    public function markAdminInboxAsRead(int $adminId): void
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE admin_messages SET read_at = NOW() WHERE recipient_id = :admin_id AND read_at IS NULL');
            $stmt->execute(['admin_id' => $adminId]);
        } catch (PDOException) {
            // no-op
        }
    }

    public function all(string $table): array
    {
        $allowed = [
            'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
            'library_resources', 'faqs', 'messages', 'pages', 'users',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes',
            'testimonials', 'social_updates'
        ];
        if (!in_array($table, $allowed, true)) {
            return [];
        }
        try {
            return $this->pdo->query("SELECT * FROM {$table} ORDER BY id DESC")->fetchAll();
        } catch (PDOException) {
            return [];
        }
    }

    public function deleteById(string $table, int $id): bool
    {
        $allowed = [
            'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
            'library_resources', 'faqs', 'pages', 'users', 'messages',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes',
            'testimonials', 'social_updates'
        ];
        if (!in_array($table, $allowed, true)) {
            return false;
        }
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }

    public function findById(string $table, int $id): ?array
    {
        $allowed = [
            'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
            'library_resources', 'faqs', 'pages', 'users', 'messages',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes',
            'testimonials', 'social_updates'
        ];
        if (!in_array($table, $allowed, true)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id=:id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function saveSettings(array $settings): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO settings(setting_key, setting_value) VALUES(:k,:v) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        foreach ($settings as $k => $v) {
            $stmt->execute(['k' => $k, 'v' => trim($v)]);
        }
    }

    public function getHiddenIds(string $entity): array
    {
        $stmt = $this->pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1');
        $stmt->execute(['k' => 'hidden_' . $entity . '_ids']);
        $raw = (string)($stmt->fetch()['setting_value'] ?? '');
        if ($raw === '') {
            return [];
        }
        return array_values(array_filter(array_map('intval', explode(',', $raw))));
    }

    public function setHiddenIds(string $entity, array $ids): void
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $ids))));
        sort($clean);
        $stmt = $this->pdo->prepare('INSERT INTO settings(setting_key, setting_value) VALUES(:k,:v) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        $stmt->execute(['k' => 'hidden_' . $entity . '_ids', 'v' => implode(',', $clean)]);
    }

    public function isEnabled(string $key, bool $default = true): bool
    {
        $stmt = $this->pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1');
        $stmt->execute(['k' => $key]);
        $value = $stmt->fetch()['setting_value'] ?? null;
        if ($value === null || $value === '') {
            return $default;
        }
        return in_array(strtolower((string)$value), ['1', 'true', 'yes', 'on'], true);
    }

    private function filterHidden(string $entity, array $rows): array
    {
        if ($rows === []) {
            return [];
        }
        $hidden = $this->getHiddenIds($entity);
        if ($hidden === []) {
            return $rows;
        }
        return array_values(array_filter($rows, fn($row) => !in_array((int)($row['id'] ?? 0), $hidden, true)));
    }

    private function getDecodedProgrammeTemplate(string $programmeName): array
    {
        $raw = $this->getSettingValue($this->programmeTemplateSettingKey($programmeName));
        if ($raw === null || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function getDecodedProgrammeOverride(string $programmeSlug): array
    {
        if ($programmeSlug === '') {
            return [];
        }
        $raw = $this->getSettingValue($this->programmeOverrideSettingKey($programmeSlug));
        if ($raw === null || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function programmeTemplateSettingKey(string $programmeName): string
    {
        $familySlug = slugify($this->extractProgrammeFamilyName($programmeName));
        return self::PROGRAMME_TEMPLATE_KEY_PREFIX . $familySlug;
    }

    private function programmeOverrideSettingKey(string $programmeSlug): string
    {
        return self::PROGRAMME_OVERRIDE_KEY_PREFIX . trim($programmeSlug);
    }

    private function extractProgrammeFamilyName(string $programmeName): string
    {
        $clean = trim(preg_replace('/^(Diploma|Certificate|Artisan|Short Course)\s+(in\s+)?/i', '', trim($programmeName)) ?? '');
        return $clean === '' ? trim($programmeName) : $clean;
    }

    private function decodeLines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', trim($value)) ?: [])));
    }

    private function encodeLines(string $value): string
    {
        return implode("\n", $this->decodeLines($value));
    }

    private function programmeMetricsMap(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'programme_metric\\_%\\_views' ESCAPE '\\' OR setting_key LIKE 'programme_metric\\_%\\_applications' ESCAPE '\\'");
            $rows = $stmt->fetchAll();
        } catch (PDOException) {
            return [];
        }

        $map = [];
        foreach ($rows as $row) {
            $key = (string)($row['setting_key'] ?? '');
            $value = (int)($row['setting_value'] ?? 0);
            if (!preg_match('/^programme_metric_(.+)_(views|applications)$/', $key, $matches)) {
                continue;
            }
            $slug = (string)($matches[1] ?? '');
            $metric = (string)($matches[2] ?? '');
            if ($slug === '' || $metric === '') {
                continue;
            }
            $map[$slug][$metric] = $value;
        }
        return $map;
    }

    // ── Testimonials ──────────────────────────────────────────

    public function getTestimonials(bool $visibleOnly = true): array
    {
        $sql = 'SELECT * FROM testimonials';
        if ($visibleOnly) {
            $sql .= ' WHERE is_visible = 1';
        }
        $sql .= ' ORDER BY sort_order ASC, id ASC';
        try {
            return $this->pdo->query($sql)->fetchAll() ?: [];
        } catch (PDOException) {
            return [];
        }
    }

    public function saveMessageReply(int $messageId, string $subject, string $body, ?int $adminId): bool
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE messages SET replied_at = NOW(), reply_subject = :subject, reply_body = :body, replied_by = :admin_id WHERE id = :id');
            return $stmt->execute(['subject' => $subject, 'body' => $body, 'admin_id' => $adminId, 'id' => $messageId]);
        } catch (PDOException) {
            return false;
        }
    }

    // ── Social Updates ────────────────────────────────────────

    public function getSocialUpdates(bool $visibleOnly = true, int $limit = 10): array
    {
        $sql = 'SELECT * FROM social_updates';
        if ($visibleOnly) {
            $sql .= ' WHERE is_visible = 1';
        }
        $sql .= ' ORDER BY is_pinned DESC, COALESCE(posted_at, created_at) DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        try {
            return $this->pdo->query($sql)->fetchAll() ?: [];
        } catch (PDOException) {
            return [];
        }
    }
}
