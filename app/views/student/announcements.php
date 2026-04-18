<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-megaphone me-2"></i>Announcements</h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>All Types</option>
                        <option>Academic</option>
                        <option>General</option>
                        <option>Emergency</option>
                    </select>
                </div>
            </div>
            
            <?php 
            $announcements = [
                ['Schedule Update', 'Tomorrow\'s CS301 lecture moved to Room 205', 'Jan 18, 2024', 'info', true],
                ['Library Maintenance', 'Digital library will be unavailable this weekend for system upgrades', 'Jan 17, 2024', 'warning', true],
                ['Exam Schedule Released', 'Mid-term examination schedule for Semester 2 has been released', 'Jan 16, 2024', 'success', false],
                ['Holiday Notice', 'College closed on January 26th for Republic Day', 'Jan 15, 2024', 'info', false],
                ['Scholarship Opportunity', 'New merit-based scholarships available for deserving students', 'Jan 14, 2024', 'success', false],
                ['Campus Security', 'Enhanced security measures implemented across campus', 'Jan 13, 2024', 'warning', false],
            ];
            foreach ($announcements as $announcement): ?>
            <div class="alert-student alert-<?= $announcement[3] ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= $announcement[0] ?></strong>
                        <p class="mb-1 small mt-1"><?= $announcement[1] ?></p>
                        <small class="text-white-50"><i class="bi bi-clock me-1"></i><?= $announcement[2] ?></small>
                    </div>
                    <?php if ($announcement[4]): ?>
                        <span class="badge bg-white text-<?= $announcement[3] ?>">New</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
