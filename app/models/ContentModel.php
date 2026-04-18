<?php
class ContentModel
{
    private PDO $pdo;
    private const PROGRAMME_TEMPLATE_KEY_PREFIX = 'programme_template_';
    private const PROGRAMME_OVERRIDE_KEY_PREFIX = 'programme_override_';

    public function __construct(array $config)
    {
        $this->pdo = Database::getInstance($config['db']);
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

    public function all(string $table): array
    {
        $allowed = [
            'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
            'library_resources', 'faqs', 'messages', 'pages', 'users',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes'
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
            'library_resources', 'faqs', 'pages', 'users',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes'
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
            'library_resources', 'faqs', 'pages', 'users',
            'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes'
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
}
