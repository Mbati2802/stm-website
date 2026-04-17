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
            send_notification_email($notifyTo, 'New Programme Application - ' . $name, $mailBody);
        }

        $siteSettings = $model->getSettings();
        $sitePhone = trim((string)($siteSettings['phone'] ?? '')) ?: '+254 791 309011';
        $siteEmail = trim((string)($siteSettings['email'] ?? '')) ?: 'contact@stmarysmchmcollege.ac.ke';

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

        flash('success', $confirmation);
        $this->redirect('programmes/apply');
    }
}
