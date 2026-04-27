<?php
$featured = $featured ?? null;
$upcoming = is_array($upcoming ?? null) ? $upcoming : [];
$pastGallery = is_array($pastGallery ?? null) ? $pastGallery : [];
$announcementsHtml = (string)($announcementsHtml ?? '');
$socialUpdatesHtml = (string)($socialUpdatesHtml ?? '');
$socialUpdates = is_array($socialUpdates ?? null) ? $socialUpdates : [];
$socialUpdatesTitle = $socialUpdatesTitle ?? 'Social Updates';
$settings = $settings ?? [];
$suTemplate = (string)($settings['social_updates_template'] ?? 'cards');
$suCols = (int)($settings['social_updates_cards_per_row'] ?? 3);
$suShowImages = ($settings['social_updates_show_images'] ?? '1') === '1';
$suContentLines = (int)($settings['social_updates_content_lines'] ?? 3);
$colClass = $suCols === 4 ? 'col-md-6 col-lg-3' : ($suCols === 2 ? 'col-md-6 col-lg-6' : 'col-md-6 col-lg-4');
$elfsightClass = '';
if (preg_match('/elfsight-app-[A-Za-z0-9\-]+/', $socialUpdatesHtml, $match)) {
  $elfsightClass = (string)($match[0] ?? '');
}

function event_category(string $raw): string {
    $v = trim($raw);
    return $v !== '' ? $v : 'General';
}

function reg_status(string $raw): string {
    $v = strtolower(trim($raw));
    return match ($v) {
        'open' => 'Open',
        'closing soon', 'closing_soon', 'closing-soon' => 'Closing soon',
        'full', 'closed' => 'Full',
        default => ($raw !== '' ? $raw : 'Open'),
    };
}
?>

<?php
$heroTitlePrimary = 'Upcoming Events';
$heroTitleSecondary = 'Activities';
$heroTagline = "Stay updated with what's happening at St. Mary's College of Health Sciences.";
$heroPrimaryLabel = 'Join an Event';
$heroPrimaryLink = 'events#upcoming';
$heroSecondaryLabel = 'Contact Us';
$heroSecondaryLink = 'contact';
include __DIR__ . '/../partials/page_hero.php';
?>

<?php if ($featured): ?>
<?php
  $startTs = ($featured['starts_at'] ?? '') !== '' ? strtotime((string)$featured['starts_at']) : null;
  $countdownTarget = $startTs ? date('c', $startTs) : '';
  $img = trim((string)($featured['image_path'] ?? ''));
?>
<section class="section-stack">
  <div class="site-width boxed-section" data-aos="fade-up">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <img class="img-fluid soft-card" src="<?= e($img !== '' ? $img : 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200') ?>" alt="<?= e($featured['title'] ?? 'Featured event') ?>">
      </div>
      <div class="col-lg-6">
        <p class="text-uppercase small fw-semibold text-secondary mb-2">Featured Event</p>
        <h2 class="section-title-standard mb-2"><?= e($featured['title'] ?? '') ?></h2>
        <p class="text-muted mb-3"><?= e(($featured['summary'] ?? '') !== '' ? plain_text((string)$featured['summary']) : 'Don’t miss our next major campus activity.') ?></p>
        <div class="d-flex flex-wrap gap-3 mb-3 small text-muted">
          <span><i class="bi bi-calendar-event me-1"></i><?= e($startTs ? date('l, j F Y', $startTs) : '') ?></span>
          <span><i class="bi bi-clock me-1"></i><?= e((string)($featured['time_label'] ?? '')) ?></span>
          <span><i class="bi bi-geo-alt me-1"></i><?= e((string)($featured['location'] ?? '')) ?></span>
        </div>
        <?php if ($countdownTarget !== ''): ?>
          <div class="soft-card p-3 mb-3" style="background:rgba(0,170,232,.10)!important">
            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
              <strong>Starts in:</strong>
              <div id="eventCountdown" data-target="<?= e($countdownTarget) ?>" class="fw-semibold"></div>
            </div>
          </div>
        <?php endif; ?>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="<?= e(base_url('events/' . ($featured['slug'] ?? ''))) ?>">Register Now</a>
          <a class="btn btn-outline-primary" href="#upcoming">See all upcoming</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php endif; ?>

