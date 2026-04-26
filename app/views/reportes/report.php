<?php
$summary = is_array($summary ?? null) ? $summary : [];
$byType = is_array($byType ?? null) ? $byType : [];
$byOrigin = is_array($byOrigin ?? null) ? $byOrigin : [];
$byMonth = is_array($byMonth ?? null) ? $byMonth : [];
$rows = is_array($rows ?? null) ? $rows : [];

$totalIngresos = (float) ($summary['total_ingresos'] ?? 0);
$totalEgresos = (float) ($summary['total_egresos'] ?? 0);
$balance = $totalIngresos - $totalEgresos;

$maxTypeValues = array_values(array_map(static fn($value): float => (float) $value, $byType ?: [0]));
$sumType = max(1.0, array_sum($maxTypeValues));
$originTotals = [];
foreach ($byOrigin as $origin => $values) {
    $originTotals[$origin] = (float) (($values['ingreso'] ?? 0) + ($values['egreso'] ?? 0));
}
$maxOriginValues = array_values(array_map(static fn($value): float => (float) $value, $originTotals ?: [0]));
$sumOrigin = max(1.0, array_sum($maxOriginValues));
?>

<style>
  :root {
    --corp-primary: #0f3a68;
    --corp-secondary: #1f6fb2;
    --corp-ink: #0f172a;
    --corp-muted: #64748b;
    --corp-border: #dbe5f0;
    --corp-soft: #f4f8fc;
    --corp-positive: #166534;
    --corp-negative: #b91c1c;
  }
  body { font-family: "Segoe UI", Roboto, Arial, sans-serif; color: var(--corp-ink); background: #fff; }
  .report-wrap { max-width: 1040px; margin: 0 auto; padding: .75rem; }
  .report-header {
    display:flex; justify-content:space-between; align-items:flex-start; gap:.75rem; margin-bottom:.7rem;
    padding: .65rem .8rem; border:1px solid var(--corp-border); border-radius:.6rem;
    background: linear-gradient(135deg, #ffffff 0%, var(--corp-soft) 100%);
  }
  .report-title { margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--corp-primary); letter-spacing: .15px; }
  .report-subtitle { font-size: .72rem; color: var(--corp-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: .65px; }
  .report-meta { font-size: .78rem; color: var(--corp-muted); line-height: 1.25; }
  .report-section { margin-bottom: .6rem; }
  .section-title {
    margin: 0 0 .2rem 0; font-size: .8rem; font-weight: 700; color: var(--corp-primary);
    text-transform: uppercase; letter-spacing: .45px;
  }
  .table-wrap { border: 1px solid var(--corp-border); border-radius: .42rem; overflow: hidden; }
  .summary-table td { font-size: .75rem; padding: .35rem .5rem; }
  .summary-table td:first-child { font-weight: 600; color: #1e3a5f; width: 25%; background: #f8fbff; }
  .split-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .45rem; }
  .month-table th, .month-table td { font-size:.74rem; padding: .34rem .45rem; }
  .table thead th { background: #f8fbff; color: #1e3a5f; font-weight: 700; border-bottom-color: #dbe5f0; font-size: .73rem; padding: .38rem .48rem; }
  .table tbody td { padding: .34rem .48rem; font-size: .74rem; }
  @media print {
    @page { size: A4 portrait; margin: 8mm; }
    .no-print { display:none !important; }
    body { background:#fff; }
    .report-wrap { padding:0; max-width: 100%; }
    .report-header, .table-wrap { box-shadow: none; }
    .split-grid, .table-wrap { break-inside: avoid; page-break-inside: avoid; }
  }
</style>

<div class="report-wrap">
  <div class="report-header">
    <div>
      <div class="report-subtitle">Gerencia administrativa · formato de impresión</div>
      <h2 class="report-title">Informe corporativo de reportes</h2>
      <div class="report-meta">
        Fecha emisión: <?= htmlspecialchars(date('d-m-Y H:i')) ?> ·
        Rango: <?= htmlspecialchars((string) ($from ?: 'Sin límite')) ?> a <?= htmlspecialchars((string) ($to ?: 'Sin límite')) ?> ·
        Estado: <?= htmlspecialchars((string) ($status ?: 'Todos')) ?>
      </div>
      <div class="report-meta mt-1">
        Filtro socio: <?= htmlspecialchars((string) (($extraFilters['socio_id'] ?? '') !== '' ? ('ID ' . $extraFilters['socio_id']) : 'Todos')) ?> ·
        Búsqueda: <?= htmlspecialchars((string) ($query ?: 'Sin texto')) ?>
      </div>
    </div>
    <div class="no-print d-flex gap-2">
      <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
      <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url('reportes')) ?>">Volver</a>
    </div>
  </div>

  <section class="report-section">
    <h3 class="section-title">Resumen ejecutivo</h3>
    <div class="table-wrap">
      <table class="table table-sm mb-0 summary-table">
        <tbody>
          <tr>
            <td>Registros</td>
            <td><?= (int) ($summary['total_registros'] ?? 0) ?></td>
            <td>Ingresos</td>
            <td>$<?= number_format($totalIngresos, 0, ',', '.') ?></td>
          </tr>
          <tr>
            <td>Egresos</td>
            <td>$<?= number_format($totalEgresos, 0, ',', '.') ?></td>
            <td>Balance</td>
            <td style="font-weight:700; color:<?= $balance >= 0 ? '#166534' : '#b91c1c' ?>">$<?= number_format($balance, 0, ',', '.') ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="report-section split-grid">
    <div>
      <h3 class="section-title">Distribución por tipo</h3>
      <div class="table-wrap">
        <table class="table table-sm mb-0">
          <thead><tr><th>Tipo</th><th class="text-end">Monto</th><th class="text-end">%</th></tr></thead>
          <tbody>
            <?php foreach ($byType as $type => $value): ?>
              <?php $percent = $sumType > 0 ? ((((float) $value) / $sumType) * 100) : 0; ?>
              <tr>
                <td><?= htmlspecialchars(ucfirst((string) $type)) ?></td>
                <td class="text-end">$<?= number_format((float) $value, 0, ',', '.') ?></td>
                <td class="text-end"><?= number_format($percent, 1, ',', '.') ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div>
      <h3 class="section-title">Composición por origen</h3>
      <div class="table-wrap">
        <table class="table table-sm mb-0">
          <thead><tr><th>Origen</th><th class="text-end">Monto</th><th class="text-end">%</th></tr></thead>
          <tbody>
            <?php foreach ($originTotals as $origin => $value): ?>
              <?php $percent = $sumOrigin > 0 ? ((((float) $value) / $sumOrigin) * 100) : 0; ?>
              <tr>
                <td><?= htmlspecialchars((string) $origin) ?></td>
                <td class="text-end">$<?= number_format((float) $value, 0, ',', '.') ?></td>
                <td class="text-end"><?= number_format($percent, 1, ',', '.') ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="report-section">
    <h3 class="section-title">Evolución mensual (filtrada)</h3>
    <div class="table-wrap table-responsive">
      <table class="table table-sm table-striped mb-0 month-table">
        <thead><tr><th>Mes</th><th class="text-end">Ingresos</th><th class="text-end">Egresos</th><th class="text-end">Balance</th></tr></thead>
        <tbody>
          <?php if (empty($byMonth)): ?>
            <tr><td colspan="4" class="text-center py-3">Sin datos en el período seleccionado.</td></tr>
          <?php else: ?>
            <?php foreach ($byMonth as $month => $totals): ?>
              <?php $monthBalance = (float) ($totals['ingreso'] ?? 0) - (float) ($totals['egreso'] ?? 0); ?>
              <tr>
                <td><?= htmlspecialchars((string) $month) ?></td>
                <td class="text-end">$<?= number_format((float) ($totals['ingreso'] ?? 0), 0, ',', '.') ?></td>
                <td class="text-end">$<?= number_format((float) ($totals['egreso'] ?? 0), 0, ',', '.') ?></td>
                <td class="text-end" style="color:<?= $monthBalance >= 0 ? '#166534' : '#b91c1c' ?>">$<?= number_format($monthBalance, 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="report-section">
    <h3 class="section-title">Detalle de movimientos filtrados</h3>
    <div class="table-wrap table-responsive">
      <table class="table table-sm table-hover mb-0">
        <thead><tr><th>#</th><th>Fecha</th><th>Tipo</th><th>Origen</th><th>Descripción</th><th class="text-end">Ingreso</th><th class="text-end">Egreso</th></tr></thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="7" class="text-center py-3">No hay movimientos para este filtro.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $i => $row): ?>
              <tr>
                <td><?= (int) ($i + 1) ?></td>
                <td><?= htmlspecialchars((string) ($row['fecha'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($row['tipo_movimiento'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($row['origen_modulo'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($row['descripcion'] ?? '')) ?></td>
                <td class="text-end">$<?= number_format((float) ($row['ingreso'] ?? 0), 0, ',', '.') ?></td>
                <td class="text-end">$<?= number_format((float) ($row['egreso'] ?? 0), 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
