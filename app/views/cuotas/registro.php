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

<?php if (!empty($flashSuccess ?? '')): ?>
  <div class="alert alert-success small mb-3"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError ?? '')): ?>
  <div class="alert alert-danger small mb-3"><?= htmlspecialchars((string) $flashError) ?></div>
<?php endif; ?>
<?php if (!empty($error ?? '')): ?>
  <div class="alert alert-danger small mb-3"><?= htmlspecialchars((string) $error) ?></div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-header py-2"><strong class="card-title mb-0">1) Buscar y seleccionar socio</strong></div>
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

<?php if (!empty($socio) && !empty($cuotaPorVencer)): ?>
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header py-2"><strong class="card-title mb-0">2) Cuota actual a pagar</strong></div>
        <div class="card-body py-3">
          <?php if ((int) ($cuotaPorVencer['es_referencia_plan'] ?? 0) === 1): ?>
            <div class="alert alert-warning small mb-3">
              Esta cuota viene del plan actual y se generará automáticamente al guardar el pago.
            </div>
          <?php endif; ?>

          <div class="table-responsive mb-3">
            <table class="table table-sm table-striped mb-0">
              <tbody>
                <tr>
                  <th>Socio</th>
                  <td><?= htmlspecialchars((string) ($socio['nombre_completo'] ?? '')) ?></td>
                  <th>RUT</th>
                  <td><?= htmlspecialchars((string) ($socio['rut'] ?? '')) ?></td>
                </tr>
                <tr>
                  <th>Plan</th>
                  <td><?= htmlspecialchars((string) ($cuotaPorVencer['nombre_periodo'] ?? '-')) ?></td>
                  <th>Periodo</th>
                  <td><?= htmlspecialchars(ucfirst((string) ($cuotaPorVencer['tipo_periodo'] ?? '-'))) ?></td>
                </tr>
                <tr>
                  <th>Vencimiento</th>
                  <td><?= htmlspecialchars(!empty($cuotaPorVencer['fecha_vencimiento']) ? human_date((string) $cuotaPorVencer['fecha_vencimiento']) : '-') ?></td>
                  <th>Estado</th>
                  <td><span class="badge badge-status <?= htmlspecialchars(status_badge_class((string) ($cuotaPorVencer['estado_cuota'] ?? 'pendiente'))) ?>"><?= htmlspecialchars(status_label((string) ($cuotaPorVencer['estado_cuota'] ?? 'pendiente'))) ?></span></td>
                </tr>
                <tr>
                  <th>Monto total</th>
                  <td><?= htmlspecialchars(money((float) ($cuotaPorVencer['monto_total'] ?? 0))) ?></td>
                  <th>Saldo pendiente</th>
                  <td><strong><?= htmlspecialchars(money((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?></strong></td>
                </tr>
              </tbody>
            </table>
          </div>

          <?php if (!empty($otrasCuotas ?? [])): ?>
            <details>
              <summary class="small mb-2" style="cursor:pointer;">Ver más cuotas pendientes para adelantar pago</summary>
              <div class="table-responsive mt-2">
                <table class="table table-sm table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Plan</th>
                      <th>Vencimiento</th>
                      <th>Estado</th>
                      <th>Saldo pendiente</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($otrasCuotas as $cuota): ?>
                      <tr>
                        <td><?= htmlspecialchars((string) ($cuota['nombre_periodo'] ?? '-')) ?></td>
                        <td><?= htmlspecialchars(!empty($cuota['fecha_vencimiento']) ? human_date((string) $cuota['fecha_vencimiento']) : '-') ?></td>
                        <td><?= htmlspecialchars(status_label((string) ($cuota['estado_cuota'] ?? 'pendiente'))) ?></td>
                        <td><?= htmlspecialchars(money((float) ($cuota['saldo_pendiente'] ?? 0))) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </details>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header py-2"><strong class="card-title mb-0">3) Registrar pago</strong></div>
        <div class="card-body py-3">
          <form method="post" action="<?= htmlspecialchars(url('cuotas')) ?>" class="row g-2">
            <input type="hidden" name="_token" value="<?= htmlspecialchars((string) ($token ?? '')) ?>">
            <input type="hidden" name="q" value="<?= htmlspecialchars((string) ($q ?? '')) ?>">
            <input type="hidden" name="socio_id" value="<?= (int) ($socio['id'] ?? 0) ?>">
            <input type="hidden" name="cuota_id" value="<?= (int) ($cuotaPorVencer['id'] ?? 0) ?>">

            <div class="col-12">
              <label class="form-label">Monto a pagar</label>
              <input type="number" step="0.01" min="0.01" max="<?= htmlspecialchars((string) ((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?>" name="monto_pago" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?>" required>
              <small class="text-muted">Editable. Sugerido: saldo pendiente.</small>
            </div>

            <div class="col-12">
              <label class="form-label">Forma de pago</label>
              <select name="medio_pago_id" class="form-select form-select-sm" required>
                <option value="">Seleccionar...</option>
                <?php foreach (($mediosPago ?? []) as $medio): ?>
                  <option value="<?= (int) ($medio['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($medio['nombre'] ?? '')) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Fecha de pago</label>
              <input type="date" name="fecha_pago" class="form-control form-control-sm" value="<?= htmlspecialchars(date('Y-m-d')) ?>" required>
            </div>

            <div class="col-12 d-grid mt-2">
              <button type="submit" class="btn btn-primary btn-sm">Registrar pago de cuota</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php elseif ((int) ($selectedSocioId ?? 0) > 0): ?>
  <div class="alert alert-warning small">No se encontró cuota actual para este socio. Verifica que tenga un plan asociado.</div>
<?php endif; ?>
