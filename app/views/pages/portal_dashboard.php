<?php
$studentName = (string)($student['name'] ?? ($_SESSION['student_name'] ?? 'Student'));
$admissionNumber = (string)($student['admission_number'] ?? ($_SESSION['student_admission_number'] ?? 'Not assigned'));
?>
<section class="hero-ou-wrap">
    <div class="site-width hero-boxed">
        <div class="boxed-section" style="background:linear-gradient(120deg, var(--primary), var(--secondary)); color:#fff;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="split-title mb-2">
                        <span class="title-primary text-white">Welcome</span> |
                        <span class="title-secondary text-white"><?= e($studentName) ?></span>
                    </h1>
                    <p class="mb-0 text-white-50">Student Portal dashboard for timetables, announcements, and updates.</p>
                    <p class="mb-0 text-white-50 small">Admission Number: <?= e($admissionNumber) ?></p>
                </div>
                <a class="btn btn-light" href="<?= e(base_url('portal/logout')) ?>">Logout</a>
            </div>
        </div>
    </div>
</section>

<section class="section-stack">
    <div class="site-width boxed-section">
        <div class="row g-4">
            <div class="col-lg-7">
                <h2 class="h5 mb-3">Announcements</h2>
                <div class="soft-card p-4 bg-white">
                    <?php if ($announcements === []): ?>
                        <p class="mb-0 text-muted">No announcements yet.</p>
                    <?php else: ?>
                        <?php foreach ($announcements as $item): ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <h3 class="h6 mb-1"><?= e((string)$item['title']) ?></h3>
                                <p class="small text-muted mb-1"><?= e((string)$item['created_at']) ?></p>
                                <p class="mb-0"><?= e((string)$item['body']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <h2 class="h5 mb-3">Timetables</h2>
                <div class="soft-card p-4 bg-white">
                    <?php if ($timetables === []): ?>
                        <p class="mb-0 text-muted">No timetable uploads yet.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($timetables as $item): ?>
                                <li class="mb-3 pb-3 border-bottom">
                                    <strong><?= e((string)$item['title']) ?></strong>
                                    <p class="small text-muted mb-1"><?= e((string)$item['created_at']) ?></p>
                                    <?php if (trim((string)($item['file_path'] ?? '')) !== ''): ?>
                                        <a href="<?= e(base_url(ltrim((string)$item['file_path'], '/'))) ?>" target="_blank" rel="noopener">Open timetable</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="soft-card p-4 mt-3 bg-white">
                    <h3 class="h6 text-uppercase text-muted mb-3">More Features</h3>
                    <ul class="mb-0">
                        <li>Application status tracking (coming soon)</li>
                        <li>Fee statements and balances (coming soon)</li>
                        <li>Course materials and downloads (coming soon)</li>
                        <li>Exam results and registration notices (coming soon)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