<section class="section-stack">
  <div class="site-width boxed-section" data-aos="fade-up">
    <h2 class="split-title mb-3"><span class="title-primary"><?= e($socialUpdatesTitle) ?></span></h2>
    <?php if ($socialUpdates !== []):
      if ($suTemplate === 'minimal'): ?>
      <div class="list-group">
        <?php foreach ($socialUpdates as $update): ?>
        <div class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div class="social-feed-content<?= $suContentLines > 0 ? '' : ' social-feed-content-expanded' ?>" style="<?= $suContentLines > 0 ? '-webkit-line-clamp:' . $suContentLines . ';line-clamp:' . $suContentLines : '' ?>"><?= nl2br(e((string)($update['content'] ?? ''))) ?></div>
                <small class="text-muted ms-2 flex-shrink-0"><?= e(date('M j', strtotime((string)($update['posted_at'] ?? $update['created_at'] ?? 'now')))) ?></small>
            </div>
            <?php if (!empty($update['link_url'])): ?>
                <a href="<?= e((string)$update['link_url']) ?>" target="_blank" rel="noopener" class="small">View post <i class="bi bi-box-arrow-up-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php elseif ($suTemplate === 'compact'): ?>
      <div class="row g-2">
        <?php foreach ($socialUpdates as $update): ?>
        <div class="col-12">
            <div class="d-flex align-items-center p-2 border rounded mb-1">
                <?php if ($suShowImages && !empty($update['image_path'])): ?>
                <img src="<?= e((string)$update['image_path']) ?>" alt="" class="rounded me-2" style="width:60px;height:60px;object-fit:cover;flex-shrink:0" loading="lazy">
                <?php endif; ?>
                <div class="flex-grow-1 min-w-0">
                    <div class="social-feed-content<?= $suContentLines > 0 ? '' : ' social-feed-content-expanded' ?>" style="<?= $suContentLines > 0 ? '-webkit-line-clamp:' . $suContentLines . ';line-clamp:' . $suContentLines : '' ?>"><?= nl2br(e((string)($update['content'] ?? ''))) ?></div>
                    <small class="text-muted"><?= e(date('M j, Y', strtotime((string)($update['posted_at'] ?? $update['created_at'] ?? 'now')))) ?></small>
                </div>
                <?php if (!empty($update['link_url'])): ?>
                <a href="<?= e((string)$update['link_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary ms-2 flex-shrink-0"><i class="bi bi-box-arrow-up-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($socialUpdates as $update): ?>
          <div class="<?= $colClass ?>">
            <article class="social-feed-item h-100 <?= !empty($update['is_pinned']) ? 'social-feed-pinned' : '' ?>">
              <?php if (!empty($update['is_pinned'])): ?>
                <span class="badge bg-warning text-dark social-feed-pin"><i class="bi bi-pin-angle-fill me-1"></i>Pinned</span>
              <?php endif; ?>
              <?php if (!empty($update['source'])): ?>
                <span class="social-feed-source"><i class="bi bi-<?= $update['source'] === 'instagram' ? 'instagram' : ($update['source'] === 'facebook' ? 'facebook' : 'tag') ?> me-1"></i><?= e(ucfirst((string)$update['source'])) ?></span>
              <?php endif; ?>
              <?php if ($suShowImages && !empty($update['image_path'])): ?>
                <img src="<?= e((string)$update['image_path']) ?>" alt="" class="social-feed-image" loading="lazy">
              <?php endif; ?>
              <div class="social-feed-content<?= $suContentLines > 0 ? '' : ' social-feed-content-expanded' ?>" style="<?= $suContentLines > 0 ? '-webkit-line-clamp:' . $suContentLines . ';line-clamp:' . $suContentLines : '' ?>"><?= nl2br(e((string)($update['content'] ?? ''))) ?></div>
              <div class="social-feed-meta">
                <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= e(date('M j, Y', strtotime((string)($update['posted_at'] ?? $update['created_at'] ?? 'now')))) ?></span>
                <?php if (!empty($update['link_url'])): ?>
                  <a href="<?= e((string)$update['link_url']) ?>" target="_blank" rel="noopener" class="small">Read more <i class="bi bi-box-arrow-up-right"></i></a>
                <?php endif; ?>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php elseif ($elfsightClass !== ''): ?>
      <div class="soft-card bg-white p-4">
        <div class="<?= e($elfsightClass) ?>" data-elfsight-app-lazy></div>
      </div>
    <?php elseif (trim($socialUpdatesHtml) !== ''): ?>
      <div class="soft-card bg-white p-4"><?= safe_html($socialUpdatesHtml, ['div','iframe','a','p','br','strong','b','em','i','span','blockquote']) ?></div>
    <?php else: ?>
      <p class="text-muted mb-0">No social updates yet. Add them from the admin panel.</p>
    <?php endif; ?>
  </div>
