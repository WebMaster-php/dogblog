<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dog Blog') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
          <img src="/images/logo.svg" alt="Dog Blog" class="navbar-logo me-2">
          <span>Dog Blog</span>
        </a>
        <div class="d-flex ms-auto">
          <?php if(session()->get('user_id')): ?>
            <a class="btn btn-outline-light me-2" href="/admin/posts">Admin</a>
            <a class="btn btn-outline-warning" href="/logout">Logout</a>
          <?php else: ?>
            <a class="btn btn-outline-light" href="/login">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <main class="container">
      <?= $this->renderSection('content') ?>
    </main>

    <footer class="bg-primary text-light mt-5 py-4">
      <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-2 mb-md-0">
          <span class="fw-bold">Dog Blog</span> · Built with CodeIgniter 4
        </div>
        <div class="text-muted">
          Powered by <a class="link-light" href="https://dog.ceo" target="_blank" rel="noopener">dog.ceo</a>
        </div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>