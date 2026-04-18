<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-calendar-week me-2"></i>Weekly Timetable</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left"></i></button>
                    <span class="btn btn-sm btn-primary">Week 3</span>
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
            
            <div class="table-responsive admin-table-card">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $timetable = [
                            ['08:00 - 09:00', 'CS301 - Database Systems', 'CS302 - Algorithms', 'CS301 - Database Systems', 'CS302 - Algorithms', 'CS303 - Web Technologies'],
                            ['09:00 - 10:00', 'CS303 - Web Technologies', 'CS304 - Software Engineering', 'CS303 - Web Technologies', 'CS304 - Software Engineering', 'Break'],
                            ['10:00 - 11:00', 'Break', 'CS305 - Computer Networks', 'Break', 'CS305 - Computer Networks', 'CS301 - Database Systems'],
                            ['11:00 - 12:00', 'CS304 - Software Engineering', 'Break', 'CS304 - Software Engineering', 'Break', 'CS302 - Algorithms'],
                            ['12:00 - 01:00', 'Lunch Break', 'Lunch Break', 'Lunch Break', 'Lunch Break', 'Lunch Break'],
                            ['01:00 - 02:00', 'CS305 - Computer Networks', 'CS306 - Operating Systems', 'CS305 - Computer Networks', 'CS306 - Operating Systems', 'CS304 - Software Engineering'],
                            ['02:00 - 03:00', 'CS306 - Operating Systems', 'CS301 - Database Systems', 'CS306 - Operating Systems', 'CS301 - Database Systems', 'Break'],
                            ['03:00 - 04:00', 'Lab Session', 'Lab Session', 'Lab Session', 'Lab Session', 'Lab Session'],
                        ];
                        foreach ($timetable as $slot): ?>
                        <tr>
                            <td><strong><?= $slot[0] ?></strong></td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <td>
                                <?php if ($slot[$i] === 'Break' || $slot[$i] === 'Lunch Break'): ?>
                                    <span class="badge bg-secondary"><?= $slot[$i] ?></span>
                                <?php elseif ($slot[$i] === 'Lab Session'): ?>
                                    <span class="badge bg-info"><?= $slot[$i] ?></span>
                                <?php else: ?>
                                    <small><?= $slot[$i] ?></small>
                                <?php endif; ?>
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="student-card mt-4">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-calendar-day me-2"></i>Today's Schedule - Monday</h4>
            </div>
            <?php 
            $todaySchedule = [
                ['08:00 - 09:00', 'CS301 - Database Systems', 'Room 205'],
                ['09:00 - 10:00', 'CS303 - Web Technologies', 'Lab 1'],
                ['10:00 - 11:00', 'Break', '-'],
                ['11:00 - 12:00', 'CS304 - Software Engineering', 'Room 301'],
                ['12:00 - 01:00', 'Lunch Break', '-'],
                ['01:00 - 02:00', 'CS305 - Computer Networks', 'Room 402'],
                ['02:00 - 03:00', 'CS306 - Operating Systems', 'Lab 2'],
                ['03:00 - 04:00', 'Lab Session', 'Lab 1'],
            ];
            foreach ($todaySchedule as $session): ?>
            <div class="d-flex align-items-center border-bottom py-2">
                <div class="flex-shrink-0" style="width: 120px;">
                    <strong><?= $session[0] ?></strong>
                </div>
                <div class="flex-grow-1">
                    <div><?= $session[1] ?></div>
                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= $session[2] ?></small>
                </div>
                <div class="flex-shrink-0">
                    <?php if ($session[1] !== 'Break' && $session[1] !== 'Lunch Break'): ?>
                        <button class="btn btn-sm btn-action-view" title="View Details"><i class="bi bi-eye"></i></button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
