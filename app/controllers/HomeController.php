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

        // Pull testimonials from DB; fallback to defaults if table missing/empty
        $testimonialsRaw = $model->getTestimonials(true);
        if ($testimonialsRaw === []) {
            $testimonialsRaw = [
                ['name' => 'Brenda W.', 'course' => 'Prospective Student', 'message' => 'The admissions team was responsive and helped me choose the right programme path.', 'image_path' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300'],
                ['name' => 'Daniel K.', 'course' => 'Current Student', 'message' => 'Course delivery is practical and the learning environment is supportive and well organized.', 'image_path' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300'],
                ['name' => 'Sharon M.', 'course' => 'Parent', 'message' => 'Clear communication and professional training standards gave us confidence in the college.', 'image_path' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300'],
            ];
        }
        // Normalize keys (image vs image_path) for view consumption
        $testimonials = array_map(static function ($t) {
            return [
                'name' => (string)($t['name'] ?? ''),
                'course' => (string)($t['course'] ?? ''),
                'message' => (string)($t['message'] ?? ''),
                'image' => (string)($t['image_path'] ?? $t['image'] ?? ''),
            ];
        }, $testimonialsRaw);

        $socialUpdates = $model->getSocialUpdates(true, 24);
        $pageSnapshots = $this->buildPageSnapshots($model, $settings);

        $this->view('pages/home', [
            'metaTitle' => 'Home',
            'settings' => $settings,
            'heroImages' => $heroImages,
            'heroCards' => $heroCards,
            'featuredProgrammes' => $model->getTrendingProgrammes(8),
            'news' => $model->latest('news', 3),
            'testimonials' => $testimonials,
            'socialUpdates' => $socialUpdates,
            'pageSnapshots' => $pageSnapshots,
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
                'extra_sections' => $this->isEnabled($settings, 'show_home_extra_sections'),
                'page_snapshots' => $this->isEnabled($settings, 'show_home_page_snapshots'),
            ],
        ]);
    }

    public function testimonials(): void
    {
        $model = new ContentModel($this->config);
        $settings = $model->getSettings();
        $testimonials = $model->getTestimonials(true);
        if ($testimonials === []) {
            $testimonials = [
                ['name' => 'Brenda W.', 'course' => 'Prospective Student', 'message' => 'The admissions team was responsive and helped me choose the right programme path.', 'image_path' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300'],
                ['name' => 'Daniel K.', 'course' => 'Current Student', 'message' => 'Course delivery is practical and the learning environment is supportive and well organized.', 'image_path' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300'],
                ['name' => 'Sharon M.', 'course' => 'Parent', 'message' => 'Clear communication and professional training standards gave us confidence in the college.', 'image_path' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300'],
            ];
        }
        $testimonials = array_map(static function ($t) {
            return [
                'name' => (string)($t['name'] ?? ''),
                'course' => (string)($t['course'] ?? ''),
                'message' => (string)($t['message'] ?? ''),
                'image' => (string)($t['image_path'] ?? $t['image'] ?? ''),
            ];
        }, $testimonials);

        $this->view('pages/testimonials', [
            'metaTitle' => 'Testimonials',
            'settings' => $settings,
            'testimonials' => $testimonials,
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

    private function buildPageSnapshots(ContentModel $model, array $settings): array
    {
        $baseCards = [
            [
                'key' => 'about',
                'title' => 'About the College',
                'description' => plain_text((string)($settings['about_short_tagline'] ?? $settings['about_intro'] ?? 'Discover our mission, values, and student-focused learning approach.')),
                'link' => 'about',
                'icon' => 'bi-info-circle',
                'badge' => 'About',
                'cta' => 'Explore',
            ],
            [
                'key' => 'programmes',
                'title' => 'Programmes',
                'description' => 'Browse ' . (int)$model->countAll('programmes') . ' programmes and find the right career path for you.',
                'link' => 'programmes',
                'icon' => 'bi-journal-bookmark',
                'badge' => 'Academics',
                'cta' => 'View Programmes',
            ],
            [
                'key' => 'apply_section',
                'title' => 'How to Apply Section',
                'description' => 'View application steps, requirements, and fee/brochure guidance.',
                'link' => 'programmes/how-to-apply',
                'icon' => 'bi-pencil-square',
                'badge' => 'Admissions',
                'cta' => 'How to Apply',
            ],
            [
                'key' => 'events',
                'title' => 'Events & Activities',
                'description' => 'Stay updated on workshops, trainings, and campus activities.',
                'link' => 'events',
                'icon' => 'bi-calendar-event',
                'badge' => 'Campus',
                'cta' => 'See Events',
            ],
            [
                'key' => 'events_upcoming',
                'title' => 'Upcoming Events Section',
                'description' => 'Jump straight to upcoming events and registration opportunities.',
                'link' => 'events#upcoming',
                'icon' => 'bi-calendar2-check',
                'badge' => 'Events',
                'cta' => 'Open Upcoming',
            ],
            [
                'key' => 'social_updates',
                'title' => 'Social Updates',
                'description' => 'See recent Facebook and Instagram highlights from the college.',
                'link' => 'events#social-updates',
                'icon' => 'bi-megaphone',
                'badge' => 'Social',
                'cta' => 'View Social Feed',
            ],
            [
                'key' => 'library',
                'title' => 'Library Resources',
                'description' => 'Access learning materials and downloadable study resources.',
                'link' => 'library',
                'icon' => 'bi-book',
                'badge' => 'Resources',
                'cta' => 'Open Library',
            ],
            [
                'key' => 'departments',
                'title' => 'Departments',
                'description' => 'Explore departments and discover specialised learning paths.',
                'link' => 'departments',
                'icon' => 'bi-diagram-3',
                'badge' => 'Academics',
                'cta' => 'View Departments',
            ],
            [
                'key' => 'media',
                'title' => 'Media & News',
                'description' => 'Catch up with campus stories, updates, and highlights.',
                'link' => 'media',
                'icon' => 'bi-newspaper',
                'badge' => 'Updates',
                'cta' => 'Read Updates',
            ],
            [
                'key' => 'gallery',
                'title' => 'Campus Gallery',
                'description' => 'Browse photos from events, practical sessions, and student life.',
                'link' => 'gallery',
                'icon' => 'bi-images',
                'badge' => 'Gallery',
                'cta' => 'Open Gallery',
            ],
            [
                'key' => 'testimonials',
                'title' => 'Testimonials',
                'description' => 'Read student and parent experiences from our community.',
                'link' => 'testimonials',
                'icon' => 'bi-chat-quote',
                'badge' => 'Voices',
                'cta' => 'View Testimonials',
            ],
            [
                'key' => 'faqs',
                'title' => 'FAQs',
                'description' => 'Get quick answers to common admissions and campus questions.',
                'link' => 'faqs',
                'icon' => 'bi-question-circle',
                'badge' => 'Help',
                'cta' => 'Read FAQs',
            ],
            [
                'key' => 'principal',
                'title' => 'Principal’s Office',
                'description' => 'Read leadership insights and institutional direction from the principal.',
                'link' => 'principal',
                'icon' => 'bi-person-badge',
                'badge' => 'Leadership',
                'cta' => 'Read Message',
            ],
            [
                'key' => 'registrar',
                'title' => 'Registrar Desk',
                'description' => 'Get guidance on admissions, registration, and academic records.',
                'link' => 'registrar',
                'icon' => 'bi-folder-check',
                'badge' => 'Admissions',
                'cta' => 'Visit Registrar',
            ],
            [
                'key' => 'uniqueness',
                'title' => 'What Makes Us Unique',
                'description' => 'See what distinguishes our training model and student support.',
                'link' => 'uniqueness',
                'icon' => 'bi-stars',
                'badge' => 'Why Us',
                'cta' => 'Explore',
            ],
            [
                'key' => 'portal',
                'title' => 'Student Portal',
                'description' => 'Log in to access grades, assignments, timetable, and support.',
                'link' => 'portal/login',
                'icon' => 'bi-person-workspace',
                'badge' => 'Portal',
                'cta' => 'Open Portal',
            ],
            [
                'key' => 'contact',
                'title' => 'Contact Us',
                'description' => 'Speak with admissions or support and get guidance quickly.',
                'link' => 'contact',
                'icon' => 'bi-envelope-open',
                'badge' => 'Support',
                'cta' => 'Contact',
            ],
            [
                'key' => 'contact_admissions',
                'title' => 'Admissions Contact',
                'description' => 'Reach admissions directly for course guidance and enrollment support.',
                'link' => 'contact-admissions',
                'icon' => 'bi-telephone-forward',
                'badge' => 'Admissions',
                'cta' => 'Contact Admissions',
            ],
            [
                'key' => 'contact_registrar',
                'title' => 'Registrar Contact',
                'description' => 'Get help from the registrar’s office on application and records.',
                'link' => 'contact-registrar',
                'icon' => 'bi-telephone',
                'badge' => 'Registrar',
                'cta' => 'Contact Registrar',
            ],
            [
                'key' => 'portals',
                'title' => 'All Portals',
                'description' => 'Jump to student and staff portal entry points in one place.',
                'link' => 'portals',
                'icon' => 'bi-box-arrow-in-right',
                'badge' => 'Access',
                'cta' => 'Open Portals',
            ],
        ];

        $rawOverrides = (string)($settings['home_page_snapshots_json'] ?? '');
        $cards = $baseCards;
        if (trim($rawOverrides) !== '') {
            $overrides = json_decode($rawOverrides, true);
            if (is_array($overrides)) {
                $normalized = [];
                foreach ($overrides as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $title = trim((string)($item['title'] ?? ''));
                    $description = trim((string)($item['description'] ?? ''));
                    $link = trim((string)($item['link'] ?? ''));
                    if ($title === '' || $link === '') {
                        continue;
                    }
                    $normalized[] = [
                        'key' => trim((string)($item['key'] ?? slugify($title))),
                        'title' => $title,
                        'description' => $description !== '' ? $description : 'Learn more about this section.',
                        'link' => $link,
                        'icon' => trim((string)($item['icon'] ?? 'bi-link-45deg')) ?: 'bi-link-45deg',
                        'badge' => trim((string)($item['badge'] ?? 'Explore')) ?: 'Explore',
                        'cta' => trim((string)($item['cta'] ?? 'Open')) ?: 'Open',
                    ];
                }
                if ($normalized !== []) {
                    $cards = $normalized;
                }
            }
        }

        $layout = json_decode((string)($settings['home_page_snapshots_layout_json'] ?? ''), true);
        if (!is_array($layout) || $layout === []) {
            return $cards;
        }

        $byKey = [];
        foreach ($cards as $card) {
            $key = trim((string)($card['key'] ?? ''));
            if ($key === '') {
                $key = slugify((string)($card['title'] ?? 'card'));
            }
            $card['key'] = $key;
            $byKey[$key] = $card;
        }

        $ordered = [];
        foreach ($layout as $item) {
            if (!is_array($item)) {
                continue;
            }
            $key = trim((string)($item['key'] ?? ''));
            if ($key === '' || !isset($byKey[$key])) {
                continue;
            }
            $enabled = !isset($item['enabled']) || in_array(strtolower((string)$item['enabled']), ['1', 'true', 'yes', 'on'], true);
            if (!$enabled) {
                continue;
            }
            $ordered[] = $byKey[$key];
            unset($byKey[$key]);
        }

        foreach ($byKey as $remaining) {
            $ordered[] = $remaining;
        }

        return $ordered;
    }
}
