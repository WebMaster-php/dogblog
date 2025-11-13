<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<section class="hero p-4 p-md-5 mb-4">
  <div class="row align-items-center">
    <div class="col-md-7">
      <h1 class="display-6 mb-2">Your Daily Dose of Doggos</h1>
      <p class="lead mb-0">Thoughtful posts, adorable images, and a clean, professional experience.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
      <span class="badge bg-primary">CodeIgniter 4</span>
      <span class="badge bg-success ms-2">Dog CEO API</span>
    </div>
  </div>
  
</section>
<div class="row mb-4">
  <div class="col-12">
    <div class="card dog-widget shadow-sm">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h5 class="card-title mb-1">Random Dog Image</h5>
          <div class="text-muted">Powered by <a href="https://dog.ceo" target="_blank" rel="noopener">dog.ceo</a></div>
        </div>
        <button id="refreshDog" class="btn btn-outline-primary">Refresh</button>
      </div>
      <img id="randomDogImg" src="" alt="Random Dog" class="card-img-bottom rounded" style="display: none;">
    </div>
  </div>
</div>
<div class="section-title mb-3"><span class="dot"></span><h3 class="m-0">Latest Posts</h3></div>
<div class="row">
  <?php foreach ($posts as $p): ?>
    <div class="col-md-4 mb-4">
      <div class="card card-fixed">
        <?php if (!empty($p['image_url'])): ?>
          <img src="<?= esc($p['image_url']) ?>" class="card-img-top" alt="">
        <?php endif; ?>
        <div class="card-body">
          <h5 class="card-title mb-1"><?= esc($p['title']) ?></h5>
          <?php if (!empty($p['published_at'])): ?>
            <div class="meta mb-2">Published <?= esc(date('M j, Y H:i', strtotime($p['published_at']))) ?></div>
          <?php endif; ?>
          <p class="excerpt">
            <?= esc(mb_substr(strip_tags($p['content'] ?? ''), 0, 240)) ?>
          </p>
          <a href="/post/<?= esc($p['slug']) ?>" class="btn btn-primary read-more">Read more</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php $page = $page ?? 1; $totalPages = $totalPages ?? 1; ?>
<div class="d-flex justify-content-between align-items-center mt-2">
  <div class="text-muted">Page <?= esc($page) ?> of <?= esc($totalPages) ?></div>
  <nav aria-label="Pagination">
    <ul class="pagination mb-0">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="/?page=<?= max(1, $page - 1) ?>" tabindex="<?= $page <= 1 ? '-1' : '0' ?>">Previous</a>
      </li>
      <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="/?page=<?= min($totalPages, $page + 1) ?>" tabindex="<?= $page >= $totalPages ? '-1' : '0' ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
<script>
async function loadRandomDog() {
  const btn = document.getElementById('refreshDog');
  const img = document.getElementById('randomDogImg');
  if (!btn || !img) return;
  btn.disabled = true;
  btn.textContent = 'Loading...';
  try {
    const res = await fetch('/api/dog/random');
    const data = await res.json();
    if (data && data.image_url) {
      img.src = data.image_url;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }
  } catch (e) {
    img.style.display = 'none';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Refresh';
  }
}
document.getElementById('refreshDog')?.addEventListener('click', loadRandomDog);
window.addEventListener('DOMContentLoaded', loadRandomDog);
</script>
<?= $this->endSection() ?>