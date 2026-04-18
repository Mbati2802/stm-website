<?php
class AdminContentController extends Controller
{
    private array $entities = [
        'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
        'library_resources', 'faqs', 'pages', 'users',
        'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials'
    ];
    private const SETTINGS_TEXT_FIELDS = [
        'phone',
        'email',
        'location',
        'current_intake',
        'top_message',
        'application_confirmation_message',
        'home_hero_title',
        'home_hero_description',
        'home_hero_primary_cta_label',
        'home_hero_primary_cta_link',
        'home_hero_secondary_cta_label',
        'home_hero_secondary_cta_link',
        'hero_images',
        'home_value_cards',
        'about_title',
        'about_intro',
        'about_mission',
        'about_vision',
        'about_values',
        'about_why_choose',
        'about_stats',
        'about_differentiators',
        'about_programmes',
        'about_commitment',
        'about_short_tagline',
        'about_cta_primary_label',
        'about_cta_primary_link',
        'about_cta_secondary_label',
        'about_cta_secondary_link',
        'principal_name',
        'principal_title',
        'principal_message',
        'principal_vision_points',
        'principal_focus_areas',
        'principal_signature',
        'principal_image',
        'principal_email',
        'principal_facebook',
        'principal_x',
        'principal_linkedin',
        'admission_number_format',
        'registrar_email',
        'registrar_image',
        'home_programme_images_json',
        'programme_detail_image',
        'home_extra_sections_json',
        'banner_home',
        'banner_programmes',
        'banner_about',
        'banner_contact',
        'banner_events',
        'banner_library',
        'banner_media',
        'banner_default_height',
    ];
    private const SETTINGS_TOGGLE_FIELDS = [
        'show_page_about',
        'show_page_programmes',
        'show_page_library',
        'show_page_media',
        'show_page_contact',
        'show_page_faqs',
        'show_page_principal',
        'show_home_hero',
        'show_home_cards',
        'show_home_banner',
        'show_home_why',
        'show_home_courses',
        'show_home_testimonials',
        'show_home_events',
        'show_home_news',
        'show_home_cta',
    ];

