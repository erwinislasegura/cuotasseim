<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="h3 mb-0">Pago de cuotas por Flow</h1>
        <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url('login')) ?>">Administración</a>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <form method="get" action="<?= htmlspecialchars(url('pago-flow')) ?>" class="row g-2 align-items-end">
            <div class="col-md-8">
              <label class="form-label">RUT del socio</label>
              <input type="text" name="rut" class="form-control" required placeholder="12.345.678-9" value="<?= htmlspecialchars((string) ($rut ?? '')) ?>">
            </div>
            <div class="col-md-4 d-grid">
              <button type="submit" class="btn btn-primary">Buscar deuda</button>
            </div>
          </form>
        </div>
      </div>

      <?php if (!empty($flashError ?? '')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $flashError) ?></div>
      <?php endif; ?>

      <?php if (($rut ?? '') !== '' && empty($result)): ?>
        <div class="alert alert-warning">No se encontró un socio con ese RUT o no tiene cuotas pendientes.</div>
      <?php endif; ?>

      <?php if (!empty($result['socio'] ?? null)): ?>
        <?php $socio = $result['socio']; ?>
        <div class="card mb-3">
          <div class="card-header"><strong>Datos del socio</strong></div>
          <div class="card-body">
            <div class="row small g-2">
              <div class="col-md-6"><span class="text-muted">Nombre:</span> <?= htmlspecialchars((string) ($socio['nombre_completo'] ?? '-')) ?></div>
              <div class="col-md-6"><span class="text-muted">RUT:</span> <?= htmlspecialchars((string) ($socio['rut'] ?? '-')) ?></div>
              <div class="col-md-6"><span class="text-muted">N° Socio:</span> <?= htmlspecialchars((string) ($socio['numero_socio'] ?? '-')) ?></div>
              <div class="col-md-6"><span class="text-muted">Teléfono:</span> <?= htmlspecialchars((string) ($socio['telefono'] ?? '-')) ?></div>
            </div>
          </div>
        </div>

        <?php if (!empty($result['periodos_pendientes'] ?? [])): ?>
          <div class="card mb-3">
            <div class="card-header"><strong>Períodos por pagar</strong></div>
            <div class="card-body py-2">
              <?php foreach (($result['periodos_pendientes'] ?? []) as $periodo): ?>
                <span class="badge bg-secondary me-1 mb-1"><?= htmlspecialchars((string) $periodo) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(url('pago-flow/crear')) ?>" id="formPagoFlow">
          <input type="hidden" name="_token" value="<?= htmlspecialchars((string) ($token ?? '')) ?>">
          <input type="hidden" name="rut" value="<?= htmlspecialchars((string) ($socio['rut'] ?? $rut ?? '')) ?>">

          <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
              <strong>Cuotas y conceptos sin pagar</strong>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="seleccionarTodo">Seleccionar todas</button>
            </div>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Concepto</th>
                    <th>Período</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th class="text-end">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach (($result['cuotas_pendientes'] ?? []) as $cuota): ?>
                    <?php $saldo = (float) ($cuota['saldo_pendiente'] ?? 0); ?>
                    <tr>
                      <td>
                        <input class="form-check-input cuota-check" type="checkbox" name="cuotas_ids[]" value="<?= (int) ($cuota['id'] ?? 0) ?>" data-monto="<?= htmlspecialchars((string) $saldo) ?>">
                      </td>
                      <td>#<?= (int) ($cuota['id'] ?? 0) ?></td>
                      <td><?= htmlspecialchars((string) ($cuota['concepto'] ?? 'Cuota')) ?></td>
                      <td><?= htmlspecialchars((string) ($cuota['periodo'] ?? '-')) ?></td>
                      <td><?= htmlspecialchars((string) ($cuota['fecha_vencimiento'] ?? '-')) ?></td>
                      <td><?= htmlspecialchars((string) ($cuota['estado_cuota'] ?? '-')) ?></td>
                      <td class="text-end">$<?= number_format($saldo, 0, ',', '.') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="6" class="text-end">Total seleccionado</th>
                    <th class="text-end" id="totalSeleccionado">$0</th>
                  </tr>
                  <tr>
                    <th colspan="6" class="text-end">Total pendiente</th>
                    <th class="text-end">$<?= number_format((float) ($result['total_pendiente'] ?? 0), 0, ',', '.') ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <button type="submit" class="btn btn-success" id="btnPagarFlow" <?= !($flowEnabled ?? false) ? 'disabled' : '' ?>>Pagar selección con Flow</button>
          <?php if (!($flowEnabled ?? false)): ?>
            <p class="small text-muted mt-2 mb-0">Flow está deshabilitado o faltan credenciales en Configuración.</p>
          <?php endif; ?>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
  (function () {
    const checks = Array.from(document.querySelectorAll('.cuota-check'));
    const totalEl = document.getElementById('totalSeleccionado');
    const toggleAllBtn = document.getElementById('seleccionarTodo');

    const formatClp = (value) => '$' + new Intl.NumberFormat('es-CL').format(Math.round(value || 0));

    const refreshTotal = () => {
      const total = checks.reduce((acc, input) => {
        if (!input.checked) return acc;
        const amount = parseFloat(input.dataset.monto || '0');
        return acc + (isNaN(amount) ? 0 : amount);
      }, 0);
      if (totalEl) totalEl.textContent = formatClp(total);
    };

    checks.forEach((input) => input.addEventListener('change', refreshTotal));

    if (toggleAllBtn) {
      toggleAllBtn.addEventListener('click', () => {
        const allChecked = checks.length > 0 && checks.every((item) => item.checked);
        checks.forEach((item) => { item.checked = !allChecked; });
        toggleAllBtn.textContent = allChecked ? 'Seleccionar todas' : 'Quitar selección';
        refreshTotal();
      });
    }

    refreshTotal();
  })();
</script>
