<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-calendar-event me-2"></i>Campus Events</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Events</option>
                        <option>Upcoming</option>
                        <option>Past</option>
                    </select>
                    <button class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Create Event</button>
                </div>
            </div>
            
            <div class="row g-3">
                <?php 
                $events = [
                    ['Tech Symposium 2024', 'Jan 28, 2024', 'Main Auditorium', 'upcoming', 'Join us for the annual technology symposium featuring industry experts and innovative projects.'],
                    ['Career Fair', 'Feb 5, 2024', 'Sports Complex', 'upcoming', 'Meet leading companies and explore internship and job opportunities.'],
                    ['Sports Day', 'Feb 15, 2024', 'College Grounds', 'upcoming', 'Annual sports competition with various athletic events.'],
                    ['Cultural Festival', 'Mar 1, 2024', 'Main Campus', 'upcoming', 'Celebrate diversity with music, dance, and cultural performances.'],
                    ['Hackathon', 'Jan 10, 2024', 'Lab Building', 'past', '24-hour coding challenge with exciting prizes.'],
                    ['Workshop: AI in Education', 'Jan 5, 2024', 'Room 305', 'past', 'Interactive workshop on artificial intelligence applications in education.'],
                ];
                foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h5 class="course-title"><?= $event[0] ?></h5>
                                <div class="course-code"><i class="bi bi-calendar me-1"></i><?= $event[1] ?></div>
                                <div class="course-instructor"><i class="bi bi-geo-alt me-1"></i><?= $event[2] ?></div>
                            </div>
                            <span class="badge bg-<?= $event[3] === 'upcoming' ? 'success' : 'secondary' ?> rounded-pill"><?= $event[3] === 'upcoming' ? 'Upcoming' : 'Past' ?></span>
                        </div>
                        <p class="small text-muted mb-3"><?= substr($event[4], 0, 80) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                <i class="bi bi-people me-1"></i><?= 50 + ($event[0] === 'Tech Symposium 2024' ? 200 : 50) ?> attendees
                            </div>
                            <div class="action-buttons">
                                <?php if ($event[3] === 'upcoming'): ?>
                                    <button class="btn btn-sm btn-primary">Register</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-action-view" title="View Details"><i class="bi bi-eye"></i></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
