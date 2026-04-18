<?php
$alerts = $dashboardAlerts ?? [];
?>
<div class="topbar d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div class="d-flex align-items-center gap-2">
    <span class="badge text-bg-light border">Operación diaria</span>
    <a class="btn btn-sm btn-light" href="<?= htmlspecialchars(url('panel')) ?>"><i class="bi bi-house me-1"></i>Inicio</a>
    <a class="btn btn-sm btn-light" href="<?= htmlspecialchars(url('pagos')) ?>"><i class="bi bi-plus-circle me-1"></i>Registrar pago</a>
    <a class="btn btn-sm btn-light" href="<?= htmlspecialchars(url('cuotas')) ?>"><i class="bi bi-lightning-charge me-1"></i>Cuotas</a>
  </div>

  <form class="d-flex align-items-center gap-2" method="get" action="<?= htmlspecialchars(url('socios')) ?>">
    <div class="input-group input-group-sm">
      <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
      <input type="text" name="q" class="form-control" placeholder="Buscar socio, RUT, correo..." style="min-width: 230px;">
    </div>
    <button class="btn btn-outline-secondary btn-sm" type="submit">Buscar</button>
  </form>

  <div class="d-flex align-items-center gap-2">
    <?php if (!empty($alerts)): ?>
      <span class="badge text-bg-warning">Alertas: <?= (int) count($alerts) ?></span>
    <?php endif; ?>
    <span class="small text-muted"><i class="bi bi-person-circle me-1"></i>Administrador</span>
    <form method="post" action="<?= htmlspecialchars(url('logout')) ?>" class="m-0">
      <?= csrf_field() ?>
      <button class="btn btn-sm btn-outline-danger" type="submit">Cerrar sesión</button>
    </form>
  </div>
</div>
