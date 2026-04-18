<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0"><?= htmlspecialchars($title ?? 'Módulo') ?></h1>
  <form method="post" action="/logout" class="m-0">
    <?= csrf_field() ?>
    <button class="btn btn-sm btn-outline-danger" type="submit">Cerrar sesión</button>
  </form>
</div>
