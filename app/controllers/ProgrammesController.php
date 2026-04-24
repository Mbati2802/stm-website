<?php
class ProgrammesController extends Controller
{
    private const CATEGORIES = ['Diploma', 'Certificate', 'Artisan', 'Short Course'];

    public function index(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_programmes')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $type = trim($_GET['category'] ?? '');
        $search = trim($_GET['search'] ?? '');
        $programmes = $model->getProgrammes($type ?: null, $search ?: null);

        $grouped = [];
        foreach (self::CATEGORIES as $category) {
            $grouped[$category] = array_values(array_filter(
                $programmes,
                fn($programme) => strcasecmp((string)($programme['category'] ?? ''), $category) === 0
            ));
        }

        $this->view('pages/programmes', [
            'metaTitle' => 'Programmes',
            'programmes' => $programmes,
            'groupedProgrammes' => $grouped,
            'type' => $type,
            'search' => $search,
            'categories' => self::CATEGORIES,
            'departments' => $model->all('departments'),
        ]);
    }

    public function show(string $slug): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_programmes')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }

        $programme = $model->getProgrammeBySlug($slug);
        if ($programme === null) {
            http_response_code(404);
            echo 'Programme not found.';
            return;
        }

        $otherProgrammes = array_values(array_filter(
            $model->getProgrammes(),
            fn($row) => (int)$row['id'] !== (int)$programme['id']
        ));
        $settings = $model->getSettings();
        $programmeContent = $model->getProgrammeContentForView($programme);
        $model->incrementProgrammeMetric((string)($programme['slug'] ?? ''), 'views');

