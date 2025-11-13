<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Posts</h3>
  <a href="/admin/posts/create" class="btn btn-success">Create New</a>
  
</div>
<?php if (session('success')): ?>
  <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Title</th>
        <th>Published</th>
        <th>Updated</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
        <tr>
          <td><?= esc($p['title']) ?></td>
          <td><?= $p['published'] ? 'Yes' : 'No' ?></td>
          <td><?= esc($p['updated_at']) ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="/admin/posts/<?= $p['id'] ?>/edit">Edit</a>
            <a class="btn btn-sm btn-danger" href="/admin/posts/<?= $p['id'] ?>/delete" onclick="return confirm('Delete this post?')">Delete</a>
            <?php if ($p['published']): ?>
              <a class="btn btn-sm btn-outline-secondary" target="_blank" href="/post/<?= esc($p['slug']) ?>">View</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>