<?php
class AdminContentController extends Controller
{
    private array $entities = [
        'programmes', 'departments', 'news', 'careers', 'tenders', 'events', 'gallery',
        'library_resources', 'faqs', 'pages', 'users',
        'portal_courses', 'programme_timetables', 'course_grades', 'course_assignments', 'study_materials', 'grading_schemes',
        'testimonials', 'social_updates'
    ];
    private const SETTINGS_TEXT_FIELDS = [
        'phone',
        'email',
        'admissions_email',
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
        'home_testimonials_json',
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
        'registrar_message',
        'registrar_about_text',
        'registrar_key_functions',
        'registrar_services',
        'registrar_office_hours',
        'registrar_important_notice',
        'home_programme_images_json',
        'programme_detail_image',
        'programme_sidebar_title',
        'programme_sidebar_text',
        'programme_sidebar_primary_label',
        'programme_sidebar_primary_link',
        'programme_sidebar_secondary_label',
        'programme_sidebar_secondary_link',
        'programme_sidebar_other_title',
        'programme_mosaic_images_json',
        'home_extra_sections_json',
        'home_page_snapshots_json',
        'home_page_snapshots_layout_json',
        'home_page_snapshots_columns',
        'banner_home',
        'banner_programmes',
        'banner_about',
        'banner_contact',
        'banner_events',
        'banner_library',
        'banner_media',
        'banner_default_height',
        'admin_reply_email_heading',
        'admin_reply_email_subheading',
        'admin_reply_email_footer_text',
        'admin_reply_email_logo_url',
        'admin_reply_email_bg_color',
        'admin_reply_email_card_color',
        'admin_reply_email_accent_color',
        'admin_reply_email_footer_bg_color',
        'testimonial_template',
        'testimonial_accent_color',
        'testimonial_bg_color',
        'testimonial_card_style',
        'testimonial_autoplay',
        'testimonial_speed',
        'testimonial_items_per_slide',
        'testimonial_slide_effect',
        'testimonial_grid_count',
        'social_updates_title',
        'social_updates_template',
        'social_updates_cards_per_row',
        'social_updates_show_images',
        'social_updates_content_lines',
        'social_updates_rows',
        'social_updates_bg_color',
        'social_updates_card_bg',
        'social_updates_accent_color',
        'facebook_page_id',
        'facebook_page_access_token',
        'instagram_business_account_id',
        'social_auto_fetch_enabled',
        'social_auto_fetch_last_run',
        'social_auto_fetch_cron_token',
        'admin_login_slug',
        'admin_login_allow_ips',
        'junior_admin_permissions',
        'junior_admin_view_permissions',
        'junior_admin_manage_permissions',
        'teacher_permissions',
        'teacher_view_permissions',
        'teacher_manage_permissions',
        'editor_view_permissions',
        'editor_manage_permissions',
        'viewer_view_permissions',
        'viewer_manage_permissions',
        'registrar_view_permissions',
        'registrar_manage_permissions',
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
        'show_home_extra_sections',
        'show_home_page_snapshots',
    ];