        $this->view('pages/programme_details', [
            'metaTitle' => $programme['name'],
            'programme' => $programme,
            'otherProgrammes' => array_slice($otherProgrammes, 0, 8),
            'settings' => $settings,
            'currentIntake' => $settings['current_intake'] ?? 'January',
            'programmeContent' => $programmeContent,
        ]);
    }

    public function applyForm(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_programmes')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }

        $selectedCourse = trim($_GET['course'] ?? '');
        $selectedLevel = trim($_GET['level'] ?? '');
        $programmes = $model->getProgrammes();
        $validCourseNames = array_column($programmes, 'name');

        if ($selectedCourse !== '' && !in_array($selectedCourse, $validCourseNames, true)) {
            $selectedCourse = '';
        }
        $settings = $model->getSettings();

        $this->view('pages/programme_apply', [
            'metaTitle' => 'Apply Now',
            'programmes' => $programmes,
            'categories' => self::CATEGORIES,
            'selectedCourse' => $selectedCourse,
            'selectedLevel' => in_array($selectedLevel, self::CATEGORIES, true) ? $selectedLevel : '',
            'currentIntake' => $settings['current_intake'] ?? 'January',
            'siteSettings' => $settings,
        ]);
    }

    public function howToApply(): void
    {
        $model = new ContentModel($this->config);
        if (!$model->isEnabled('show_page_programmes')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }

        $this->view('pages/how_to_apply', [
            'metaTitle' => 'How to Apply',
            'settings' => $model->getSettings(),
        ]);
    }

    public function submitApplication(): void
    {
        $model = new ContentModel($this->config);

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $guardianName = trim($_POST['guardian_name'] ?? '');
        $guardianPhone = trim($_POST['guardian_phone'] ?? '');
        $county = trim($_POST['county'] ?? '');
        $course = trim($_POST['course_selection'] ?? '');
        $grade = trim($_POST['grade'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $intake = trim($_POST['preferred_intake'] ?? '');
        $referral = trim($_POST['referral_source'] ?? '');
        $courseSlug = slugify($course);

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $course === '') {
            flash('error', 'Please complete all required application fields correctly.');
            $this->redirect('programmes/apply');
        }

        $message = implode("\n", [
            'New programme application submitted:',
            'Guardian Name: ' . $guardianName,
            'Guardian Phone: ' . $guardianPhone,
            'County: ' . $county,
            'Course Selection: ' . $course,
            'Grade: ' . $grade,
            'Level: ' . $level,
            'Preferred Intake: ' . $intake,
            'Heard About Us Via: ' . $referral,
        ]);

        try {
            $model->saveMessage([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => 'Programme Application',
                'message' => $message,
            ]);
            $model->saveProgrammeApplication([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'guardian_name' => $guardianName,
                'guardian_phone' => $guardianPhone,
                'county' => $county,
                'course_selection' => $course,
                'grade' => $grade,
                'level' => $level,
                'preferred_intake' => $intake,
                'referral_source' => $referral,
            ]);
            $model->incrementProgrammeMetric($courseSlug, 'applications');
        } catch (Throwable) {
            flash('error', 'Application service is temporarily unavailable. Please try again shortly.');
            $this->redirect('programmes/apply');
        }

        $notifyTo = trim((string)($this->config['application_notification_email'] ?? ($this->config['notification_email'] ?? 'admission@stmarysmchmcollege.ac.ke')));
        if ($notifyTo !== '') {
            $mailBody = implode("\n", [
                'A new programme application was submitted.',
                'Name: ' . $name,
                'Email: ' . $email,
                'Phone: ' . $phone,
                'Course: ' . $course,
                'Level: ' . $level,
                'Preferred Intake: ' . $intake,
                '',
                'Application Details:',
                $message,
            ]);
            $mailHtml = build_structured_notification_email('New Programme Application', [
                'Applicant Name' => $name,
                'Email' => $email,
                'Phone' => $phone,
                'Guardian Name' => $guardianName,
                'Guardian Phone' => $guardianPhone,
                'County' => $county,
                'Course' => $course,
                'Grade' => $grade,
                'Level' => $level,
                'Preferred Intake' => $intake,
                'Referral Source' => $referral,
            ]);
            send_notification_email($notifyTo, 'New Programme Application - ' . $name, $mailBody, $mailHtml);
        }

        $siteSettings = $model->getSettings();
        $sitePhone = trim((string)($siteSettings['phone'] ?? '')) ?: '+254 791 309011';
        $siteEmail = trim((string)($siteSettings['email'] ?? '')) ?: 'admission@stmarysmchmcollege.ac.ke';

        $defaultConfirmation = implode("\n\n", [
            'Application Received Successfully',
            "Thank you for applying to St. Mary\u{2019}s Mother and Child Hospital Medical Training College.",
            'We have received your application, and our admissions team is currently reviewing your details. You have taken an important step toward building a meaningful and rewarding career in the healthcare field—and we are excited to be part of your journey.',
            'Our team will contact you soon with the next steps regarding your application. In the meantime, feel free to reach out if you have any questions or need further assistance.',
            'We look forward to welcoming you to our community of passionate and dedicated healthcare professionals.',
            '📞 Contact Us: {PHONE}',
            '📩 Email: {EMAIL}',
            '',
            'Your future in healthcare starts here.',
        ]);

        $template = trim((string)($siteSettings['application_confirmation_message'] ?? ''));
        if ($template === '') {
            $template = $defaultConfirmation;
        }
        $confirmation = strtr($template, [
            '{PHONE}' => $sitePhone,
            '{EMAIL}' => $siteEmail,
        ]);

        $isHtmlTemplate = str_contains($confirmation, '<') && str_contains($confirmation, '>');
        $plainConfirmation = $isHtmlTemplate ? plain_text_multiline($confirmation) : $confirmation;
        $htmlConfirmation = $isHtmlTemplate
            ? $confirmation
            : (
                '<!doctype html><html><body style="margin:0;padding:0;background:#f5f7fb;">' .
                '<div style="max-width:640px;margin:0 auto;padding:24px;">' .
                '<div style="background:#ffffff;border:1px solid #e9eef5;border-radius:14px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">' .
                '<div style="padding:22px 22px 12px;background:linear-gradient(135deg,#0d6efd 0%,#1b8cff 100%);color:#fff;">' .
                '<div style="font-size:18px;font-weight:700;letter-spacing:.2px;">Application Received Successfully</div>' .
                '<div style="margin-top:6px;font-size:13px;opacity:.95;">St. Mary’s Mother and Child Hospital Medical Training College</div>' .
                '</div>' .
                '<div style="padding:22px;color:#1f2a37;line-height:1.55;font-size:14px;">' .
                '<p style="margin:0 0 12px;">Thank you for applying to <strong>St. Mary’s Mother and Child Hospital Medical Training College</strong>.</p>' .
                '<p style="margin:0 0 12px;">We have received your application, and our admissions team is currently reviewing your details. You have taken an important step toward building a meaningful and rewarding career in the healthcare field—and we are excited to be part of your journey.</p>' .
                '<p style="margin:0 0 12px;">Our team will contact you soon with the next steps regarding your application. In the meantime, feel free to reach out if you have any questions or need further assistance.</p>' .
                '<p style="margin:0 0 16px;">We look forward to welcoming you to our community of passionate and dedicated healthcare professionals.</p>' .
                '<div style="padding:14px 16px;background:#f8fafc;border:1px solid #eef2f7;border-radius:12px;">' .
                '<div style="font-weight:700;margin-bottom:8px;">Contact Us</div>' .
                '<div style="margin:0 0 6px;">📞 <strong>Phone:</strong> ' . e($sitePhone) . '</div>' .
                '<div style="margin:0;">📩 <strong>Email:</strong> ' . e($siteEmail) . '</div>' .
                '</div>' .
                '<p style="margin:16px 0 0;font-weight:700;">Your future in healthcare starts here.</p>' .
                '</div>' .
                '<div style="padding:14px 22px;background:#ffffff;border-top:1px solid #eef2f7;color:#6b7280;font-size:12px;">' .
                'This is an automated confirmation. If you have questions, reply to this email.' .
                '</div>' .
                '</div>' .
                '</div></body></html>'
            );

        // Email the applicant (no PDF). UI only shows a short confirmation.
        send_notification_email(
            $email,
            'Application Received Successfully',
            $plainConfirmation,
            $htmlConfirmation
        );

        flash('success', 'Application received successfully. Thank you for applying — our admissions team will contact you soon.');
        $this->redirect('programmes/apply');
    }
}
