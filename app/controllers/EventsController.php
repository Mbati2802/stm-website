<?php
class EventsController extends Controller
{
    public function index(): void
    {
        $model = new ContentModel($this->config);
        $settings = $model->getSettings();

        $upcoming = $model->getUpcomingEvents(30);
        $featured = $model->getFeaturedEvent();
        $pastGallery = array_slice(array_values(array_filter($model->all('gallery'), fn($row) => ($row['category'] ?? '') === 'Events')), 0, 18);

        $this->view('pages/events', [
            'metaTitle' => 'Upcoming Events & Activities',
            'settings' => $settings,
            'featured' => $featured,
            'upcoming' => $upcoming,
            'pastGallery' => $pastGallery,
            'announcementsHtml' => (string)($settings['events_announcements'] ?? ''),
            'socialUpdatesHtml' => (string)($settings['events_social_updates_html'] ?? ''),
            'socialUpdates' => $model->getSocialUpdates(true, 12),
            'socialUpdatesTitle' => trim((string)($settings['social_updates_title'] ?? '')) !== '' ? (string)$settings['social_updates_title'] : 'Social Updates',
        ]);
    }

    public function show(string $slug): void
    {
        $model = new ContentModel($this->config);
        $event = $model->getEventBySlug($slug);
        if (!$event) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $this->view('pages/event_details', [
            'metaTitle' => $event['title'] ?? 'Event',
            'event' => $event,
            'settings' => $model->getSettings(),
        ]);
    }

    public function registerForm(string $slug): void
    {
        $model = new ContentModel($this->config);
        $event = $model->getEventBySlug($slug);
        if (!$event) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $this->view('pages/event_register', [
            'metaTitle' => 'Register',
            'event' => $event,
            'settings' => $model->getSettings(),
        ]);
    }

    public function submitRegistration(string $slug): void
    {
        $model = new ContentModel($this->config);
        $event = $model->getEventBySlug($slug);
        if (!$event) {
            http_response_code(404);
            echo 'Event not found';
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $isStudent = (int)($_POST['is_student'] ?? 1);
        $notes = trim($_POST['notes'] ?? '');

        $_SESSION['old'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'notes' => $notes,
        ];

        if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please fill in your name, phone, and a valid email.');
            $this->redirect('events/' . $slug . '/register');
        }

        $pdo = Database::getInstance($this->config['db']);
        try {
            $stmt = $pdo->prepare('INSERT INTO event_registrations(event_id, name, email, phone, is_student, notes, created_at) VALUES(:event_id, :name, :email, :phone, :is_student, :notes, NOW())');
            $stmt->execute([
                'event_id' => (int)($event['id'] ?? 0),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'is_student' => $isStudent ? 1 : 0,
                'notes' => $notes,
            ]);
        } catch (PDOException) {
            flash('error', 'Registration database table is missing. Create the `event_registrations` table in MySQL then try again.');
            $this->redirect('events/' . $slug . '/register');
        }

        unset($_SESSION['old']);
        $notifyTo = trim((string)($this->config['event_notification_email'] ?? ($this->config['notification_email'] ?? 'contact@stmarysmchmcollege.ac.ke')));
        if ($notifyTo !== '') {
            $mailBody = implode("\n", [
                'A new event registration was submitted.',
                'Event: ' . (string)($event['title'] ?? 'Event'),
                'Name: ' . $name,
                'Email: ' . $email,
                'Phone: ' . $phone,
                'Is Student: ' . ($isStudent ? 'Yes' : 'No'),
                'Notes:',
                $notes,
            ]);
            $mailHtml = build_structured_notification_email('New Event Registration', [
                'Event' => (string)($event['title'] ?? 'Event'),
                'Registrant Name' => $name,
                'Email' => $email,
                'Phone' => $phone,
                'Is Student' => $isStudent ? 'Yes' : 'No',
                'Notes' => $notes,
            ]);
            send_notification_email($notifyTo, 'Event Registration - ' . (string)($event['title'] ?? 'Event'), $mailBody, $mailHtml);
        }
        flash('success', 'Registration received. We will contact you with confirmation details.');
        $this->redirect('events/' . $slug);
    }
}