    public function list(string $entity): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            http_response_code(404);
            echo 'Invalid entity';
            return;
        }

        $model = new ContentModel($this->config);
        $hiddenIds = $model->getHiddenIds($entity);
        $this->view('admin/list', [
            'metaTitle' => 'Manage ' . ucfirst(str_replace('_', ' ', $entity)),
            'entity' => $entity,
            'rows' => $model->all($entity),
            'hiddenIds' => $hiddenIds,
        ]);
    }

    public function create(string $entity): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            http_response_code(404);
            echo 'Invalid entity';
            return;
        }

        $viewData = ['metaTitle' => 'Create ' . ucfirst($entity), 'entity' => $entity, 'isEdit' => false, 'row' => []];
        if ($entity === 'programmes') {
            $viewData['programmeContent'] = (new ContentModel($this->config))->getProgrammeContentForEditor(['name' => '', 'slug' => '']);
        }
        $this->view('admin/form', $viewData);
    }

    public function store(string $entity): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            $this->redirect('admin');
        }

        $this->persistEntity($entity, null);

        flash('success', ucfirst($entity) . ' saved successfully.');
        $this->redirect('admin/list/' . $entity);
    }

    public function edit(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            http_response_code(404);
            echo 'Invalid entity';
            return;
        }
        $model = new ContentModel($this->config);
        $row = $model->findById($entity, $id);
        if (!$row) {
            flash('error', 'Record not found.');
            $this->redirect('admin/list/' . $entity);
        }
        $this->view('admin/form', [
            'metaTitle' => 'Edit ' . ucfirst(str_replace('_', ' ', $entity)),
            'entity' => $entity,
            'isEdit' => true,
            'row' => $row,
            'programmeContent' => $entity === 'programmes' ? $model->getProgrammeContentForEditor($row) : null,
        ]);
    }

    public function update(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            $this->redirect('admin');
        }
        $this->persistEntity($entity, $id);
        flash('success', ucfirst($entity) . ' updated successfully.');
        $this->redirect('admin/list/' . $entity);
    }

    public function delete(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        if ($entity === 'users') {
            $target = $model->findById('users', $id);
            if ($target === null || !Auth::canManageRole((string)($target['role'] ?? 'teacher'))) {
                flash('error', 'You do not have permission to delete this user.');
                $this->redirect('admin/list/users');
            }
            if ((int)($target['id'] ?? 0) === (int)($_SESSION['admin_id'] ?? 0)) {
                flash('error', 'You cannot delete your own account.');
                $this->redirect('admin/list/users');
            }
        }
        $model->deleteById($entity, $id);
        flash('success', 'Item deleted.');
        $this->redirect('admin/list/' . $entity);
    }

    public function toggleVisibility(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity)) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $hidden = $model->getHiddenIds($entity);
        if (in_array($id, $hidden, true)) {
            $hidden = array_values(array_filter($hidden, fn($v) => $v !== $id));
            flash('success', 'Item is now visible.');
        } else {
            $hidden[] = $id;
            flash('success', 'Item hidden from public UI.');
        }
        $model->setHiddenIds($entity, $hidden);
        $this->redirect('admin/list/' . $entity);
    }

    public function messages(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('messages')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $this->view('admin/messages', ['metaTitle' => 'Contact Messages', 'rows' => $model->all('messages')]);
    }

    public function exportMessages(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('messages')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $rows = $model->all('messages');

        $filename = 'messages_export_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        $columns = ['Name', 'Email', 'Phone', 'Subject', 'Message', 'Date'];
        echo implode("\t", $columns) . "\n";

        foreach ($rows as $row) {
            $line = [
                $this->cleanExportValue((string)($row['name'] ?? '')),
                $this->cleanExportValue((string)($row['email'] ?? '')),
                $this->cleanExportValue((string)($row['phone'] ?? '')),
                $this->cleanExportValue((string)($row['subject'] ?? '')),
                $this->cleanExportValue((string)($row['message'] ?? '')),
                $this->cleanExportValue((string)($row['created_at'] ?? '')),
            ];
            echo implode("\t", $line) . "\n";
        }
        exit;
    }

    public function eventRegistrations(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('events')) {
            $this->redirect('admin');
        }
        $pdo = Database::getInstance($this->config['db']);
        try {
            $rows = $pdo->query('
                SELECT er.*, e.title AS event_title, e.slug AS event_slug
                FROM event_registrations er
                LEFT JOIN events e ON e.id = er.event_id
                ORDER BY er.created_at DESC, er.id DESC
            ')->fetchAll();
        } catch (PDOException) {
            $rows = [];
            flash('error', 'Event registrations table is missing. Create `event_registrations` in MySQL.');
        }

        $this->view('admin/event_registrations', [
            'metaTitle' => 'Event Registrations',
            'rows' => $rows,
        ]);
    }

    public function settings(): void
    {
        Auth::requireAdmin();
        if (Auth::isTeacher()) {
            flash('error', 'You do not have permission to access settings.');
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        try {
            $settings = $model->getSettings();
        } catch (Throwable) {
            $settings = [];
            flash('error', 'Unable to load settings right now. Confirm the `settings` table exists and try again.');
        }
        $this->view('admin/settings', ['metaTitle' => 'Settings', 'settings' => $settings]);
    }

    public function students(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('students')) {
            $this->redirect('admin');
        }
        $portalModel = new StudentPortalModel($this->config);
        $contentModel = new ContentModel($this->config);
        $settings = $contentModel->getSettings();
        $this->view('admin/students', [
            'metaTitle' => 'Student Accounts',
            'rows' => $portalModel->allStudents(),
            'admissionNumberFormat' => (string)($settings['admission_number_format'] ?? 'STM/{YEAR}/{SEQ4}'),
        ]);
    }

    public function assignStudentAdmissionNumber(int $id): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('students')) {
            $this->redirect('admin');
        }
        $portalModel = new StudentPortalModel($this->config);
        $student = $portalModel->findStudentById($id);
        if ($student === null) {
            flash('error', 'Student not found.');
            $this->redirect('admin/students');
        }

        $admissionNumber = strtoupper(trim($_POST['admission_number'] ?? ''));
        if ($admissionNumber === '') {
            $contentModel = new ContentModel($this->config);
            $format = (string)($contentModel->getSettings()['admission_number_format'] ?? 'STM/{YEAR}/{SEQ4}');
            $admissionNumber = $this->buildAdmissionNumber($format, (int)$student['id']);
        }

        if (!preg_match('/^[A-Z0-9\/\-_]+$/', $admissionNumber)) {
            flash('error', 'Admission number format is invalid. Use letters, numbers, /, -, _.');
            $this->redirect('admin/students');
        }

        try {
            $portalModel->assignAdmissionNumber($id, $admissionNumber);
        } catch (PDOException) {
            flash('error', 'Admission number already exists. Please choose another one.');
            $this->redirect('admin/students');
        }

        flash('success', 'Admission number assigned successfully.');
        $this->redirect('admin/students');
    }

    public function bulkAssignAdmissionNumbers(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('students')) {
            $this->redirect('admin');
        }
        $portalModel = new StudentPortalModel($this->config);
        $contentModel = new ContentModel($this->config);
        $format = (string)($contentModel->getSettings()['admission_number_format'] ?? 'STM/{YEAR}/{SEQ4}');
        $students = $portalModel->allStudents();

        $assigned = 0;
        foreach ($students as $student) {
            $id = (int)($student['id'] ?? 0);
            $current = trim((string)($student['admission_number'] ?? ''));
            if ($id <= 0 || $current !== '') {
                continue;
            }

            $admissionNumber = $this->buildAdmissionNumber($format, $id);
            try {
                $portalModel->assignAdmissionNumber($id, $admissionNumber);
                $assigned++;
            } catch (PDOException) {
                // Skip duplicates and continue assigning others.
                continue;
            }
        }

        flash('success', 'Bulk generation complete. Assigned admission numbers to ' . $assigned . ' student(s).');
        $this->redirect('admin/students');
    }

    public function saveSettings(): void
    {
        Auth::requireAdmin();
        if (Auth::isTeacher()) {
            flash('error', 'You do not have permission to update settings.');
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        try {
            $settings = $this->collectSettingsFromRequest();
            $settings = $this->applySettingsImageUploads($settings, $model->getSettings());
            $model->saveSettings($settings);
            flash('success', 'Settings updated.');
        } catch (Throwable) {
            flash('error', 'Settings could not be saved. Please verify database schema and try again.');
        }
        $this->redirect('admin/settings');
    }

    private function collectSettingsFromRequest(): array
    {
        $settings = [];

        foreach (self::SETTINGS_TEXT_FIELDS as $field) {
            $settings[$field] = $_POST[$field] ?? '';
        }

        foreach (self::SETTINGS_TOGGLE_FIELDS as $field) {
            $settings[$field] = isset($_POST[$field]) ? '1' : '0';
        }

        return $settings;
    }

    private function applySettingsImageUploads(array $settings, array $currentSettings): array
    {
        $principalImage = $this->uploadFile('principal_image_file', ['image/jpeg', 'image/png', 'image/webp'], 'settings');
        if ($principalImage !== '') {
            $settings['principal_image'] = $principalImage;
        } elseif (($settings['principal_image'] ?? '') === '' && isset($currentSettings['principal_image'])) {
            $settings['principal_image'] = $currentSettings['principal_image'];
        }

        $heroImages = $this->uploadMultipleFiles('hero_image_files', ['image/jpeg', 'image/png', 'image/webp'], 'settings');
        if ($heroImages !== []) {
            $settings['hero_images'] = json_encode($heroImages, JSON_UNESCAPED_SLASHES);
        } elseif (($settings['hero_images'] ?? '') === '' && isset($currentSettings['hero_images'])) {
            $settings['hero_images'] = $currentSettings['hero_images'];
        }

        $programmeCards = $this->uploadMultipleFiles('home_programme_image_files', ['image/jpeg', 'image/png', 'image/webp'], 'settings');
        if ($programmeCards !== []) {
            $existing = [];
            if (!empty($settings['home_programme_images_json'])) {
                $decoded = json_decode((string)$settings['home_programme_images_json'], true);
                if (is_array($decoded)) {
                    $existing = $decoded;
                }
            } elseif (!empty($currentSettings['home_programme_images_json'])) {
                $decoded = json_decode((string)$currentSettings['home_programme_images_json'], true);
                if (is_array($decoded)) {
                    $existing = $decoded;
                }
            }
            foreach ($programmeCards as $index => $img) {
                $existing['uploaded_' . ($index + 1)] = $img;
            }
            $settings['home_programme_images_json'] = json_encode($existing, JSON_UNESCAPED_SLASHES);
        } elseif (($settings['home_programme_images_json'] ?? '') === '' && isset($currentSettings['home_programme_images_json'])) {
            $settings['home_programme_images_json'] = $currentSettings['home_programme_images_json'];
        }

        $programmeDetailImage = $this->uploadFile('programme_detail_image_file', ['image/jpeg', 'image/png', 'image/webp'], 'settings');
        if ($programmeDetailImage !== '') {
            $settings['programme_detail_image'] = $programmeDetailImage;
        } elseif (($settings['programme_detail_image'] ?? '') === '' && isset($currentSettings['programme_detail_image'])) {
            $settings['programme_detail_image'] = $currentSettings['programme_detail_image'];
        }

        $bannerFields = [
            'banner_home_file' => 'banner_home',
            'banner_programmes_file' => 'banner_programmes',
            'banner_about_file' => 'banner_about',
            'banner_contact_file' => 'banner_contact',
            'banner_events_file' => 'banner_events',
            'banner_library_file' => 'banner_library',
            'banner_media_file' => 'banner_media',
        ];
        foreach ($bannerFields as $fileField => $settingKey) {
            $uploaded = $this->uploadFile($fileField, ['image/jpeg', 'image/png', 'image/webp'], 'banners');
            if ($uploaded !== '') {
                $settings[$settingKey] = $uploaded;
            } elseif (($settings[$settingKey] ?? '') === '' && isset($currentSettings[$settingKey])) {
                $settings[$settingKey] = $currentSettings[$settingKey];
            }
        }

        return $settings;
    }

    private function persistEntity(string $entity, ?int $id): void
    {
        $pdo = Database::getInstance($this->config['db']);
        $isUpdate = $id !== null;

        switch ($entity) {
            case 'departments':
                $name = trim($_POST['name'] ?? '');
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE departments SET name=:name, slug=:slug, description=:description WHERE id=:id');
                    $stmt->execute(['name' => $name, 'slug' => slugify($name), 'description' => trim($_POST['description'] ?? ''), 'id' => $id]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO departments(name, slug, description, created_at) VALUES(:name, :slug, :description, NOW())');
                    $stmt->execute(['name' => $name, 'slug' => slugify($name), 'description' => trim($_POST['description'] ?? '')]);
                }
                break;
            case 'programmes':
                $name = trim($_POST['name'] ?? '');
                $slug = slugify($name);
                $oldSlug = '';
                if ($isUpdate) {
                    $existing = (new ContentModel($this->config))->findById('programmes', (int)$id);
                    $oldSlug = (string)($existing['slug'] ?? '');
                }
                $params = [
                    'name' => $name,
                    'slug' => $slug,
                    'category' => trim($_POST['category'] ?? 'Diploma'),
                    'terms' => (int)($_POST['terms'] ?? 1),
                    'department_id' => (int)($_POST['department_id'] ?? 1),
                    'description' => plain_text_multiline($_POST['description'] ?? ''),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE programmes SET name=:name, slug=:slug, category=:category, terms=:terms, department_id=:department_id, description=:description WHERE id=:id');
                    $params['id'] = $id;
                    $stmt->execute($params);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO programmes(name, slug, category, terms, department_id, description, created_at) VALUES(:name, :slug, :category, :terms, :department_id, :description, NOW())');
                    $stmt->execute($params);
                }
                $model = new ContentModel($this->config);
                $model->saveProgrammeContentFromEditor($name, $slug, [
                    'content_scope' => $_POST['programme_content_scope'] ?? 'shared',
                    'overview' => $_POST['programme_overview'] ?? '',
                    'objectives' => $_POST['programme_objectives'] ?? '',
                    'content_areas' => $_POST['programme_content_areas'] ?? '',
                    'career_opportunities' => $_POST['programme_career_opportunities'] ?? '',
                    'why_study' => $_POST['programme_why_study'] ?? '',
                    'duration_override' => $_POST['programme_duration_override'] ?? '',
                    'entry_requirement_override' => $_POST['programme_entry_requirement_override'] ?? '',
                ]);
                if ($isUpdate && $oldSlug !== '' && $oldSlug !== $slug) {
                    $model->deleteSetting('programme_override_' . $oldSlug);
                }
                break;
            case 'faqs':
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE faqs SET question=:question, answer=:answer WHERE id=:id');
                    $stmt->execute(['question' => trim($_POST['question'] ?? ''), 'answer' => trim($_POST['answer'] ?? ''), 'id' => $id]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO faqs(question, answer, created_at) VALUES(:question, :answer, NOW())');
                    $stmt->execute(['question' => trim($_POST['question'] ?? ''), 'answer' => trim($_POST['answer'] ?? '')]);
                }
                break;
            case 'pages':
                $title = trim($_POST['title'] ?? '');
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE pages SET title=:title, slug=:slug, content=:content WHERE id=:id');
                    $stmt->execute(['title' => $title, 'slug' => slugify($_POST['slug'] ?? $title), 'content' => trim($_POST['content'] ?? ''), 'id' => $id]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO pages(title, slug, content, created_at) VALUES(:title, :slug, :content, NOW()) ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content)');
                    $stmt->execute(['title' => $title, 'slug' => slugify($_POST['slug'] ?? $title), 'content' => trim($_POST['content'] ?? '')]);
                }
                break;
            case 'gallery':
                $image = $this->uploadFile('image', ['image/jpeg', 'image/png', 'image/webp'], 'gallery');
                if ($isUpdate) {
                    $sql = 'UPDATE gallery SET title=:title, category=:category';
                    $params = ['title' => trim($_POST['title'] ?? ''), 'category' => trim($_POST['category'] ?? 'Events'), 'id' => $id];
                    if ($image !== '') {
                        $sql .= ', image_path=:image_path';
                        $params['image_path'] = $image;
                    }
                    $sql .= ' WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO gallery(title, category, image_path, created_at) VALUES(:title, :category, :image_path, NOW())');
                    $stmt->execute(['title' => trim($_POST['title'] ?? ''), 'category' => trim($_POST['category'] ?? 'Events'), 'image_path' => $image]);
                }
                break;
            case 'library_resources':
                $file = $this->uploadFile('file_path', ['application/pdf'], 'library');
                if ($isUpdate) {
                    $sql = 'UPDATE library_resources SET title=:title, summary=:summary';
                    $params = ['title' => trim($_POST['title'] ?? ''), 'summary' => trim($_POST['summary'] ?? ''), 'id' => $id];
                    if ($file !== '') {
                        $sql .= ', file_path=:file_path';
                        $params['file_path'] = $file;
                    }
                    $sql .= ' WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO library_resources(title, summary, file_path, created_at) VALUES(:title, :summary, :file_path, NOW())');
                    $stmt->execute(['title' => trim($_POST['title'] ?? ''), 'summary' => trim($_POST['summary'] ?? ''), 'file_path' => $file]);
                }
                break;
            case 'events':
                $title = trim($_POST['title'] ?? '');
                $startsAtRaw = trim($_POST['starts_at'] ?? '');
                $endsAtRaw = trim($_POST['ends_at'] ?? '');
                $startsAt = $startsAtRaw !== '' ? date('Y-m-d H:i:s', strtotime($startsAtRaw)) : date('Y-m-d H:i:s');
                $endsAt = $endsAtRaw !== '' ? date('Y-m-d H:i:s', strtotime($endsAtRaw)) : null;
                $uploadedImage = $this->uploadFile('event_image_file', ['image/jpeg', 'image/png', 'image/webp'], 'events');

                $params = [
                    'title' => $title,
                    'slug' => slugify($title),
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'category' => trim($_POST['category'] ?? 'Academic Workshops'),
                    'time_label' => trim($_POST['time_label'] ?? ''),
                    'location' => trim($_POST['location'] ?? ''),
                    'venue_type' => trim($_POST['venue_type'] ?? 'Campus'),
                    'registration_status' => trim($_POST['registration_status'] ?? 'Open'),
                    'registration_url' => trim($_POST['registration_url'] ?? ''),
                    'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                    'summary' => trim($_POST['summary'] ?? ''),
                    'body' => trim($_POST['body'] ?? ''),
                    'image_path' => $uploadedImage !== '' ? $uploadedImage : trim($_POST['image_path'] ?? ''),
                ];

                try {
                    if ($isUpdate) {
                        $stmt = $pdo->prepare('UPDATE events SET title=:title, slug=:slug, starts_at=:starts_at, ends_at=:ends_at, category=:category, time_label=:time_label, location=:location, venue_type=:venue_type, registration_status=:registration_status, registration_url=:registration_url, is_featured=:is_featured, summary=:summary, body=:body, image_path=:image_path WHERE id=:id');
                        $params['id'] = $id;
                        $stmt->execute($params);
                    } else {
                        $stmt = $pdo->prepare('INSERT INTO events(title, slug, starts_at, ends_at, category, time_label, location, venue_type, registration_status, registration_url, is_featured, summary, body, image_path, created_at) VALUES(:title, :slug, :starts_at, :ends_at, :category, :time_label, :location, :venue_type, :registration_status, :registration_url, :is_featured, :summary, :body, :image_path, NOW())');
                        $stmt->execute($params);
                    }
                } catch (PDOException) {
                    flash('error', 'Events table is missing or outdated. Run the latest SQL schema for `events` in MySQL, then try again.');
                    $this->redirect('admin/list/events');
                }
                break;
            case 'users':
                $name = trim($_POST['name'] ?? '');
                $email = strtolower(trim($_POST['email'] ?? ''));
                $role = strtolower(trim($_POST['role'] ?? 'teacher'));
                $status = strtolower(trim($_POST['status'] ?? 'active'));
                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    flash('error', 'Provide valid user details.');
                    $this->redirect('admin/list/users');
                }
                if (!in_array($role, ['super_admin', 'junior_admin', 'teacher'], true) || !Auth::canManageRole($role)) {
                    flash('error', 'You do not have permission to assign this role.');
                    $this->redirect('admin/list/users');
                }
                if (!in_array($status, ['active', 'disabled'], true)) {
                    $status = 'active';
                }
                $password = (string)($_POST['password'] ?? '');
                $passwordConfirm = (string)($_POST['password_confirm'] ?? '');
                if (!$isUpdate && strlen($password) < 6) {
                    flash('error', 'Password must be at least 6 characters.');
                    $this->redirect('admin/list/users');
                }
                if ($password !== '' && $password !== $passwordConfirm) {
                    flash('error', 'Password confirmation does not match.');
                    $this->redirect('admin/list/users');
                }

                if ($isUpdate) {
                    $existing = (new ContentModel($this->config))->findById('users', (int)$id);
                    if ($existing === null) {
                        flash('error', 'User not found.');
                        $this->redirect('admin/list/users');
                    }
                    if (!Auth::canManageRole((string)($existing['role'] ?? 'teacher'))) {
                        flash('error', 'You do not have permission to edit this user.');
                        $this->redirect('admin/list/users');
                    }
                    $sql = 'UPDATE users SET name=:name, email=:email, role=:role, status=:status';
                    $params = ['name' => $name, 'email' => $email, 'role' => $role, 'status' => $status, 'id' => $id];
                    if ($password !== '') {
                        $sql .= ', password=:password';
                        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
                    }
                    $sql .= ' WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO users(name, email, password, role, status, created_by, created_at) VALUES(:name, :email, :password, :role, :status, :created_by, NOW())');
                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => $role,
                        'status' => $status,
                        'created_by' => (int)($_SESSION['admin_id'] ?? 0),
                    ]);
                }
                break;
            case 'portal_courses':
                $stmtData = [
                    'programme_id' => (int)($_POST['programme_id'] ?? 0),
                    'teacher_id' => (int)($_POST['teacher_id'] ?? 0),
                    'code' => trim($_POST['code'] ?? ''),
                    'title' => trim($_POST['title'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE portal_courses SET programme_id=:programme_id, teacher_id=:teacher_id, code=:code, title=:title, description=:description WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO portal_courses(programme_id, teacher_id, code, title, description, created_at) VALUES(:programme_id, :teacher_id, :code, :title, :description, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            case 'programme_timetables':
                $file = $this->uploadFile('file_path', ['application/pdf'], 'timetables');
                $stmtData = [
                    'programme_id' => (int)($_POST['programme_id'] ?? 0),
                    'title' => trim($_POST['title'] ?? ''),
                    'details' => trim($_POST['details'] ?? ''),
                    'file_path' => $file !== '' ? $file : trim($_POST['file_path_existing'] ?? ''),
                ];
                if ($isUpdate) {
                    if ($file === '' && isset($_POST['current_file_path'])) {
                        $stmtData['file_path'] = trim((string)$_POST['current_file_path']);
                    }
                    $stmt = $pdo->prepare('UPDATE programme_timetables SET programme_id=:programme_id, title=:title, details=:details, file_path=:file_path WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO programme_timetables(programme_id, title, details, file_path, created_at) VALUES(:programme_id, :title, :details, :file_path, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            case 'course_grades':
                $stmtData = [
                    'student_id' => (int)($_POST['student_id'] ?? 0),
                    'course_id' => (int)($_POST['course_id'] ?? 0),
                    'grade' => trim($_POST['grade'] ?? ''),
                    'remarks' => trim($_POST['remarks'] ?? ''),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE course_grades SET student_id=:student_id, course_id=:course_id, grade=:grade, remarks=:remarks WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO course_grades(student_id, course_id, grade, remarks, created_at) VALUES(:student_id, :course_id, :grade, :remarks, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            case 'course_assignments':
                $file = $this->uploadFile('file_path', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], 'assignments');
                $dueRaw = trim($_POST['due_at'] ?? '');
                $stmtData = [
                    'course_id' => (int)($_POST['course_id'] ?? 0),
                    'title' => trim($_POST['title'] ?? ''),
                    'instructions' => trim($_POST['instructions'] ?? ''),
                    'due_at' => $dueRaw !== '' ? date('Y-m-d H:i:s', strtotime($dueRaw)) : null,
                    'file_path' => $file !== '' ? $file : trim((string)($_POST['current_file_path'] ?? '')),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE course_assignments SET course_id=:course_id, title=:title, instructions=:instructions, due_at=:due_at, file_path=:file_path WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO course_assignments(course_id, title, instructions, due_at, file_path, created_at) VALUES(:course_id, :title, :instructions, :due_at, :file_path, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            case 'study_materials':
                $file = $this->uploadFile('file_path', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], 'study-materials');
                $stmtData = [
                    'course_id' => (int)($_POST['course_id'] ?? 0),
                    'title' => trim($_POST['title'] ?? ''),
                    'summary' => trim($_POST['summary'] ?? ''),
                    'file_path' => $file !== '' ? $file : trim((string)($_POST['current_file_path'] ?? '')),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE study_materials SET course_id=:course_id, title=:title, summary=:summary, file_path=:file_path WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO study_materials(course_id, title, summary, file_path, created_at) VALUES(:course_id, :title, :summary, :file_path, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            default:
                $title = trim($_POST['title'] ?? '');
                $uploadedImage = $this->uploadFile('image_file', ['image/jpeg', 'image/png', 'image/webp'], $entity);
                $params = [
                    'title' => $title,
                    'slug' => slugify($title),
                    'summary' => trim($_POST['summary'] ?? ''),
                    'body' => trim($_POST['body'] ?? ''),
                    'image_path' => $uploadedImage !== '' ? $uploadedImage : trim($_POST['image_path'] ?? ''),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare("UPDATE {$entity} SET title=:title, slug=:slug, summary=:summary, body=:body, image_path=:image_path WHERE id=:id");
                    $params['id'] = $id;
                    $stmt->execute($params);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO {$entity}(title, slug, summary, body, image_path, created_at) VALUES(:title, :slug, :summary, :body, :image_path, NOW())");
                    $stmt->execute($params);
                }
        }
    }

    public function mediaLibrary(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('media')) {
            $this->redirect('admin');
        }
        $pdo = Database::getInstance($this->config['db']);
        try {
            $rows = $pdo->query('SELECT * FROM media_assets ORDER BY id DESC')->fetchAll();
        } catch (PDOException) {
            $rows = [];
            flash('error', 'Media table missing. Create `media_assets` in MySQL.');
        }
        $this->view('admin/media_library', ['metaTitle' => 'Media Library', 'rows' => $rows]);
    }

    public function uploadMedia(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('media')) {
            $this->redirect('admin');
        }
        $pdo = Database::getInstance($this->config['db']);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $filePath = $this->uploadFile('media_file', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 'media');
        if ($filePath === '') {
            flash('error', 'Please upload a valid image file.');
            $this->redirect('admin/media');
        }

        try {
            $stmt = $pdo->prepare('INSERT INTO media_assets(title, file_path, category, created_at) VALUES(:title,:file_path,:category,NOW())');
            $stmt->execute([
                'title' => $title !== '' ? $title : basename($filePath),
                'file_path' => $filePath,
                'category' => $category !== '' ? $category : 'General',
            ]);
            flash('success', 'Media uploaded.');
        } catch (PDOException) {
            flash('error', 'Media table missing. Create `media_assets` in MySQL.');
        }

        $this->redirect('admin/media');
    }

    public function deleteMedia(int $id): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('media')) {
            $this->redirect('admin');
        }
        $pdo = Database::getInstance($this->config['db']);
        try {
            $stmt = $pdo->prepare('SELECT file_path FROM media_assets WHERE id=:id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch() ?: null;
            if ($row && isset($row['file_path'])) {
                $relative = ltrim((string)$row['file_path'], '/');
                $absolute = $this->webRootPath() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
                if (is_file($absolute)) {
                    @unlink($absolute);
                }
            }

            $del = $pdo->prepare('DELETE FROM media_assets WHERE id=:id');
            $del->execute(['id' => $id]);
            flash('success', 'Media deleted.');
        } catch (PDOException) {
            flash('error', 'Unable to delete media right now.');
        }
        $this->redirect('admin/media');
    }

    private function uploadFile(string $field, array $allowedMime, string $folder): string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $tmp = $_FILES[$field]['tmp_name'];
        $mime = mime_content_type($tmp) ?: '';
        if (!in_array($mime, $allowedMime, true)) {
            return '';
        }

        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES[$field]['name']);
        $dir = $this->uploadsRootPath() . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        move_uploaded_file($tmp, $dir . DIRECTORY_SEPARATOR . $name);
        return '/uploads/' . $folder . '/' . $name;
    }

    private function uploadMultipleFiles(string $field, array $allowedMime, string $folder): array
    {
        if (!isset($_FILES[$field]['name']) || !is_array($_FILES[$field]['name'])) {
            return [];
        }

        $dir = $this->uploadsRootPath() . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $uploaded = [];
        $count = count($_FILES[$field]['name']);
        for ($i = 0; $i < $count; $i++) {
            if (($_FILES[$field]['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmp = (string)($_FILES[$field]['tmp_name'][$i] ?? '');
            if ($tmp === '' || !is_file($tmp)) {
                continue;
            }

            $mime = mime_content_type($tmp) ?: '';
            if (!in_array($mime, $allowedMime, true)) {
                continue;
            }

            $originalName = (string)($_FILES[$field]['name'][$i] ?? ('image_' . $i));
            $name = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            if (move_uploaded_file($tmp, $dir . DIRECTORY_SEPARATOR . $name)) {
                $uploaded[] = '/uploads/' . $folder . '/' . $name;
            }
        }

        return $uploaded;
    }

    private function webRootPath(): string
    {
        $root = realpath(__DIR__ . '/../../../');
        return $root !== false ? $root : __DIR__ . '/../../../';
    }

    private function uploadsRootPath(): string
    {
        return $this->webRootPath() . DIRECTORY_SEPARATOR . 'uploads';
    }

    private function cleanExportValue(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = str_replace("\t", ' ', $value);
        return '"' . str_replace('"', '""', $value) . '"';
    }

    private function canAccessEntity(string $entity): bool
    {
        if (!in_array($entity, $this->entities, true)) {
            return false;
        }
        return Auth::canManageEntity($entity);
    }

    private function buildAdmissionNumber(string $format, int $studentId): string
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $seq4 = str_pad((string)$studentId, 4, '0', STR_PAD_LEFT);
        $seq5 = str_pad((string)$studentId, 5, '0', STR_PAD_LEFT);
        $seq6 = str_pad((string)$studentId, 6, '0', STR_PAD_LEFT);
        $replacements = [
            '{YEAR}' => $year,
            '{YY}' => substr($year, -2),
            '{MM}' => $month,
            '{DD}' => $day,
            '{SEQ4}' => $seq4,
            '{SEQ5}' => $seq5,
            '{SEQ6}' => $seq6,
            '{ID}' => (string)$studentId,
        ];
        return strtr($format, $replacements);
    }
}
