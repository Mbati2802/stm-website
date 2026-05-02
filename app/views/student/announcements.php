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
            
            <?php if ($announcements === []): ?>
                <p class="text-muted mb-0">No announcements yet.</p>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                <div class="alert-student alert-info">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong><?= e((string)$announcement['title']) ?></strong>
                            <p class="mb-1 small mt-1"><?= e((string)$announcement['body']) ?></p>
                            <small class="text-white-50"><i class="bi bi-clock me-1"></i><?= e((string)$announcement['created_at']) ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
