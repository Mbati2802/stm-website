<section class="py-4">
    <div class="student-content-wrap">
        <div class="student-card">
            <div class="student-card-header">
                <h4 class="student-card-title"><i class="bi bi-people me-2"></i>Clubs & Societies</h4>
                <button class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Join New Club</button>
            </div>
            
            <div class="row g-3">
                <?php 
                $clubs = [
                    ['Programming Club', 'Tech enthusiasts coding together', 'bi-code-slash', true, 45],
                    ['Debate Society', 'Sharpen your public speaking skills', 'bi-chat-quote', true, 32],
                    ['Sports Club', 'Various sports and fitness activities', 'bi-trophy', true, 68],
                    ['Music Society', 'Musical performances and collaborations', 'bi-music-note', false, 28],
                    ['Drama Club', 'Theatrical performances and acting', 'bi-mask', false, 25],
                    ['Photography Club', 'Capture moments and learn photography', 'bi-camera', true, 38],
                ];
                foreach ($clubs as $club): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="unit-card">
                        <div class="unit-header">
                            <div>
                                <h5 class="unit-title"><?= $club[0] ?></h5>
                                <div class="unit-code"><?= $club[1] ?></div>
                            </div>
                            <?php if ($club[3]): ?>
                                <span class="badge bg-success rounded-pill">Member</span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill">Not Joined</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                <i class="bi <?= $club[2] ?> me-1"></i><?= $club[4] ?> members
                            </div>
                            <div class="action-buttons">
                                <?php if ($club[3]): ?>
                                    <button class="btn btn-sm btn-action-edit" title="View Activities"><i class="bi bi-eye"></i></button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary">Join</button>
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
