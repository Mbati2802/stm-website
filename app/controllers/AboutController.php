<?php
class AboutController extends Controller
{
    private ContentModel $model;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->model = new ContentModel($config);
    }

    public function about(): void
    {
        if (!$this->model->isEnabled('show_page_about')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $this->view('pages/about', [
            'metaTitle' => 'About Us',
            'page' => $this->model->page('about'),
            'settings' => $this->model->getSettings(),
        ]);
    }

    public function principal(): void
    {
        if (!$this->model->isEnabled('show_page_principal')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $this->view('pages/principal', [
            'metaTitle' => 'The Principal',
            'page' => $this->model->page('principal'),
            'settings' => $this->model->getSettings(),
        ]);
    }

    public function registrar(): void
    {
        $this->view('pages/registrar', ['metaTitle' => 'Registrar', 'page' => $this->model->page('registrar'), 'settings' => $this->model->getSettings()]);
    }

    public function uniqueness(): void
    {
        $settings = $this->model->getSettings();
        $whyItems = array_filter(array_map('trim', explode('|', (string)($settings['about_differentiators'] ?? 'Practical-Based Learning|Market-Driven Courses|Supportive Learning Environment|Affordable & Accessible Education'))));
        $this->view('pages/uniqueness', [
            'metaTitle' => 'College Uniqueness',
            'settings' => $settings,
            'whyItems' => $whyItems,
        ]);
    }

    public function contact(): void
    {
        if (!$this->model->isEnabled('show_page_contact')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $this->view('pages/contact', ['metaTitle' => 'Contact Us', 'settings' => $this->model->getSettings()]);
    }

    public function submitContact(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? 'General Enquiry');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
            flash('error', 'Please provide valid contact details and message.');
            $this->redirect('contact');
        }

        try {
            $this->model->saveMessage(compact('name', 'email', 'phone', 'subject', 'message'));
        } catch (Throwable) {
            flash('error', 'Contact service is temporarily unavailable. Please try again shortly.');
            $this->redirect('contact');
        }

        $notifyTo = trim((string)($this->config['contact_notification_email'] ?? ($this->config['notification_email'] ?? 'contact@stmarysmchmcollege.ac.ke')));
        if ($notifyTo !== '') {
            $mailBody = implode("\n", [
                'A new contact form message was submitted.',
                'Name: ' . $name,
                'Email: ' . $email,
                'Phone: ' . $phone,
                'Subject: ' . $subject,
                'Message:',
                $message,
            ]);
            send_notification_email($notifyTo, 'New Contact Message - ' . $subject, $mailBody);
        }

        flash('success', 'Message submitted successfully. Our admissions team will contact you.');
        $this->redirect('contact');
    }

    public function faqs(): void
    {
        if (!$this->model->isEnabled('show_page_faqs')) {
            http_response_code(404);
            echo 'Page not available.';
            return;
        }
        $this->view('pages/faqs', ['metaTitle' => 'FAQs', 'faqs' => $this->model->faqs()]);
    }
}