    public function list(string $entity): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity, 'view')) {
            http_response_code(404);
            echo 'Invalid entity';
            return;
        }

        $model = new ContentModel($this->config);
        $hiddenIds = $model->getHiddenIds($entity);
        $rows = $model->all($entity);
        if ($entity === 'users' && Auth::isJuniorAdmin()) {
            $rows = array_values(array_filter($rows, static fn($user) => (string)($user['role'] ?? '') !== 'super_admin'));
        }
        $viewData = [
            'metaTitle' => 'Manage ' . ucfirst(str_replace('_', ' ', $entity)),
            'entity' => $entity,
            'rows' => $rows,
            'hiddenIds' => $hiddenIds,
        ];
        if (in_array($entity, ['testimonials', 'social_updates'], true)) {
            $viewData['settings'] = $model->getSettings();
        }
        $this->view('admin/list', $viewData);
    }

    public function create(string $entity): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity, 'manage')) {
            http_response_code(404);
            echo 'Invalid entity';
            return;
        }

        $viewData = ['metaTitle' => 'Create ' . ucfirst($entity), 'entity' => $entity, 'isEdit' => false, 'row' => []];
        $viewData = array_merge($viewData, $this->buildFormRelations());
        if ($entity === 'programmes') {
            $viewData['programmeContent'] = (new ContentModel($this->config))->getProgrammeContentForEditor(['name' => '', 'slug' => '']);
            $viewData['programmeHomeCardImage'] = '';
        }
        $this->view('admin/form', $viewData);
    }

    public function store(string $entity): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity, 'manage')) {
            $this->redirect('admin');
        }

        $this->persistEntity($entity, null);

        flash('success', ucfirst($entity) . ' saved successfully.');
        $this->redirect('admin/list/' . $entity);
    }

    public function edit(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity, 'view')) {
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
            'programmeHomeCardImage' => $entity === 'programmes' ? $this->programmeHomeCardImage((string)($row['name'] ?? '')) : '',
        ] + $this->buildFormRelations());
    }

    public function update(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity, 'manage')) {
            $this->redirect('admin');
        }
        $this->persistEntity($entity, $id);
        flash('success', ucfirst($entity) . ' updated successfully.');
        $this->redirect('admin/list/' . $entity);
    }

    public function delete(string $entity, int $id): void
    {
        Auth::requireAdmin();
        if (!$this->canAccessEntity($entity, 'manage')) {
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
        if (!$this->canAccessEntity($entity, 'manage')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $pdo = Database::getInstance($this->config['db']);
        $hidden = $model->getHiddenIds($entity);

        // Determine new visibility state
        $makingVisible = in_array($id, $hidden, true);
        if ($makingVisible) {
            $hidden = array_values(array_filter($hidden, fn($v) => $v !== $id));
            flash('success', 'Item is now visible.');
        } else {
            $hidden[] = $id;
            flash('success', 'Item hidden from public UI.');
        }
        $model->setHiddenIds($entity, $hidden);

        // Also update the is_visible column for entities that use it (so frontend queries work)
        if (in_array($entity, ['testimonials', 'social_updates'], true)) {
            $isVisible = $makingVisible ? 1 : 0;
            $stmt = $pdo->prepare("UPDATE {$entity} SET is_visible = :v WHERE id = :id");
            $stmt->execute(['v' => $isVisible, 'id' => $id]);
        }

        $this->redirect('admin/list/' . $entity);
    }

    public function messages(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('messages')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $rows = $model->all('messages');
        $filter = trim((string)($_GET['filter'] ?? 'all'));
        if ($filter === 'unread') {
            $rows = array_values(array_filter($rows, static fn($row) => empty($row['read_at'])));
        }
        $this->view('admin/messages', ['metaTitle' => 'Contact Messages', 'rows' => $rows, 'filter' => $filter]);
    }

    public function viewMessage(int $id): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('messages')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $message = $model->findById('messages', $id);
        if ($message === null) {
            flash('error', 'Message not found.');
            $this->redirect('admin/messages');
        }
        $model->markPublicMessageRead($id);
        $message = $model->findById('messages', $id) ?? $message;
        $this->view('admin/message_view', [
            'metaTitle' => 'Message Details',
            'message' => $message,
        ]);
    }

    public function replyMessage(int $id): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('messages')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $message = $model->findById('messages', $id);
        if ($message === null) {
            flash('error', 'Message not found.');
            $this->redirect('admin/messages');
        }

        $to = trim((string)($message['email'] ?? ''));
        $subject = trim((string)($_POST['reply_subject'] ?? ''));
        $body = trim((string)($_POST['reply_body'] ?? ''));
        $linksRaw = trim((string)($_POST['reply_links'] ?? ''));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL) || $subject === '' || $body === '') {
            flash('error', 'Provide a valid reply subject and message body.');
            $this->redirect('admin/messages/view/' . (int)$id);
        }

        $linkLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $linksRaw) ?: [])));
        $validLinks = array_values(array_filter($linkLines, static fn($u) => filter_var($u, FILTER_VALIDATE_URL)));
        $safeReplyBodyHtml = safe_html($body);
        $replyText = plain_text_multiline($body) . "\n\n--- Original Message ---\nFrom: " . (string)($message['name'] ?? '') . ' <' . $to . ">\nSubject: " . (string)($message['subject'] ?? '') . "\n" . (string)($message['message'] ?? '');
        if ($validLinks !== []) {
            $replyText .= "\n\nHelpful links:\n- " . implode("\n- ", $validLinks);
        }
        $settings = $model->getSettings();
        $appName = (string)($this->config['app_name'] ?? 'College');
        $logoUrl = trim((string)($settings['admin_reply_email_logo_url'] ?? ''));
        if ($logoUrl === '') {
            $logoUrl = base_url('assets/images/logo.png');
        }
        $contactPhone = trim((string)($settings['phone'] ?? '+254 791 309011'));
        $contactEmail = trim((string)($settings['email'] ?? 'contact@stmarysmchmcollege.ac.ke'));
        $heading = trim((string)($settings['admin_reply_email_heading'] ?? 'Thank you for your email'));
        $subheading = trim((string)($settings['admin_reply_email_subheading'] ?? ('Here is our response from ' . $appName)));
        $footerText = trim((string)($settings['admin_reply_email_footer_text'] ?? 'We value your message and are always ready to assist.'));
        $bgColor = $this->sanitizeHexColor((string)($settings['admin_reply_email_bg_color'] ?? ''), '#ffffff');
        $cardColor = $this->sanitizeHexColor((string)($settings['admin_reply_email_card_color'] ?? ''), '#f5f6fb');
        $accentColor = $this->sanitizeHexColor((string)($settings['admin_reply_email_accent_color'] ?? ''), '#5fc7e7');
        $footerBgColor = $this->sanitizeHexColor((string)($settings['admin_reply_email_footer_bg_color'] ?? ''), '#2c3653');
        $safeSenderName = e((string)($message['name'] ?? 'User'));
        $safeOriginalSubject = e((string)($message['subject'] ?? 'General Enquiry'));
        $safeContactEmail = e($contactEmail);
        $safeContactPhone = e($contactPhone);
        $safePhone2 = e('0101 711 499');
        $linksHtml = '';
        if ($validLinks !== []) {
            $items = '';
            foreach ($validLinks as $url) {
                $safeUrl = e($url);
                $items .= '<li style="margin:0 0 6px;"><a href="' . $safeUrl . '" style="color:#0d6efd;text-decoration:none;">' . $safeUrl . '</a></li>';
            }
            $linksHtml = '<div style="margin-top:14px;"><strong>Helpful Links</strong><ul style="margin:8px 0 0 18px;padding:0;">' . $items . '</ul></div>';
        }
        $html = '<!doctype html><html><body style="margin:0;padding:0;background:' . e($bgColor) . ';">'
            . '<div style="max-width:760px;margin:20px auto;padding:0 12px;">'
            . '<div style="background:' . e($cardColor) . ';border-top:4px solid ' . e($accentColor) . ';border-bottom:4px solid ' . e($accentColor) . ';">'
            . '<div style="padding:26px 34px 20px;text-align:center;">'
            . '<div style="text-align:center;margin:0 0 14px;">'
            . '<img src="' . e($logoUrl) . '" alt="' . e($appName) . ' logo" width="64" height="64" style="display:inline-block;width:64px;height:64px;object-fit:contain;border:0;outline:none;text-decoration:none;">'
            . '</div>'
            . '<h1 style="margin:0;color:#1f2a44;font-family:Arial,sans-serif;font-size:42px;line-height:1.1;">' . e($heading) . '</h1>'
            . '<p style="margin:8px 0 0;color:#6e7381;font-family:Arial,sans-serif;font-size:14px;">' . e($subheading) . '</p>'
            . '</div>'
            . '<div style="padding:0 34px 28px;">'
            . '<table role="presentation" style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;color:#1f2a44;">'
            . '<tr><td style="padding:10px 0;border-top:3px solid #2e3448;border-bottom:1px solid #2e3448;font-weight:700;width:110px;">Title</td><td style="padding:10px 0;border-top:3px solid #2e3448;border-bottom:1px solid #2e3448;">' . e($subject) . '</td></tr>'
            . '<tr><td style="padding:10px 0;border-bottom:2px solid #2e3448;font-weight:700;">Regarding</td><td style="padding:10px 0;border-bottom:2px solid #2e3448;">' . $safeOriginalSubject . '</td></tr>'
            . '</table>'
            . '<div style="margin-top:18px;font-family:Arial,sans-serif;color:#20293f;font-size:15px;line-height:1.65;">'
            . '<p style="margin:0 0 12px;">Hi, ' . $safeSenderName . '</p>'
            . '<div style="margin:0 0 12px;">' . $safeReplyBodyHtml . '</div>'
            . $linksHtml
            . '<p style="margin:16px 0 0;">Thank you,<br>' . e($appName) . '</p>'
            . '</div>'
            . '</div>'
            . '<div style="background:' . e($footerBgColor) . ';padding:18px 34px;color:#b9e7ff;font-family:Arial,sans-serif;font-size:13px;line-height:1.6;text-align:center;">'
            . '<strong style="color:#fff;">' . e($appName) . '</strong><br>'
            . '<a href="mailto:' . $safeContactEmail . '" style="color:#b9e7ff;text-decoration:none;">' . $safeContactEmail . '</a>'
            . ' | <a href="tel:' . $safeContactPhone . '" style="color:#b9e7ff;text-decoration:none;">' . $safeContactPhone . '</a>'
            . ' | <a href="tel:' . $safePhone2 . '" style="color:#b9e7ff;text-decoration:none;">' . $safePhone2 . '</a><br>' . e($footerText)
            . '</div>'
            . '</div></div></body></html>';
        $attachmentPath = $this->uploadFile('reply_attachment', ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], 'message-replies');
        if ($attachmentPath !== '') {
            $absolute = $this->resolveStoredFilePath($attachmentPath);
            $attachmentName = basename($absolute);
            $attachmentMime = mime_content_type($absolute) ?: 'application/octet-stream';
            $attachmentContent = @file_get_contents($absolute);
            $sent = is_string($attachmentContent)
                ? send_notification_email_with_attachment($to, $subject, $replyText, $attachmentName, $attachmentContent, $attachmentMime, $html)
                : false;
        } else {
            $sent = send_notification_email($to, $subject, $replyText, $html);
        }
        $model->saveMessageReply($id, $subject, $body, (int)($_SESSION['admin_id'] ?? 0));

        if (!$sent) {
            $errorDetail = trim(email_last_error_get());
            $messageText = 'Reply saved but email could not be delivered. Please check email settings.';
            if ($errorDetail !== '') {
                $messageText .= ' Reason: ' . $errorDetail;
            }
            flash('error', $messageText);
            $this->redirect('admin/messages/view/' . (int)$id);
        }

        flash('success', 'Reply sent successfully.');
        $this->redirect('admin/messages/view/' . (int)$id);
    }

    public function supportTickets(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('students')) {
            $this->redirect('admin');
        }
        $portalModel = new StudentPortalModel($this->config);
        $this->view('admin/support_tickets', [
            'metaTitle' => 'Student Support Tickets',
            'rows' => $portalModel->getAllSupportTickets(),
        ]);
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

    public function emailEventRegistrant(int $id): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('events')) {
            $this->redirect('admin');
        }
        $pdo = Database::getInstance($this->config['db']);
        $subject = trim((string)($_POST['subject'] ?? ''));
        $body = trim((string)($_POST['body'] ?? ''));
        if ($subject === '' || $body === '') {
            flash('error', 'Email subject and body are required.');
            $this->redirect('admin/event-registrations');
        }
        try {
            $stmt = $pdo->prepare('
                SELECT er.*, e.title AS event_title
                FROM event_registrations er
                LEFT JOIN events e ON e.id = er.event_id
                WHERE er.id = :id
                LIMIT 1
            ');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch() ?: null;
            if (!$row || !filter_var((string)($row['email'] ?? ''), FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Registrant email not found.');
                $this->redirect('admin/event-registrations');
            }
            $tableHtml = build_structured_notification_email($subject, [
                'Registrant' => (string)($row['name'] ?? ''),
                'Email' => (string)($row['email'] ?? ''),
                'Phone' => (string)($row['phone'] ?? ''),
                'Event' => (string)($row['event_title'] ?? 'Event'),
                'Message' => plain_text_multiline($body),
            ]);
            $sent = send_notification_email((string)$row['email'], $subject, plain_text_multiline($body), $tableHtml);
            if (!$sent) {
                flash('error', 'Could not send email to registrant.');
                $this->redirect('admin/event-registrations');
            }
            flash('success', 'Email sent to registrant successfully.');
            $this->redirect('admin/event-registrations');
        } catch (PDOException) {
            flash('error', 'Unable to process event registration email right now.');
            $this->redirect('admin/event-registrations');
        }
    }

    public function applications(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('messages')) {
            $this->redirect('admin');
        }
        $model = new ContentModel($this->config);
        $this->view('admin/applications', [
            'metaTitle' => 'Programme Applications',
            'rows' => $model->getProgrammeApplications(300),
            'trend' => $model->getDailyTrend('programme_applications', 30),
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
        $this->view('admin/settings', [
            'metaTitle' => 'Settings',
            'settings' => $settings,
            'emailDiagnostics' => email_delivery_last_status(),
            'emailDiagnosticsHistory' => email_delivery_recent_logs(25),
        ]);
    }

    public function internalMessages(): void
    {
        Auth::requireAdmin();
        $adminId = (int)($_SESSION['admin_id'] ?? 0);
        if ($adminId <= 0) {
            $this->redirect(admin_login_path());
        }
        $model = new ContentModel($this->config);
        $inbox = $model->getAdminInbox($adminId, 80);
        $sent = $model->getAdminSentMessages($adminId, 80);
        $model->markAdminInboxAsRead($adminId);
        $this->view('admin/internal_messages', [
            'metaTitle' => 'Team Messages',
            'users' => $model->getAdminUsers(),
            'inbox' => $inbox,
            'sent' => $sent,
        ]);
    }

    public function sendInternalMessage(): void
    {
        Auth::requireAdmin();
        $senderId = (int)($_SESSION['admin_id'] ?? 0);
        if ($senderId <= 0) {
            $this->redirect(admin_login_path());
        }
        $recipientId = (int)($_POST['recipient_id'] ?? 0);
        $subject = trim((string)($_POST['subject'] ?? ''));
        $body = trim((string)($_POST['body'] ?? ''));
        if ($recipientId <= 0 || $subject === '' || $body === '') {
            flash('error', 'Recipient, subject, and message body are required.');
            $this->redirect('admin/internal-messages');
        }
        if ($recipientId === $senderId) {
            flash('error', 'You cannot send a message to yourself.');
            $this->redirect('admin/internal-messages');
        }
        $model = new ContentModel($this->config);
        if (!$model->sendAdminMessage($senderId, $recipientId, $subject, $body)) {
            flash('error', 'Message could not be sent. Confirm the admin_messages table exists.');
            $this->redirect('admin/internal-messages');
        }
        flash('success', 'Message sent successfully.');
        $this->redirect('admin/internal-messages');
    }

    public function students(): void
    {
        Auth::requireAdmin();
        if (!Auth::canViewEntity('students')) {
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
            if (Auth::isJuniorAdmin() && !Auth::isSuperAdmin()) {
                $settings['junior_admin_permissions'] = (string)($model->getSettingValue('junior_admin_permissions') ?? '');
                $settings['junior_admin_view_permissions'] = (string)($model->getSettingValue('junior_admin_view_permissions') ?? '');
                $settings['junior_admin_manage_permissions'] = (string)($model->getSettingValue('junior_admin_manage_permissions') ?? '');
                $settings['teacher_permissions'] = (string)($model->getSettingValue('teacher_permissions') ?? '');
                $settings['teacher_view_permissions'] = (string)($model->getSettingValue('teacher_view_permissions') ?? '');
                $settings['teacher_manage_permissions'] = (string)($model->getSettingValue('teacher_manage_permissions') ?? '');
                $settings['editor_view_permissions'] = (string)($model->getSettingValue('editor_view_permissions') ?? '');
                $settings['editor_manage_permissions'] = (string)($model->getSettingValue('editor_manage_permissions') ?? '');
                $settings['viewer_view_permissions'] = (string)($model->getSettingValue('viewer_view_permissions') ?? '');
                $settings['viewer_manage_permissions'] = (string)($model->getSettingValue('viewer_manage_permissions') ?? '');
                $settings['registrar_view_permissions'] = (string)($model->getSettingValue('registrar_view_permissions') ?? '');
                $settings['registrar_manage_permissions'] = (string)($model->getSettingValue('registrar_manage_permissions') ?? '');
                $settings['admin_login_slug'] = (string)($model->getSettingValue('admin_login_slug') ?? 'admin/login');
            }
            $settings = $this->applySettingsImageUploads($settings, $model->getSettings());
            $model->saveSettings($settings);
            $_SESSION['junior_admin_permissions'] = (string)($settings['junior_admin_permissions'] ?? '');
            $_SESSION['junior_admin_view_permissions'] = (string)($settings['junior_admin_view_permissions'] ?? '');
            $_SESSION['junior_admin_manage_permissions'] = (string)($settings['junior_admin_manage_permissions'] ?? '');
            $_SESSION['teacher_permissions'] = (string)($settings['teacher_permissions'] ?? '');
            $_SESSION['teacher_view_permissions'] = (string)($settings['teacher_view_permissions'] ?? '');
            $_SESSION['teacher_manage_permissions'] = (string)($settings['teacher_manage_permissions'] ?? '');
            $_SESSION['editor_view_permissions'] = (string)($settings['editor_view_permissions'] ?? '');
            $_SESSION['editor_manage_permissions'] = (string)($settings['editor_manage_permissions'] ?? '');
            $_SESSION['viewer_view_permissions'] = (string)($settings['viewer_view_permissions'] ?? '');
            $_SESSION['viewer_manage_permissions'] = (string)($settings['viewer_manage_permissions'] ?? '');
            $_SESSION['registrar_view_permissions'] = (string)($settings['registrar_view_permissions'] ?? '');
            $_SESSION['registrar_manage_permissions'] = (string)($settings['registrar_manage_permissions'] ?? '');
            flash('success', 'Settings updated.');
        } catch (Throwable) {
            flash('error', 'Settings could not be saved. Please verify database schema and try again.');
        }
        $this->redirect('admin/settings');
    }

    public function savePartialSettings(): void
    {
        Auth::requireAdmin();
        if (Auth::isTeacher()) {
            flash('error', 'You do not have permission to update settings.');
            $this->redirect('admin');
        }
        $redirect = trim((string)($_POST['_redirect'] ?? 'admin/settings'));
        $model = new ContentModel($this->config);
        $allowed = array_merge(self::SETTINGS_TEXT_FIELDS, self::SETTINGS_TOGGLE_FIELDS);
        try {
            $settings = [];
            foreach ($_POST as $k => $v) {
                if (in_array($k, $allowed, true)) {
                    $settings[$k] = (string)$v;
                }
            }
            if ($settings !== []) {
                $model->saveSettings($settings);
            }
            flash('success', 'Settings updated.');
        } catch (Throwable) {
            flash('error', 'Settings could not be saved.');
        }
        $this->redirect($redirect);
    }

    private function collectSettingsFromRequest(): array
    {
        $current = (new ContentModel($this->config))->getSettings();
        $settings = [];

        foreach (self::SETTINGS_TEXT_FIELDS as $field) {
            $settings[$field] = array_key_exists($field, $_POST)
                ? (string)$_POST[$field]
                : (string)($current[$field] ?? '');
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

        $mosaicImages = $this->uploadMultipleFiles('programme_mosaic_image_files', ['image/jpeg', 'image/png', 'image/webp'], 'settings');
        if ($mosaicImages !== []) {
            $existingMosaic = [];
            if (!empty($settings['programme_mosaic_images_json'])) {
                $decoded = json_decode((string)$settings['programme_mosaic_images_json'], true);
                if (is_array($decoded)) {
                    $existingMosaic = array_values(array_filter(array_map('strval', $decoded)));
                }
            } elseif (!empty($currentSettings['programme_mosaic_images_json'])) {
                $decoded = json_decode((string)$currentSettings['programme_mosaic_images_json'], true);
                if (is_array($decoded)) {
                    $existingMosaic = array_values(array_filter(array_map('strval', $decoded)));
                }
            }
            $settings['programme_mosaic_images_json'] = json_encode(array_values(array_unique(array_merge($existingMosaic, $mosaicImages))), JSON_UNESCAPED_SLASHES);
        } elseif (($settings['programme_mosaic_images_json'] ?? '') === '' && isset($currentSettings['programme_mosaic_images_json'])) {
            $settings['programme_mosaic_images_json'] = $currentSettings['programme_mosaic_images_json'];
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
                $homeCardImage = $this->uploadFile('programme_home_card_image_file', ['image/jpeg', 'image/png', 'image/webp'], 'settings');
                if ($homeCardImage !== '') {
                    $existingHomeImages = json_decode((string)($model->getSettingValue('home_programme_images_json') ?? '[]'), true);
                    if (!is_array($existingHomeImages)) {
                        $existingHomeImages = [];
                    }
                    $existingHomeImages[$name] = $homeCardImage;
                    $model->setSettingValue('home_programme_images_json', json_encode($existingHomeImages, JSON_UNESCAPED_SLASHES));
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
                    'publish_to_portal' => isset($_POST['publish_to_portal']) ? 1 : 0,
                    'portal_announcement_text' => trim($_POST['portal_announcement_text'] ?? ''),
                ];
                $socialUpdatesEmbed = trim((string)($_POST['social_updates_embed'] ?? ''));

                try {
                    if ($isUpdate) {
                        $stmt = $pdo->prepare('UPDATE events SET title=:title, slug=:slug, starts_at=:starts_at, ends_at=:ends_at, category=:category, time_label=:time_label, location=:location, venue_type=:venue_type, registration_status=:registration_status, registration_url=:registration_url, is_featured=:is_featured, summary=:summary, body=:body, image_path=:image_path, publish_to_portal=:publish_to_portal, portal_announcement_text=:portal_announcement_text WHERE id=:id');
                        $params['id'] = $id;
                        $stmt->execute($params);
                    } else {
                        $stmt = $pdo->prepare('INSERT INTO events(title, slug, starts_at, ends_at, category, time_label, location, venue_type, registration_status, registration_url, is_featured, summary, body, image_path, publish_to_portal, portal_announcement_text, created_at) VALUES(:title, :slug, :starts_at, :ends_at, :category, :time_label, :location, :venue_type, :registration_status, :registration_url, :is_featured, :summary, :body, :image_path, :publish_to_portal, :portal_announcement_text, NOW())');
                        $stmt->execute($params);
                    }
                } catch (PDOException) {
                    flash('error', 'Events table is missing or outdated. Run the latest SQL schema for `events` in MySQL, then try again.');
                    $this->redirect('admin/list/events');
                }
                (new ContentModel($this->config))->setSettingValue('events_social_updates_html', $socialUpdatesEmbed);
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
                if (!in_array($role, ['super_admin', 'junior_admin', 'editor', 'viewer', 'registrar', 'teacher'], true) || !Auth::canManageRole($role)) {
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
                if ($stmtData['programme_id'] <= 0) {
                    flash('error', 'Please select a valid programme.');
                    $this->redirect('admin/list/portal_courses');
                }
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
                if ($stmtData['programme_id'] <= 0) {
                    flash('error', 'Please select a valid programme.');
                    $this->redirect('admin/list/programme_timetables');
                }
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
                    'marks' => (($_POST['marks'] ?? '') !== '') ? (float)$_POST['marks'] : null,
                    'grading_scheme_id' => (int)($_POST['grading_scheme_id'] ?? 0),
                    'remarks' => trim($_POST['remarks'] ?? ''),
                ];
                if ($stmtData['marks'] !== null) {
                    if ($stmtData['grading_scheme_id'] > 0) {
                        $gradeLookup = $pdo->prepare('SELECT grade_label FROM grading_schemes WHERE id = :id AND :marks BETWEEN min_score AND max_score LIMIT 1');
                        $gradeLookup->execute(['id' => $stmtData['grading_scheme_id'], 'marks' => $stmtData['marks']]);
                    } else {
                        $gradeLookup = $pdo->prepare('SELECT grade_label FROM grading_schemes WHERE :marks BETWEEN min_score AND max_score ORDER BY min_score DESC LIMIT 1');
                        $gradeLookup->execute(['marks' => $stmtData['marks']]);
                    }
                    $derivedGrade = trim((string)($gradeLookup->fetch()['grade_label'] ?? ''));
                    if ($derivedGrade !== '') {
                        $stmtData['grade'] = $derivedGrade;
                    }
                }
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE course_grades SET student_id=:student_id, course_id=:course_id, grade=:grade, marks=:marks, grading_scheme_id=:grading_scheme_id, remarks=:remarks WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO course_grades(student_id, course_id, grade, marks, grading_scheme_id, remarks, created_at) VALUES(:student_id, :course_id, :grade, :marks, :grading_scheme_id, :remarks, NOW())');
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
                if ($stmtData['course_id'] <= 0) {
                    flash('error', 'Please select a valid course.');
                    $this->redirect('admin/list/course_assignments');
                }
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
                if ($stmtData['course_id'] <= 0) {
                    flash('error', 'Please select a valid course.');
                    $this->redirect('admin/list/study_materials');
                }
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE study_materials SET course_id=:course_id, title=:title, summary=:summary, file_path=:file_path WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO study_materials(course_id, title, summary, file_path, created_at) VALUES(:course_id, :title, :summary, :file_path, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            case 'grading_schemes':
                $stmtData = [
                    'name' => trim($_POST['name'] ?? ''),
                    'grade_label' => strtoupper(trim($_POST['grade_label'] ?? '')),
                    'min_score' => (float)($_POST['min_score'] ?? 0),
                    'max_score' => (float)($_POST['max_score'] ?? 0),
                    'remarks' => trim($_POST['remarks'] ?? ''),
                ];
                if ($stmtData['name'] === '' || $stmtData['grade_label'] === '') {
                    flash('error', 'Please provide scheme name and grade label.');
                    $this->redirect('admin/list/grading_schemes');
                }
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE grading_schemes SET name=:name, grade_label=:grade_label, min_score=:min_score, max_score=:max_score, remarks=:remarks WHERE id=:id');
                    $stmtData['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO grading_schemes(name, grade_label, min_score, max_score, remarks, created_at) VALUES(:name, :grade_label, :min_score, :max_score, :remarks, NOW())');
                }
                $stmt->execute($stmtData);
                break;
            case 'testimonials':
                $name = trim($_POST['name'] ?? '');
                $course = trim($_POST['course'] ?? '');
                $tMessage = trim($_POST['message'] ?? '');
                if ($name === '' || $tMessage === '') {
                    flash('error', 'Testimonial name and message are required.');
                    $this->redirect('admin/list/testimonials');
                }
                $tImage = $this->uploadFile('image_file', ['image/jpeg', 'image/png', 'image/webp'], 'testimonials');
                $tParams = [
                    'name' => $name,
                    'course' => $course,
                    'message' => $tMessage,
                    'image_path' => $tImage !== '' ? $tImage : trim($_POST['image_path'] ?? ''),
                    'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
                    'sort_order' => (int)($_POST['sort_order'] ?? 0),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE testimonials SET name=:name, course=:course, message=:message, image_path=:image_path, is_visible=:is_visible, sort_order=:sort_order WHERE id=:id');
                    $tParams['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO testimonials(name, course, message, image_path, is_visible, sort_order, created_at) VALUES(:name, :course, :message, :image_path, :is_visible, :sort_order, NOW())');
                }
                $stmt->execute($tParams);
                break;
            case 'social_updates':
                $suContent = trim($_POST['content'] ?? '');
                if ($suContent === '') {
                    flash('error', 'Social update content is required.');
                    $this->redirect('admin/list/social_updates');
                }
                $suImage = $this->uploadFile('image_file', ['image/jpeg', 'image/png', 'image/webp'], 'social-updates');
                $suParams = [
                    'content' => $suContent,
                    'image_path' => $suImage !== '' ? $suImage : trim($_POST['image_path'] ?? ''),
                    'link_url' => trim($_POST['link_url'] ?? ''),
                    'source' => trim($_POST['source'] ?? 'general'),
                    'is_pinned' => isset($_POST['is_pinned']) ? 1 : 0,
                    'is_visible' => isset($_POST['is_visible']) ? 1 : ($isUpdate ? 0 : 1),
                ];
                if ($isUpdate) {
                    $stmt = $pdo->prepare('UPDATE social_updates SET content=:content, image_path=:image_path, link_url=:link_url, source=:source, is_pinned=:is_pinned, is_visible=:is_visible WHERE id=:id');
                    $suParams['id'] = $id;
                } else {
                    $stmt = $pdo->prepare('INSERT INTO social_updates(content, image_path, link_url, source, is_pinned, is_visible, created_at) VALUES(:content, :image_path, :link_url, :source, :is_pinned, :is_visible, NOW())');
                }
                $stmt->execute($suParams);
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
        if (!Auth::canViewEntity('media')) {
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
        $titlePrefix = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $filePaths = $this->uploadMultipleFiles('media_files', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 'media');
        if ($filePaths === []) {
            flash('error', 'Please upload a valid image file.');
            $this->redirect('admin/media');
        }

        try {
            $stmt = $pdo->prepare('INSERT INTO media_assets(title, file_path, category, created_at) VALUES(:title,:file_path,:category,NOW())');
            $uploadedCount = 0;
            foreach ($filePaths as $index => $filePath) {
                $computedTitle = $titlePrefix !== ''
                    ? ($titlePrefix . ' #' . ($index + 1))
                    : basename((string)$filePath);
                $stmt->execute([
                    'title' => $computedTitle,
                    'file_path' => $filePath,
                    'category' => $category !== '' ? $category : 'General',
                ]);
                $uploadedCount++;
            }
            flash('success', 'Media uploaded: ' . $uploadedCount . ' file(s).');
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
                $absolute = $this->resolveStoredFilePath('/' . $relative);
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
        $root = $this->webRootPath();
        $publicUploads = $root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';
        $legacyUploads = $root . DIRECTORY_SEPARATOR . 'uploads';
        if (is_dir($root . DIRECTORY_SEPARATOR . 'public')) {
            return $publicUploads;
        }
        return $legacyUploads;
    }

    private function resolveStoredFilePath(string $publicPath): string
    {
        $relative = ltrim(str_replace('/', DIRECTORY_SEPARATOR, (string)$publicPath), DIRECTORY_SEPARATOR);
        $root = $this->webRootPath();
        $publicCandidate = $root . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relative;
        if (is_file($publicCandidate)) {
            return $publicCandidate;
        }
        return $root . DIRECTORY_SEPARATOR . $relative;
    }

    private function cleanExportValue(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = str_replace("\t", ' ', $value);
        return '"' . str_replace('"', '""', $value) . '"';
    }

    private function canAccessEntity(string $entity, string $mode = 'manage'): bool
    {
        if (!in_array($entity, $this->entities, true)) {
            return false;
        }
        return $mode === 'view' ? Auth::canViewEntity($entity) : Auth::canManageEntity($entity);
    }

    private function buildFormRelations(): array
    {
        $model = new ContentModel($this->config);
        $programmes = $model->all('programmes');
        $courses = $model->all('portal_courses');
        $students = (new StudentPortalModel($this->config))->allStudents();
        $users = $model->all('users');
        $teachers = array_values(array_filter($users, static fn($user) => (string)($user['role'] ?? '') === 'teacher'));
        $gradingSchemes = $model->all('grading_schemes');
        $settings = $model->getSettings();

        return [
            'programmes' => $programmes,
            'courses' => $courses,
            'students' => $students,
            'teachers' => $teachers,
            'gradingSchemes' => $gradingSchemes,
            'siteSettings' => $settings,
        ];
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

    private function programmeHomeCardImage(string $programmeName): string
    {
        if ($programmeName === '') {
            return '';
        }
        $raw = (new ContentModel($this->config))->getSettingValue('home_programme_images_json') ?? '';
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return '';
        }
        return (string)($decoded[$programmeName] ?? '');
    }

    private function sanitizeHexColor(string $raw, string $fallback): string
    {
        $value = trim($raw);
        if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value)) {
            return $value;
        }
        return $fallback;
    }

    public function runSocialFetch(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('social_updates')) {
            $this->redirect('admin');
        }
        $fetcher = new SocialFetcher($this->config);
        $result = $fetcher->syncAll(12);
        if (!empty($result['errors'])) {
            flash('error', 'Social fetch finished with issues: ' . implode(' | ', $result['errors']));
        } else {
            $msg = sprintf('Fetched %d posts (Facebook: %d, Instagram: %d).', (int)$result['total'], (int)$result['stats']['facebook'], (int)$result['stats']['instagram']);
            flash('success', $msg);
        }
        $this->redirect('admin/list/social_updates');
    }

    public function debugSocialFetch(): void
    {
        Auth::requireAdmin();
        if (!Auth::canManageEntity('social_updates')) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Permission denied']);
            return;
        }
        header('Content-Type: application/json');
        try {
            $fetcher = new SocialFetcher($this->config);
            $diag = $fetcher->preview(5);
            echo json_encode(['ok' => true] + $diag, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode([
                'ok' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getFile() . ':' . $e->getLine(),
            ], JSON_PRETTY_PRINT);
        }
    }

    public function cronSocialFetch(): void
    {
        // Token-protected endpoint for external cron services.
        // Configure ?token=... matching the social_auto_fetch_cron_token setting.
        $model = new ContentModel($this->config);
        $settings = $model->getSettings();
        $expected = trim((string)($settings['social_auto_fetch_cron_token'] ?? ''));
        $given = trim((string)($_GET['token'] ?? ''));
        header('Content-Type: application/json');
        if ($expected === '' || !hash_equals($expected, $given)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'Invalid token']);
            return;
        }
        if (($settings['social_auto_fetch_enabled'] ?? '1') !== '1') {
            echo json_encode(['ok' => true, 'skipped' => true, 'reason' => 'auto-fetch disabled']);
            return;
        }
        $fetcher = new SocialFetcher($this->config);
        $result = $fetcher->syncAll(12);
        echo json_encode(['ok' => $result['success']] + $result);
    }
}
