<div class="bg-white border rounded-3 px-3 py-2 mb-3 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div class="d-flex align-items-center gap-2">
    <span class="badge text-bg-primary">Proyecto</span>
    <a class="btn btn-light btn-sm border" href="<?= htmlspecialchars(url('panel')) ?>">Inicio</a>
    <a class="btn btn-light btn-sm border" href="<?= htmlspecialchars(url('configuracion')) ?>">Configuración</a>
    <a class="btn btn-light btn-sm border" href="<?= htmlspecialchars(url('reportes')) ?>">Reportes</a>
  </div>

  <form class="d-flex align-items-center gap-2" method="get" action="<?= htmlspecialchars(url('socios')) ?>">
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar en socios..." style="min-width: 210px;">
    <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
  </form>

  <div class="d-flex align-items-center gap-2">
    <span class="small text-muted">Admin</span>
    <form method="post" action="<?= htmlspecialchars(url('logout')) ?>" class="m-0">
      <?= csrf_field() ?>
      <button class="btn btn-sm btn-outline-danger" type="submit">Cerrar sesión</button>
    </form>
  </div>
</div>