</section>

<section class="section-stack" id="upcoming">
  <div class="site-width boxed-section" data-aos="fade-up">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
      <div>
        <h2 class="split-title mb-1"><span class="title-primary">Upcoming</span> | <span class="title-secondary">Events</span></h2>
        <p class="section-subtitle-standard">Workshops, trainings, guest lectures, outreach and student activities.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <?php $cats = ['All','Academic Workshops','Clinical Training Sessions','Guest Lectures','Career Days','Community Outreach Programs','Student Life & Sports Events']; ?>
        <?php foreach ($cats as $c): ?>
          <button type="button" class="btn btn-sm <?= $c==='All'?'btn-primary':'btn-outline-primary' ?> event-filter" data-filter="<?= e($c) ?>"><?= e($c) ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ($upcoming === []): ?>
      <p class="text-muted mb-0">No upcoming events yet. Check back soon.</p>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($upcoming as $ev): ?>
          <?php
            $startTs = ($ev['starts_at'] ?? '') !== '' ? strtotime((string)$ev['starts_at']) : null;
            $cat = event_category((string)($ev['category'] ?? ''));
            $status = reg_status((string)($ev['registration_status'] ?? 'Open'));
            $statusClass = $status === 'Open' ? 'bg-success' : ($status === 'Closing soon' ? 'bg-warning text-dark' : 'bg-secondary');
            $img = trim((string)($ev['image_path'] ?? ''));
            $desc = ($ev['summary'] ?? '') !== '' ? plain_text((string)$ev['summary']) : plain_text((string)($ev['body'] ?? ''));
            $desc = $desc !== '' ? $desc : 'What you’ll gain: practical exposure, mentorship and campus connections.';
          ?>
          <div class="col-md-6 col-lg-4 event-card" data-category="<?= e($cat) ?>">
            <article class="card border-0 soft-card h-100">
              <img class="card-img-top" style="height:190px;object-fit:cover" src="<?= e($img !== '' ? $img : 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900') ?>" alt="<?= e($ev['title'] ?? '') ?>">
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                  <span class="badge bg-light text-dark"><?= e($cat) ?></span>
                  <span class="badge <?= e($statusClass) ?>"><?= e($status) ?></span>
                </div>
                <h3 class="h6 mb-2"><?= e($ev['title'] ?? '') ?></h3>
                <p class="small text-muted mb-2">
                  <i class="bi bi-calendar-event me-1"></i><?= e($startTs ? date('D, j M Y', $startTs) : '') ?>
                  <?php if (($ev['time_label'] ?? '') !== ''): ?> • <i class="bi bi-clock me-1"></i><?= e((string)$ev['time_label']) ?><?php endif; ?>
                </p>
                <p class="small text-muted mb-3">
                  <?php if (($ev['location'] ?? '') !== ''): ?><i class="bi bi-geo-alt me-1"></i><?= e((string)$ev['location']) ?><?php endif; ?>
                </p>
                <p class="small mb-3 text-secondary-emphasis"><?= e($desc) ?></p>
                <div class="mt-auto d-flex gap-2 flex-wrap">
                  <?php $registerHref = trim((string)($ev['registration_url'] ?? '')) !== '' ? (string)$ev['registration_url'] : base_url('events/' . ($ev['slug'] ?? '') . '/register'); ?>
                  <a class="btn btn-sm btn-primary" href="<?= e($registerHref) ?>"><?= $status === 'Open' ? 'Register' : 'Learn More' ?></a>
                  <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('events/' . ($ev['slug'] ?? ''))) ?>">Learn More</a>
                </div>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section-stack">
  <div class="site-width boxed-section" data-aos="fade-up">
    <h2 class="split-title mb-3"><span class="title-primary">Past Events</span> | <span class="title-secondary">Gallery</span></h2>
    <p class="text-muted mb-4">A glimpse of student engagement, practical sessions, and community activities.</p>
    <?php if ($pastGallery === []): ?>
      <p class="text-muted mb-0">No gallery photos yet. Add images under Admin → Gallery (Category: Events).</p>
    <?php else: ?>
      <div class="gallery-grid">
        <?php foreach ($pastGallery as $item): ?>
          <figure class="gallery-item">
            <img loading="lazy" src="<?= e(base_url(ltrim((string)$item['image_path'], '/'))) ?>" alt="<?= e((string)$item['title']) ?>" data-lightbox-src="<?= e(base_url(ltrim((string)$item['image_path'], '/'))) ?>">
            <figcaption><?= e((string)$item['title']) ?></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section-stack">
  <div class="site-width boxed-section" data-aos="fade-up">
    <h2 class="split-title mb-3"><span class="title-primary">Event</span> | <span class="title-secondary">Announcements</span></h2>
    <?php if (trim($announcementsHtml) !== ''): ?>
      <div class="soft-card bg-white p-4"><?= safe_html($announcementsHtml) ?></div>
    <?php else: ?>
      <p class="text-muted mb-0">No announcements yet.</p>
    <?php endif; ?>
  </div>
