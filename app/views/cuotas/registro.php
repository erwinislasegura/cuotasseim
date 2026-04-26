<?php
$periodoAPagarLabel = static function (array $cuota): string {
  $tipo = trim((string) ($cuota['tipo_periodo'] ?? 'mensual'));
  $fechaBase = (string) ($cuota['fecha_vencimiento'] ?? date('Y-m-d'));
  $month = (int) date('n', strtotime($fechaBase));
  $year = (int) date('Y', strtotime($fechaBase));
  $meses = [1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'];

  if ($tipo === 'trimestral') {
    $n = (int) ceil(max(1, min(12, $month)) / 3);
    $map = [1 => 'uno', 2 => 'dos', 3 => 'tres', 4 => 'cuatro'];
    return 'Trimestre ' . ($map[$n] ?? (string) $n) . ' ' . $year;
  }
  if ($tipo === 'semestral') {
    $n = $month <= 6 ? 1 : 2;
    $map = [1 => 'uno', 2 => 'dos'];
    return 'Semestre ' . ($map[$n] ?? (string) $n) . ' ' . $year;
  }
  if ($tipo === 'anual') {
    return 'Año ' . $year;
  }

  return 'Mes ' . ($meses[$month] ?? (string) $month) . ' ' . $year;
};
?>

<section class="page-header d-flex justify-content-between align-items-start mb-3 gap-2 flex-wrap">
  <div>
    <h1 class="mb-1"><?= htmlspecialchars($title ?? 'Registro de cuotas') ?></h1>
    <p class="small mb-2"><?= htmlspecialchars($description ?? '') ?></p>
  </div>
</section>

<?php if (!empty($flashSuccess ?? '')): ?>
  <div class="alert alert-success small mb-2"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError ?? '')): ?>
  <div class="alert alert-danger small mb-2"><?= htmlspecialchars((string) $flashError) ?></div>
<?php endif; ?>
<?php if (!empty($error ?? '')): ?>
  <div class="alert alert-danger small mb-2"><?= htmlspecialchars((string) $error) ?></div>
<?php endif; ?>

