<section class="page-header d-flex justify-content-between align-items-start mb-3 gap-2 flex-wrap">
  <div>
    <h1 class="mb-1"><?= htmlspecialchars($title ?? 'Registro de cuotas') ?></h1>
    <p class="small mb-2"><?= htmlspecialchars($description ?? '') ?></p>
    <nav aria-label="breadcrumb" class="small">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(url('panel')) ?>">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registro de cuotas</li>
      </ol>
    </nav>
  </div>
</section>

<?php if (!empty($error ?? '')): ?>
  <div class="alert alert-danger small mb-3"><?= htmlspecialchars((string) $error) ?></div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-header py-2"><strong class="card-title mb-0">Buscar socio</strong></div>
  <div class="card-body py-3">
    <form method="get" action="<?= htmlspecialchars(url('cuotas')) ?>" class="row g-2 align-items-end mb-3">
      <div class="col-md-8 col-lg-6">
        <label for="q" class="form-label">Nombre o RUT</label>
        <input type="text" id="q" name="q" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($q ?? '')) ?>" placeholder="Ej: Juan Pérez o 12.345.678-9">
      </div>
      <div class="col-md-auto d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
        <a href="<?= htmlspecialchars(url('cuotas')) ?>" class="btn btn-light btn-sm">Limpiar</a>
      </div>
    </form>

    <?php if (!empty($socios ?? [])): ?>
      <div class="table-responsive">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr>
              <th>N° socio</th>
              <th>Nombre</th>
              <th>RUT</th>
              <th class="text-end">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($socios ?? []) as $item): ?>
              <tr>
                <td><?= htmlspecialchars((string) ($item['numero_socio'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($item['nombre_completo'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($item['rut'] ?? '')) ?></td>
                <td class="text-end">
                  <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url('cuotas') . '?q=' . urlencode((string) ($q ?? '')) . '&socio_id=' . (int) ($item['id'] ?? 0)) ?>">Seleccionar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="small text-muted mb-0">No hay socios para mostrar con la búsqueda actual.</p>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($socio)): ?>
  <div class="card mb-3">
    <div class="card-header py-2"><strong class="card-title mb-0">Datos del socio</strong></div>
    <div class="card-body py-2">
      <div class="table-responsive">
        <table class="table table-sm table-striped mb-0">
          <tbody>
            <tr>
              <th scope="row">N° socio</th>
              <td><?= htmlspecialchars((string) ($socio['numero_socio'] ?? '')) ?></td>
              <th scope="row">Nombre</th>
              <td><?= htmlspecialchars((string) ($socio['nombre_completo'] ?? '')) ?></td>
            </tr>
            <tr>
              <th scope="row">RUT</th>
              <td><?= htmlspecialchars((string) ($socio['rut'] ?? '')) ?></td>
              <th scope="row">Teléfono</th>
              <td><?= htmlspecialchars((string) ($socio['telefono'] ?? '')) ?></td>
            </tr>
            <tr>
              <th scope="row">Correo</th>
              <td><?= htmlspecialchars((string) ($socio['correo'] ?? '')) ?></td>
              <th scope="row">Dirección</th>
              <td><?= htmlspecialchars(trim((string) (($socio['direccion'] ?? '') . ' ' . ($socio['comuna'] ?? '') . ' ' . ($socio['ciudad'] ?? '')))) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header py-2"><strong class="card-title mb-0">Cuota del periodo actual / por vencer</strong></div>
    <div class="card-body py-3">
      <?php if (!empty($cuotaPorVencer)): ?>
        <div class="table-responsive">
          <table class="table table-sm table-striped mb-0">
            <thead>
              <tr>
                <th>Plan</th>
                <th>Tipo de periodo</th>
                <th>Concepto</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th>Monto total</th>
                <th>Pagado</th>
                <th>Saldo pendiente</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= htmlspecialchars((string) ($cuotaPorVencer['nombre_periodo'] ?? '-')) ?></td>
                <td><?= htmlspecialchars(ucfirst((string) ($cuotaPorVencer['tipo_periodo'] ?? '-'))) ?></td>
                <td><?= htmlspecialchars((string) ($cuotaPorVencer['concepto'] ?? '-')) ?></td>
                <td><?= htmlspecialchars(!empty($cuotaPorVencer['fecha_vencimiento']) ? human_date((string) $cuotaPorVencer['fecha_vencimiento']) : '-') ?></td>
                <td><span class="badge badge-status <?= htmlspecialchars(status_badge_class((string) ($cuotaPorVencer['estado_cuota'] ?? 'pendiente'))) ?>"><?= htmlspecialchars(status_label((string) ($cuotaPorVencer['estado_cuota'] ?? 'pendiente'))) ?></span></td>
                <td><?= htmlspecialchars(money((float) ($cuotaPorVencer['monto_total'] ?? 0))) ?></td>
                <td><?= htmlspecialchars(money((float) ($cuotaPorVencer['monto_pagado'] ?? 0))) ?></td>
                <td><?= htmlspecialchars(money((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="small text-muted mb-0">Este socio no tiene cuotas registradas.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($otrasCuotas ?? [])): ?>
    <div class="card">
      <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <strong class="card-title mb-0">Otras cuotas pendientes</strong>
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#otrasCuotasCollapse" aria-expanded="false" aria-controls="otrasCuotasCollapse">
          Ver más cuotas
        </button>
      </div>
      <div class="collapse" id="otrasCuotasCollapse">
        <div class="card-body py-2">
          <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Plan</th>
                  <th>Tipo de periodo</th>
                  <th>Concepto</th>
                  <th>Vencimiento</th>
                  <th>Estado</th>
                  <th>Saldo pendiente</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($otrasCuotas as $index => $cuota): ?>
                  <tr>
                    <td><?= (int) ($index + 2) ?></td>
                    <td><?= htmlspecialchars((string) ($cuota['nombre_periodo'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars(ucfirst((string) ($cuota['tipo_periodo'] ?? '-'))) ?></td>
                    <td><?= htmlspecialchars((string) ($cuota['concepto'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars(!empty($cuota['fecha_vencimiento']) ? human_date((string) $cuota['fecha_vencimiento']) : '-') ?></td>
                    <td><span class="badge badge-status <?= htmlspecialchars(status_badge_class((string) ($cuota['estado_cuota'] ?? 'pendiente'))) ?>"><?= htmlspecialchars(status_label((string) ($cuota['estado_cuota'] ?? 'pendiente'))) ?></span></td>
                    <td><?= htmlspecialchars(money((float) ($cuota['saldo_pendiente'] ?? 0))) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php elseif ((int) ($selectedSocioId ?? 0) > 0): ?>
  <div class="alert alert-warning small">No se encontró información del socio seleccionado.</div>
<?php endif; ?>
