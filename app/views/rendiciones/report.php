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
$maxType = max(1, ...$maxTypeValues);
$originTotals = [];
foreach ($byOrigin as $origin => $values) {
    $originTotals[$origin] = (float) (($values['ingreso'] ?? 0) + ($values['egreso'] ?? 0));
}
$maxOriginValues = array_values(array_map(static fn($value): float => (float) $value, $originTotals ?: [0]));
$maxOrigin = max(1, ...$maxOriginValues);
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
  .kpi-grid { display:grid; grid-template-columns: repeat(4,minmax(130px,1fr)); gap:.5rem; margin-bottom:.7rem; }
  .kpi {
    border:1px solid var(--corp-border); border-radius:.5rem; padding:.55rem .65rem; background:#fff;
    box-shadow: 0 1px 2px rgba(15, 58, 104, .06);
  }
  .kpi .label { font-size:.68rem; color:var(--corp-muted); text-transform:uppercase; letter-spacing:.45px; }
  .kpi .value { font-size:1rem; font-weight:700; color: var(--corp-primary); line-height: 1.1; }
  .chart-grid { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; margin-bottom:.7rem; }
  .chart-card {
    border:1px solid var(--corp-border); border-radius:.55rem; padding:.6rem; background:#fff;
    box-shadow: 0 1px 2px rgba(15, 58, 104, .05);
  }
  .chart-title { font-size: .85rem; font-weight:700; margin-bottom:.4rem; color: var(--corp-primary); }
  .mini-bars { display:grid; gap:.22rem; }
  .bar-row { display:grid; grid-template-columns:110px 1fr 90px; align-items:center; gap:.35rem; font-size:.76rem; }
  .bar-track { background:#e8eef5; border-radius:999px; height:7px; overflow:hidden; }
  .bar-fill { height:100%; background:var(--corp-secondary); border-radius:999px; }
  .bar-fill.egreso { background:var(--corp-negative); }
  .bar-fill.origin { background:#0f766e; }
  .month-table th, .month-table td { font-size:.75rem; padding: .35rem .45rem; }
  .report-card { border:1px solid var(--corp-border); border-radius:.55rem; overflow:hidden; box-shadow: 0 1px 2px rgba(15, 58, 104, .05); }
  .report-card .card-header { background: var(--corp-soft); border-bottom:1px solid var(--corp-border); color: var(--corp-primary); }
  .table thead th { background: #f8fbff; color: #1e3a5f; font-weight: 700; border-bottom-color: #dbe5f0; font-size: .74rem; padding: .4rem .5rem; }
  .table tbody td { padding: .36rem .5rem; font-size: .75rem; }
  @media print {
    @page { size: A4 portrait; margin: 8mm; }
    .no-print { display:none !important; }
    body { background:#fff; }
    .report-wrap { padding:0; max-width: 100%; }
    .report-header, .kpi, .chart-card, .report-card { box-shadow: none; }
    .kpi-grid, .chart-grid { break-inside: avoid; page-break-inside: avoid; }
    .report-card { break-inside: auto; page-break-inside: auto; }
  }
</style>

<div class="report-wrap">
  <div class="report-header">
    <div>
      <div class="report-subtitle">Gerencia administrativa · formato de impresión</div>
      <h2 class="report-title">Informe corporativo de rendiciones</h2>
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
      <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url('rendiciones')) ?>">Volver</a>
    </div>
  </div>

  <section class="kpi-grid">
    <article class="kpi"><div class="label">Registros</div><div class="value"><?= (int) ($summary['total_registros'] ?? 0) ?></div></article>
    <article class="kpi"><div class="label">Ingresos</div><div class="value">$<?= number_format($totalIngresos, 0, ',', '.') ?></div></article>
    <article class="kpi"><div class="label">Egresos</div><div class="value">$<?= number_format($totalEgresos, 0, ',', '.') ?></div></article>
    <article class="kpi"><div class="label">Balance</div><div class="value" style="color:<?= $balance >= 0 ? '#166534' : '#b91c1c' ?>">$<?= number_format($balance, 0, ',', '.') ?></div></article>
  </section>

  <section class="chart-grid">
    <article class="chart-card">
      <div class="chart-title">Gráfico 1 · Distribución por tipo de movimiento</div>
      <div class="mini-bars">
        <?php foreach ($byType as $type => $value): ?>
          <?php $width = max(2, (int) round((((float) $value) / $maxType) * 100)); ?>
          <div class="bar-row">
            <div><?= htmlspecialchars(ucfirst((string) $type)) ?></div>
            <div class="bar-track"><div class="bar-fill <?= $type === 'egreso' ? 'egreso' : '' ?>" style="width: <?= $width ?>%"></div></div>
            <div class="text-end">$<?= number_format((float) $value, 0, ',', '.') ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </article>

    <article class="chart-card">
      <div class="chart-title">Gráfico 2 · Composición por origen</div>
      <div class="mini-bars">
        <?php foreach ($originTotals as $origin => $value): ?>
          <?php $width = max(2, (int) round((((float) $value) / $maxOrigin) * 100)); ?>
          <div class="bar-row">
            <div><?= htmlspecialchars((string) $origin) ?></div>
            <div class="bar-track"><div class="bar-fill origin" style="width: <?= $width ?>%"></div></div>
            <div class="text-end">$<?= number_format((float) $value, 0, ',', '.') ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </article>
  </section>

  <section class="card mb-3 report-card">
    <div class="card-header py-2"><strong>Evolución mensual (filtrada)</strong></div>
    <div class="card-body p-0">
      <div class="table-responsive">
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
    </div>
  </section>

  <section class="card report-card">
    <div class="card-header py-2"><strong>Detalle de movimientos filtrados</strong></div>
    <div class="card-body p-0">
      <div class="table-responsive">
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
    </div>
  </section>
</div>
