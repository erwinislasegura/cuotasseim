<section class="page-header d-flex justify-content-between align-items-start mb-3 gap-2 flex-wrap">
  <div>
    <h1 class="mb-1"><?= htmlspecialchars($title ?? 'Deuda acumulada') ?></h1>
    <p class="small mb-2"><?= htmlspecialchars($description ?? '') ?></p>
  </div>
</section>

<?php if (!empty($error ?? '')): ?>
  <div class="alert alert-danger small mb-3"><?= htmlspecialchars((string) $error) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>Total deuda acumulada</strong>
    <span class="badge text-bg-warning"><?= money((float) ($totalDeuda ?? 0)) ?></span>
  </div>
  <div class="card-body">
    <?php if (!empty($deudas ?? [])): ?>
      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Socio</th>
              <th>RUT</th>
              <th>Plan</th>
              <th>Vencimiento</th>
              <th>Estado</th>
              <th class="text-end">Monto cuota</th>
              <th class="text-end">Saldo pendiente</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($deudas ?? []) as $item): ?>
              <tr>
                <td>
                  <div class="fw-semibold"><?= htmlspecialchars((string) ($item['nombre_completo'] ?? '')) ?></div>
                  <small class="text-muted">N° <?= htmlspecialchars((string) ($item['numero_socio'] ?? '-')) ?></small>
                </td>
                <td><?= htmlspecialchars((string) ($item['rut'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string) ($item['nombre_periodo'] ?? 'Sin plan')) ?></td>
                <td><?= !empty($item['fecha_vencimiento']) ? htmlspecialchars(human_date((string) $item['fecha_vencimiento'])) : '-' ?></td>
                <td><span class="badge text-bg-secondary"><?= htmlspecialchars((string) ($item['estado_cuota'] ?? '-')) ?></span></td>
                <td class="text-end"><?= money((float) ($item['monto'] ?? 0)) ?></td>
                <td class="text-end fw-semibold"><?= money((float) ($item['saldo_pendiente'] ?? 0)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="small text-muted mb-0">No hay deuda acumulada para mostrar.</p>
    <?php endif; ?>
  </div>
</div>
