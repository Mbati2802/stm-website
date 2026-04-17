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
        $principalName = trim((string)($siteSettings['principal_name'] ?? 'The Principal'));
        $principalMessage = trim(plain_text((string)($siteSettings['principal_message'] ?? 'Welcome to St. Mary\'s College of Health Sciences.')));
        $applicantBody = implode("\n", [
            'Congratulations ' . $name . ',',
            '',
            'Thank you for applying to St. Mary\'s College of Health Sciences.',
            'We have received your application and attached your interim admission letter.',
            '',
            'Our admissions office will contact you with the next steps.',
            '',
            'Admissions Office',
            'St. Mary\'s College of Health Sciences',
        ]);

        $letterLines = [
            'ST. MARY\'S COLLEGE OF HEALTH SCIENCES',
            'P.O BOX 1666-20117, NAIVASHA',
            '',
            'INTERIM ADMISSION LETTER',
            'Ref: STM/ADM/' . date('Y') . '/' . strtoupper(substr(md5($email . $phone . $name), 0, 6)),
            'Date: ' . date('d M Y'),
            '',
            'Applicant Details',
            'Name: ' . $name,
            'Email: ' . $email,
            'Phone: ' . $phone,
            'County: ' . $county,
            '',
            'Dear ' . $name . ',',
            'Congratulations on your application to pursue ' . $course . '.',
            'You have been issued this interim admission letter pending final admission processing.',
            'Please keep this letter for reference as we complete your admission workflow.',
            '',
            'Principal\'s Message:',
            $principalMessage,
            '',
            'Signed:',
            $principalName,
            'Principal',
        ];
        $pdf = generate_simple_pdf($letterLines);
        $sentApplicantEmail = send_notification_email_with_attachment(
            $email,
            'Application Received - Interim Admission Letter',
            $applicantBody,
            'interim_admission_letter.pdf',
            $pdf,
            'application/pdf'
        );

        if (!$sentApplicantEmail) {
            flash('error', 'Application received, but confirmation email could not be sent right now. Admissions will still contact you.');
            $this->redirect('programmes');
        }

        flash('success', 'Your application has been submitted successfully.');
        $this->redirect('programmes');
    }
}