<div class="row g-3 cuotas-layout">
  <div class="col-lg-7">
    <div class="card h-100 cuotas-card-compact">
      <div class="card-header py-2 d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <strong class="card-title mb-0">Socios</strong>
        <form method="get" action="<?= htmlspecialchars(url('cuotas')) ?>" class="d-flex gap-2 flex-wrap cuotas-search-form">
          <input type="text" id="q" name="q" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($q ?? '')) ?>" placeholder="Buscar por nombre o RUT">
          <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
          <a href="<?= htmlspecialchars(url('cuotas')) ?>" class="btn btn-light btn-sm">Limpiar</a>
        </form>
      </div>
      <div class="card-body py-2">
        <?php if (!empty($socios ?? [])): ?>
          <div class="table-responsive cuotas-socios-scroll">
            <table class="table table-sm table-hover mb-0">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Nombre</th>
                  <th>RUT</th>
                  <th class="text-end">Seleccionar</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (($socios ?? []) as $item): ?>
                  <?php $isCurrent = (int) ($selectedSocioId ?? 0) === (int) ($item['id'] ?? 0); ?>
                  <tr class="<?= $isCurrent ? 'table-primary' : '' ?>">
                    <td><?= htmlspecialchars((string) ($item['numero_socio'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string) ($item['nombre_completo'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string) ($item['rut'] ?? '')) ?></td>
                    <td class="text-end">
                      <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url('cuotas') . '?q=' . urlencode((string) ($q ?? '')) . '&socio_id=' . (int) ($item['id'] ?? 0)) ?>">
                        <?= $isCurrent ? 'Activo' : 'Elegir' ?>
                      </a>
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
  </div>

  <div class="col-lg-5">
    <div class="cuotas-sticky-panel">
      <div class="card cuotas-card-compact">
        <div class="card-header py-2"><strong class="card-title mb-0">Pago de cuota (siempre visible)</strong></div>
        <div class="card-body py-2">
          <?php if (!empty($socio)): ?>
            <div class="cuotas-data-list mb-2">
              <div><span>Socio</span><strong><?= htmlspecialchars((string) ($socio['nombre_completo'] ?? '')) ?></strong></div>
              <div><span>RUT</span><strong><?= htmlspecialchars((string) ($socio['rut'] ?? '')) ?></strong></div>
              <div><span>N° Socio</span><strong><?= htmlspecialchars((string) ($socio['numero_socio'] ?? '-')) ?></strong></div>
              <?php if (!empty($cuotaPorVencer)): ?>
                <div><span>Plan</span><strong><?= htmlspecialchars((string) ($cuotaPorVencer['nombre_periodo'] ?? '-')) ?></strong></div>
                <div><span>Periodo</span><strong><?= htmlspecialchars($periodoAPagarLabel($cuotaPorVencer)) ?></strong></div>
                <div><span>Saldo pendiente</span><strong><?= htmlspecialchars(money((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?></strong></div>
              <?php endif; ?>
            </div>

            <?php if (!empty($sinPlanAsociado ?? false)): ?>
              <div class="alert alert-warning small mb-2">
                Este socio no tiene un plan asociado. Debes asociarle un plan para registrar el pago de cuota.
              </div>
            <?php elseif (!empty($cuotaPorVencer)): ?>
              <?php if ((int) ($cuotaPorVencer['es_referencia_plan'] ?? 0) === 1): ?>
                <div class="alert alert-warning small mb-2">Se generará automáticamente la cuota del plan al guardar el pago.</div>
              <?php endif; ?>

              <form method="post" action="<?= htmlspecialchars(url('cuotas')) ?>" class="row g-2">
                <input type="hidden" name="_token" value="<?= htmlspecialchars((string) ($token ?? '')) ?>">
                <input type="hidden" name="q" value="<?= htmlspecialchars((string) ($q ?? '')) ?>">
                <input type="hidden" name="socio_id" value="<?= (int) ($socio['id'] ?? 0) ?>">
                <?php
                  $cuotasSeleccionables = [];
                  if (!empty($cuotaPorVencer)) {
                    $cuotasSeleccionables[] = $cuotaPorVencer;
                  }
                  if (!empty($otrasCuotas ?? [])) {
                    foreach ($otrasCuotas as $cuotaPendiente) {
                      if ((int) ($cuotaPendiente['id'] ?? 0) > 0) {
                        $cuotasSeleccionables[] = $cuotaPendiente;
                      }
                    }
                  }
                ?>

                <?php if (!empty($cuotasSeleccionables)): ?>
                  <div class="col-12">
                    <label class="form-label">Periodo a pagar</label>
                    <select name="cuota_id" class="form-select form-select-sm js-cuota-selector" required>
                      <?php foreach ($cuotasSeleccionables as $index => $cuotaItem): ?>
                        <?php
                          $cuotaId = (int) ($cuotaItem['id'] ?? 0);
                          $saldoPendienteItem = (float) ($cuotaItem['saldo_pendiente'] ?? 0);
                          $periodoTextoItem = $periodoAPagarLabel($cuotaItem);
                          $venceItem = !empty($cuotaItem['fecha_vencimiento']) ? human_date((string) $cuotaItem['fecha_vencimiento']) : '-';
                          $labelPeriodo = trim((string) ($cuotaItem['nombre_periodo'] ?? ('Cuota #' . $cuotaId)));
                          $label = $labelPeriodo . ' · ' . $periodoTextoItem . ' · Vence: ' . $venceItem . ' · Saldo: ' . money($saldoPendienteItem);
                          if ($cuotaId <= 0) {
                            $label .= ' · (se generará automáticamente)';
                          }
                        ?>
                        <option
                          value="<?= $cuotaId ?>"
                          data-saldo="<?= htmlspecialchars((string) $saldoPendienteItem) ?>"
                          <?= $index === 0 ? 'selected' : '' ?>
                        >
                          <?= htmlspecialchars($label) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                <?php else: ?>
                  <input type="hidden" name="cuota_id" value="<?= (int) ($cuotaPorVencer['id'] ?? 0) ?>">
                <?php endif; ?>

                <div class="col-12">
                  <label class="form-label">Monto (editable)</label>
                  <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="<?= htmlspecialchars((string) ((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?>"
                    name="monto_pago"
                    class="form-control form-control-sm js-monto-pago"
                    value="<?= htmlspecialchars((string) ((float) ($cuotaPorVencer['saldo_pendiente'] ?? 0))) ?>"
                    required
                  >
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

                <div class="col-12 d-grid">
                  <button type="submit" class="btn btn-primary btn-sm">Registrar pago</button>
                </div>
              </form>

              <?php if (!empty($otrasCuotas ?? [])): ?>
                <details class="mt-2">
                  <summary class="small" style="cursor:pointer;">Otras cuotas pendientes (<?= (int) count($otrasCuotas) ?>)</summary>
                  <div class="table-responsive mt-2 cuotas-otras-scroll">
                    <table class="table table-sm table-striped mb-0">
                      <thead><tr><th>Plan</th><th>Vence</th><th>Saldo</th></tr></thead>
                      <tbody>
                        <?php foreach ($otrasCuotas as $cuota): ?>
                          <tr>
                            <td><?= htmlspecialchars((string) ($cuota['nombre_periodo'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars(!empty($cuota['fecha_vencimiento']) ? human_date((string) $cuota['fecha_vencimiento']) : '-') ?></td>
                            <td><?= htmlspecialchars(money((float) ($cuota['saldo_pendiente'] ?? 0))) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </details>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <p class="small text-muted mb-0">Selecciona un socio para habilitar el registro de pago.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    const cuotaSelector = document.querySelector('.js-cuota-selector');
    const montoInput = document.querySelector('.js-monto-pago');
    if (!cuotaSelector || !montoInput) {
      return;
    }

    const actualizarMontoDesdePeriodo = function () {
      const option = cuotaSelector.options[cuotaSelector.selectedIndex];
      if (!option) {
        return;
      }
      const saldo = parseFloat(option.getAttribute('data-saldo') || '0');
      if (!Number.isFinite(saldo) || saldo <= 0) {
        return;
      }
      montoInput.max = String(saldo);
      montoInput.value = String(saldo);
    };

    cuotaSelector.addEventListener('change', actualizarMontoDesdePeriodo);
    actualizarMontoDesdePeriodo();
  })();
</script>
