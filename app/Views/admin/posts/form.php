<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = isset($post) && $post !== null; ?>
<h3 class="mb-3"><?= $isEdit ? 'Edit Post' : 'Create Post' ?></h3>
<form method="post" action="<?= $isEdit ? '/admin/posts/' . $post['id'] . '/update' : '/admin/posts' ?>">
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" class="form-control" name="title" value="<?= esc($post['title'] ?? '') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Content</label>
    <textarea class="form-control" rows="6" name="content"><?= esc($post['content'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Image URL</label>
    <div class="input-group">
      <input type="text" class="form-control" id="image_url" name="image_url" value="<?= esc($post['image_url'] ?? '') ?>">
      <button class="btn btn-outline-secondary" type="button" id="fetchImage" data-post-id="<?= $isEdit ? (int)($post['id']) : '' ?>">Fetch Dog Image</button>
    </div>
    <div class="mt-3" id="imagePreview" style="display: <?= !empty($post['image_url']) ? 'block' : 'none' ?>;">
      <img src="<?= esc($post['image_url'] ?? '') ?>" alt="Preview" class="img-fluid rounded border">
    </div>
  </div>
  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="published" name="published" <?= !empty($post['published']) ? 'checked' : '' ?>>
    <label class="form-check-label" for="published">Published</label>
  </div>
  <button class="btn btn-primary" type="submit">Save</button>
  <a class="btn btn-secondary" href="/admin/posts">Cancel</a>
</form>
<script>
document.getElementById('fetchImage').addEventListener('click', async () => {
  const btn = document.getElementById('fetchImage');
  btn.disabled = true;
  btn.textContent = 'Fetching...';
  try {
    const postId = btn.getAttribute('data-post-id');
    const url = postId ? `/admin/posts/fetch-image?post_id=${postId}` : '/admin/posts/fetch-image';
    const res = await fetch(url);
    const data = await res.json();
    if (data.image_url) {
      document.getElementById('image_url').value = data.image_url;
      const imgPrev = document.getElementById('imagePreview');
      imgPrev.style.display = 'block';
      imgPrev.querySelector('img').src = data.image_url;
    } else {
      alert('Failed to fetch image');
    }
  } catch (e) {
    alert('Error fetching image');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Fetch Dog Image';
  }
});
</script>
<?= $this->endSection() ?>