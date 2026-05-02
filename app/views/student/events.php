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
            
            <?php if ($events === []): ?>
                <p class="text-muted mb-0">No events yet.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($events as $event): ?>
                    <?php
                    $startsAt = (string)($event['starts_at'] ?? '');
                    $startTs = $startsAt !== '' ? strtotime($startsAt) : null;
                    $isUpcoming = $startTs && $startTs > time();
                    $dateStr = $startTs ? date('M j, Y', $startTs) : '';
                    $location = trim((string)($event['location'] ?? ''));
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="unit-card">
                            <div class="unit-header">
                                <div>
                                    <h5 class="unit-title"><?= e((string)($event['title'] ?? '')) ?></h5>
                                    <div class="unit-code"><i class="bi bi-calendar me-1"></i><?= e($dateStr) ?></div>
                                    <div class="unit-instructor"><i class="bi bi-geo-alt me-1"></i><?= e($location) ?></div>
                                </div>
                                <span class="badge bg-<?= $isUpcoming ? 'success' : 'secondary' ?> rounded-pill"><?= $isUpcoming ? 'Upcoming' : 'Past' ?></span>
                            </div>
                            <p class="small text-muted mb-3"><?= e(substr((string)($event['summary'] ?? (string)($event['body'] ?? '')), 0, 80)) ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-action-view" title="View Details"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
