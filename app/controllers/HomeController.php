<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $model = new ContentModel($this->config);
        $settings = $model->getSettings();

        $heroImages = $this->decodeList($settings['hero_images'] ?? '', [
            'https://images.unsplash.com/photo-1581595219315-a187dd40c322?w=1200',
            'https://images.unsplash.com/photo-1584982751601-97dcc096659c?w=1200',
            'https://images.unsplash.com/photo-1576671081837-49000212a370?w=1200',
            'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1200',
        ]);

        $heroCards = $this->decodeList($settings['home_value_cards'] ?? '', [
            [
                'title_primary' => 'Flexibility',
                'title_secondary' => 'That Fits You',
                'text' => 'Learn with schedules that support work, family, and personal commitments.',
                'icon' => 'bi-calendar-check',
                'cta_label' => 'Apply',
                'cta_link' => 'programmes',
            ],
            [
                'title_primary' => 'Openness',
                'title_secondary' => 'For All Learners',
                'text' => 'Accessible admissions and practical pathways for aspiring healthcare professionals.',
                'icon' => 'bi-people',
                'cta_label' => 'Explore',
                'cta_link' => 'about',
            ],
            [
                'title_primary' => 'Inclusivity',
                'title_secondary' => 'And Support',
                'text' => 'A supportive environment where diverse learners thrive and succeed.',
                'icon' => 'bi-heart-pulse',
                'cta_label' => 'Student Life',
                'cta_link' => 'about',
            ],
            [
                'title_primary' => 'Certification',
                'title_secondary' => 'With Impact',
                'text' => 'Recognized diploma and certificate programmes aligned to Kenyan industry needs.',
                'icon' => 'bi-award',
                'cta_label' => 'View Courses',
                'cta_link' => 'programmes',
            ],
        ]);

        $testimonials = $this->decodeList($settings['home_testimonials_json'] ?? '', [
            ['name' => 'Brenda W.', 'course' => 'Prospective Student', 'message' => 'The admissions team was responsive and helped me choose the right programme path.', 'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300'],
            ['name' => 'Daniel K.', 'course' => 'Current Student', 'message' => 'Course delivery is practical and the learning environment is supportive and well organized.', 'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300'],
            ['name' => 'Sharon M.', 'course' => 'Parent', 'message' => 'Clear communication and professional training standards gave us confidence in the college.', 'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300'],
            ['name' => 'Ian K.', 'course' => 'Applicant', 'message' => 'Programme information is clear and the support team gives timely guidance on requirements.', 'image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=300'],
            ['name' => 'Purity N.', 'course' => 'Current Student', 'message' => 'The timetable is practical and helps me balance learning with my other responsibilities.', 'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=300'],
            ['name' => 'Grace A.', 'course' => 'Guardian', 'message' => 'The college environment is disciplined, safe, and focused on quality healthcare training.', 'image' => 'https://images.unsplash.com/photo-1554151228-14d9def656e4?w=300'],
        ]);

        $this->view('pages/home', [
            'metaTitle' => 'Home',
            'settings' => $settings,
            'heroImages' => $heroImages,
            'heroCards' => $heroCards,
            'featuredProgrammes' => $model->getTrendingProgrammes(8),
            'news' => $model->latest('news', 3),
            'testimonials' => $testimonials,
            'events' => $model->getUpcomingEvents(4),
            'sectionVisibility' => [
                'hero' => $this->isEnabled($settings, 'show_home_hero'),
                'cards' => $this->isEnabled($settings, 'show_home_cards'),
                'banner' => $this->isEnabled($settings, 'show_home_banner'),
                'why' => $this->isEnabled($settings, 'show_home_why'),
                'courses' => $this->isEnabled($settings, 'show_home_courses'),
                'testimonials' => $this->isEnabled($settings, 'show_home_testimonials'),
                'events' => $this->isEnabled($settings, 'show_home_events'),
                'news' => $this->isEnabled($settings, 'show_home_news'),
                'cta' => $this->isEnabled($settings, 'show_home_cta'),
            ],
        ]);
    }

    private function isEnabled(array $settings, string $key, bool $default = true): bool
    {
        if (!isset($settings[$key]) || $settings[$key] === '') {
            return $default;
        }
        return in_array(strtolower((string)$settings[$key]), ['1', 'true', 'yes', 'on'], true);
    }

    private function decodeList(string $json, array $fallback): array
    {
        if ($json === '') {
            return $fallback;
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded) || $decoded === []) {
            return $fallback;
        }

        return $decoded;
    }
}
