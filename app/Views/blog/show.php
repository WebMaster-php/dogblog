<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= esc($post['title']) ?></li>
  </ol>
</nav>
<article>
  <h2 class="mb-3"><?= esc($post['title']) ?></h2>
  <?php if (!empty($post['image_url'])): ?>
    <img src="<?= esc($post['image_url']) ?>" class="img-fluid rounded mb-3" alt="">
  <?php endif; ?>
  <div><?= $post['content'] ?></div>
</article>

<hr class="my-4">

<div class="row">
  <div class="col-md-8">
    <div class="section-title mb-2"><span class="dot"></span><h5 class="m-0">More Posts</h5></div>
    <?php if (!empty($recentPosts)): ?>
      <ul class="list-group list-group-flush">
        <?php foreach ($recentPosts as $rp): ?>
          <li class="list-group-item">
            <a href="/post/<?= esc($rp['slug']) ?>" class="text-decoration-none"><?= esc($rp['title']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="text-muted">No other posts yet.</div>
    <?php endif; ?>
  </div>
  <div class="col-md-4 mt-3 mt-md-0 d-flex align-items-start justify-content-md-end">
    <a href="/" class="btn btn-outline-secondary">Go to Home</a>
  </div>
  
</div>
<?= $this->endSection() ?>