</section>

<section class="section-stack">
  <div class="site-width boxed-section" data-aos="fade-up">
    <div class="cta-banner p-5 text-center text-white">
      <h2 class="fw-bold mb-2">Don’t miss out on opportunities to learn and grow beyond the classroom.</h2>
      <p class="mb-4 text-white-50">Join workshops, trainings and community programmes—build skills and networks.</p>
      <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a class="btn btn-light" href="#upcoming">Join an Event</a>
        <a class="btn btn-outline-light" href="<?= e(base_url('contact')) ?>">Contact Us</a>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Category filters (client-side)
  const buttons = document.querySelectorAll('.event-filter');
  const cards = document.querySelectorAll('.event-card');
  buttons.forEach(btn => btn.addEventListener('click', () => {
    buttons.forEach(b => b.classList.remove('btn-primary'));
    buttons.forEach(b => b.classList.add('btn-outline-primary'));
    btn.classList.remove('btn-outline-primary');
    btn.classList.add('btn-primary');
    const filter = btn.getAttribute('data-filter') || 'All';
    cards.forEach(card => {
      const cat = card.getAttribute('data-category') || 'General';
      card.style.display = (filter === 'All' || cat === filter) ? '' : 'none';
    });
  }));

  // Featured countdown
  const el = document.getElementById('eventCountdown');
  if (el) {
    const targetRaw = el.getAttribute('data-target');
    const target = targetRaw ? new Date(targetRaw).getTime() : null;
    if (target) {
      const tick = () => {
        const now = Date.now();
        const diff = Math.max(0, target - now);
        const d = Math.floor(diff / (1000 * 60 * 60 * 24));
        const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const m = Math.floor((diff / (1000 * 60)) % 60);
        el.textContent = `${d}d ${h}h ${m}m`;
      };
      tick();
      setInterval(tick, 60000);
    }
  }
});
</script>

