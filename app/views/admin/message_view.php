<section class="py-4">
    <div class="admin-content-wrap">
        <div class="admin-page-head mb-3">
            <h1 class="h4 fw-bold mb-0">Message Details</h1>
            <a class="btn btn-outline-secondary" href="<?= e(base_url('admin/messages')) ?>"><i class="bi bi-arrow-left me-1"></i>Back to Messages</a>
        </div>

        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

        <div class="soft-card p-4 mb-3">
            <div class="row g-3">
                <div class="col-md-6"><strong>Name:</strong> <?= e((string)($message['name'] ?? '')) ?></div>
                <div class="col-md-6"><strong>Email:</strong> <?= e((string)($message['email'] ?? '')) ?></div>
                <div class="col-md-6"><strong>Phone:</strong> <?= e((string)($message['phone'] ?? '')) ?></div>
                <div class="col-md-6"><strong>Date:</strong> <?= e((string)($message['created_at'] ?? '')) ?></div>
                <div class="col-12"><strong>Subject:</strong> <?= e((string)($message['subject'] ?? '')) ?></div>
                <div class="col-12">
                    <strong>Message:</strong>
                    <div class="mt-2 p-3 border rounded bg-light"><?= nl2br(e((string)($message['message'] ?? ''))) ?></div>
                </div>
            </div>
        </div>

        <?php if (!empty($message['replied_at'])): ?>
        <div class="soft-card p-4 mb-3 border-start border-4 border-success">
            <h2 class="h6 text-uppercase text-success mb-3"><i class="bi bi-check-circle me-1"></i>Replied on <?= e((string)$message['replied_at']) ?></h2>
            <?php if (!empty($message['reply_subject'])): ?>
                <div class="mb-2"><strong>Subject:</strong> <?= e((string)$message['reply_subject']) ?></div>
            <?php endif; ?>
            <div class="p-3 border rounded bg-light mb-2"><?= nl2br(e((string)($message['reply_body'] ?? ''))) ?></div>
            <small class="text-muted">Reply is saved. You can send another reply below if needed.</small>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= e(base_url('admin/messages/reply/' . (int)($message['id'] ?? 0))) ?>" enctype="multipart/form-data" class="soft-card p-4">
            <?= csrf_field() ?>
            <h2 class="h6 text-uppercase text-muted mb-3">Send Reply</h2>
            <div class="mb-3">
                <label class="form-label">Reply Subject</label>
                <input name="reply_subject" class="form-control" required value="<?= e('Re: ' . (string)($message['subject'] ?? '')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Reply Message</label>
                <textarea name="reply_body" rows="8" class="form-control rich-editor" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Helpful Links (optional)</label>
                <textarea name="reply_links" rows="3" class="form-control" placeholder="https://example.com/admissions-guide&#10;https://example.com/fee-structure"></textarea>
                <small class="text-muted">Add one URL per line. These links are appended to the email.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Attachment (optional: image or PDF)</label>
                <input type="file" name="reply_attachment" class="form-control" accept="image/png,image/jpeg,image/webp,application/pdf">
            </div>
            <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Send Reply</button>
        </form>
    </div>
</section>
