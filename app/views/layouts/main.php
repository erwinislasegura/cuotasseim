<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Panel') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<div class="container-fluid">
  <div class="row">
    <aside class="col-md-2 bg-dark text-white min-vh-100 p-3">
      <?php require __DIR__ . '/partials/sidebar.php'; ?>
    </aside>
    <main class="col-md-10 p-4">
      <?php require __DIR__ . '/partials/navbar.php'; ?>
      <?= $content ?>
      <?php require __DIR__ . '/partials/footer.php'; ?>
    </main>
  </div>
</div>
</body>
</html>
