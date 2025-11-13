<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
  <div class="col-md-4">
    <h3 class="mb-3">Admin Login</h3>
    <?php if (session('error')): ?>
      <div class="alert alert-danger"><?= esc(session('error')) ?></div>
    <?php endif; ?>
    <form method="post" action="/login">
      <div class="mb-3">
        <label class="form-label">Username or Email</label>
        <input type="text" class="form-control" name="username" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>
  </div>
  
</div>
<?= $this->endSection() ?>