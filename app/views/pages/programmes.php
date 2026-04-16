<?php $categories = $categories ?? ['Diploma', 'Certificate', 'Artisan', 'Short Course']; ?>

<?php
$heroTitlePrimary = 'Programmes';
$heroTitleSecondary = 'Courses Offered';
$heroTagline = 'Explore diploma, certificate, artisan and short-course pathways aligned to healthcare careers.';
$heroPrimaryLabel = 'Apply Now';
$heroPrimaryLink = 'programmes/apply';
$heroSecondaryLabel = 'Contact Admissions';
$heroSecondaryLink = 'contact';
include __DIR__ . '/../partials/page_hero.php';
?>

<section class="section-stack">
  <div class="site-width boxed-section">
    <h1 class="split-title mb-3"><span class="title-primary">Programmes</span> | <span class="title-secondary">Directory</span></h1>
    <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

    <div class="row g-3 align-items-center mb-4">
      <div class="col-lg-7">
        <input id="programmeSearchInput" class="form-control" type="search" placeholder="Start typing to search programmes...">
      </div>
      <div class="col-lg-5">
        <ul class="nav nav-pills justify-content-lg-end gap-2" id="programmeTabs" role="tablist">
          <?php foreach ($categories as $index => $cat): ?>
            <li class="nav-item" role="presentation">
              <button class="btn btn-sm <?= $index === 0 ? 'btn-primary' : 'btn-outline-primary' ?> programme-tab-btn" data-category="<?= e($cat) ?>" type="button"><?= e($cat) ?></button>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <?php foreach ($categories as $idx => $cat): ?>
      <div class="programme-tab-pane <?= $idx === 0 ? '' : 'd-none' ?>" data-category-pane="<?= e($cat) ?>">
        <div class="row g-4 programme-cards-grid">
          <?php foreach (($groupedProgrammes[$cat] ?? []) as $programme): ?>
            <div
              class="col-sm-6 col-lg-3 programme-card-item"
              data-programme-name="<?= e(strtolower((string)$programme['name'])) ?>"
              data-programme-description="<?= e(strtolower((string)$programme['description'])) ?>"
              data-programme-department="<?= e(strtolower((string)($programme['department_name'] ?? 'general'))) ?>"
            >
              <article class="card h-100 border-0 soft-card compact-programme-card">
                <div class="card-body d-flex flex-column">
                  <h2 class="h6"><?= e($programme['name']) ?></h2>
                  <p class="small text-secondary mb-1"><?= e($programme['category']) ?> • <?= (int)$programme['terms'] ?> Terms</p>
                  <p class="small text-muted mb-1">Department: <?= e($programme['department_name'] ?? 'General') ?></p>
                  <p class="small text-muted mb-2 line-clamp-3"><?= e(plain_text($programme['description'] ?? '')) ?></p>
                  <div class="mt-auto d-flex gap-2">
                    <a class="btn btn-sm btn-outline-primary read-more-btn" href="<?= e(base_url('programmes/' . $programme['slug'])) ?>">Read More</a>
                    <a class="btn btn-sm btn-primary apply-now-btn" href="<?= e(base_url('programmes/apply?course=' . urlencode((string)$programme['name']) . '&level=' . urlencode((string)$programme['category']))) ?>">Apply Now</a>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="text-muted mt-3 mb-0 no-results d-none">No programmes found in this category for your search.</p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('programmeSearchInput');
  const tabButtons = document.querySelectorAll('.programme-tab-btn');
  const panes = document.querySelectorAll('.programme-tab-pane');

  const setActiveTab = (category) => {
    tabButtons.forEach((button) => {
      const isActive = button.getAttribute('data-category') === category;
      button.classList.toggle('btn-primary', isActive);
      button.classList.toggle('btn-outline-primary', !isActive);
    });
    panes.forEach((pane) => {
      const isActive = pane.getAttribute('data-category-pane') === category;
      pane.classList.toggle('d-none', !isActive);
    });
    applySearchFilter();
  };

  const applySearchFilter = () => {
    const term = (searchInput.value || '').toLowerCase().trim();
    panes.forEach((pane) => {
      const cards = pane.querySelectorAll('.programme-card-item');
      let visibleCount = 0;
      cards.forEach((card) => {
        const text = [
          card.getAttribute('data-programme-name') || '',
          card.getAttribute('data-programme-description') || '',
          card.getAttribute('data-programme-department') || ''
        ].join(' ');
        const show = term === '' || text.includes(term);
        card.classList.toggle('d-none', !show);
        if (show) visibleCount++;
      });
      const emptyState = pane.querySelector('.no-results');
      if (emptyState) {
        emptyState.classList.toggle('d-none', visibleCount > 0);
      }
    });
  };

  tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
      setActiveTab(button.getAttribute('data-category'));
    });
  });
  searchInput.addEventListener('input', applySearchFilter);

  setActiveTab('Diploma');
});
</script>
