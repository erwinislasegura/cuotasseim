<section class="page-header mb-3">
  <h1 class="mb-1">Panel ejecutivo</h1>
  <p class="small mb-0">Visión consolidada de cobranza, tesorería y operación mensual.</p>
</section>

<div class="row g-3 mb-3">
  <div class="col-md-6 col-xl-3">
    <article class="card kpi-card h-100">
      <div class="card-body">
        <div class="kpi-label">Socios activos</div>
        <div class="kpi-value"><?= (int) ($kpis['socios_activos'] ?? 0) ?></div>
        <div class="kpi-trend"><i class="bi bi-people me-1"></i>Padrón vigente</div>
      </div>
    </article>
  </div>
  <div class="col-md-6 col-xl-3">
    <article class="card kpi-card h-100">
      <div class="card-body">
        <div class="kpi-label">Recaudación del mes</div>
        <div class="kpi-value"><?= money((float) ($kpis['recaudado_mes'] ?? 0)) ?></div>
        <div class="kpi-trend"><i class="bi bi-graph-up-arrow me-1"></i>Pagos aplicados</div>
      </div>
    </article>
  </div>
  <div class="col-md-6 col-xl-3">
    <article class="card kpi-card h-100">
      <div class="card-body">
        <div class="kpi-label">Deuda acumulada</div>
        <div class="kpi-value"><?= money((float) ($kpis['adeudado_total'] ?? 0)) ?></div>
        <div class="kpi-trend"><i class="bi bi-exclamation-circle me-1"></i>Cuotas pendientes</div>
      </div>
    </article>
  </div>
  <div class="col-md-6 col-xl-3">
    <article class="card kpi-card h-100">
      <div class="card-body">
        <div class="kpi-label">Saldo proyectado</div>
        <div class="kpi-value"><?= money((float) ($kpis['saldo_estimado'] ?? 0)) ?></div>
        <div class="kpi-trend"><i class="bi bi-bank me-1"></i>Ingresos - egresos del mes</div>
      </div>
    </article>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong class="card-title mb-0">Flujo últimos 6 meses</strong>
        <span class="small text-muted">Ingresos vs egresos</span>
      </div>
      <div class="card-body small">
        <?php
        $ingresos = [];
        foreach (($series['ingresos'] ?? []) as $item) {
          $ingresos[$item['mes']] = (float) $item['total'];
        }
        $egresos = [];
        foreach (($series['egresos'] ?? []) as $item) {
          $egresos[$item['mes']] = (float) $item['total'];
        }
        $months = array_unique(array_merge(array_keys($ingresos), array_keys($egresos)));
        sort($months);
        $maxValue = 0.0;
        foreach ($months as $month) {
          $maxValue = max($maxValue, $ingresos[$month] ?? 0, $egresos[$month] ?? 0);
        }
        ?>
        <?php if (empty($months)): ?>
          <p class="text-muted mb-0">No hay datos suficientes para el gráfico de flujo.</p>
        <?php else: ?>
          <div class="d-flex flex-column gap-2">
            <?php foreach ($months as $month): ?>
              <?php
              $in = (float) ($ingresos[$month] ?? 0);
              $out = (float) ($egresos[$month] ?? 0);
              $inWidth = $maxValue > 0 ? max(4, (int) round(($in / $maxValue) * 100)) : 0;
              $outWidth = $maxValue > 0 ? max(4, (int) round(($out / $maxValue) * 100)) : 0;
              ?>
              <div>
                <div class="d-flex justify-content-between mb-1">
                  <strong><?= htmlspecialchars($month) ?></strong>
                  <span class="text-muted">Ingreso <?= money($in) ?> · Egreso <?= money($out) ?></span>
                </div>
                <div class="progress" role="progressbar" aria-label="Ingresos" style="height: 9px;">
                  <div class="progress-bar" style="width: <?= $inWidth ?>%; background:#1f7a5f;"></div>
                </div>
                <div class="progress mt-1" role="progressbar" aria-label="Egresos" style="height: 9px;">
                  <div class="progress-bar" style="width: <?= $outWidth ?>%; background:#b74b4b;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><strong class="card-title mb-0">Alertas operativas</strong></div>
      <div class="card-body">
        <?php if (empty($dashboardAlerts ?? [])): ?>
          <div class="alert alert-success mb-0">Sin alertas críticas por el momento.</div>
        <?php else: ?>
          <?php foreach (($dashboardAlerts ?? []) as $message): ?>
            <div class="alert alert-warning mb-2"><?= htmlspecialchars((string) $message) ?></div>
          <?php endforeach; ?>
        <?php endif; ?>
        <div class="d-grid gap-2 mt-3">
          <a href="<?= htmlspecialchars(url('pagos')) ?>" class="btn btn-sm btn-primary">Registrar pago</a>
          <a href="<?= htmlspecialchars(url('cuotas')) ?>" class="btn btn-sm btn-outline-secondary">Revisar cuotas pendientes</a>
          <a href="<?= htmlspecialchars(url('reportes')) ?>" class="btn btn-sm btn-outline-secondary">Ver reportes</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong class="card-title mb-0">Pagos recientes</strong>
        <a href="<?= htmlspecialchars(url('pagos')) ?>" class="btn btn-light btn-sm">Ver módulo</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
            <tr><th>#</th><th>Fecha</th><th>Monto</th><th>Comprobante</th><th>Estado</th></tr>
            </thead>
            <tbody>
            <?php if (empty($recentPayments ?? [])): ?>
              <tr><td colspan="5" class="empty-state">Sin pagos recientes.</td></tr>
            <?php else: ?>
              <?php foreach (($recentPayments ?? []) as $row): ?>
                <tr>
                  <td><?= (int) $row['id'] ?></td>
                  <td><?= htmlspecialchars((string) $row['fecha_pago']) ?></td>
                  <td><?= money((float) $row['monto_total']) ?></td>
                  <td><?= htmlspecialchars((string) ($row['numero_comprobante'] ?? '-')) ?></td>
                  <td><span class="badge badge-status <?= htmlspecialchars(status_badge_class((string) $row['estado_pago'])) ?>"><?= htmlspecialchars(status_label((string) $row['estado_pago'])) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong class="card-title mb-0">Egresos recientes</strong>
        <a href="<?= htmlspecialchars(url('egresos')) ?>" class="btn btn-light btn-sm">Ver módulo</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
            <tr><th>#</th><th>Fecha</th><th>Detalle</th><th>Monto</th><th>Estado</th></tr>
            </thead>
            <tbody>
            <?php if (empty($recentExpenses ?? [])): ?>
              <tr><td colspan="5" class="empty-state">Sin egresos recientes.</td></tr>
            <?php else: ?>
              <?php foreach (($recentExpenses ?? []) as $row): ?>
                <tr>
                  <td><?= (int) $row['id'] ?></td>
                  <td><?= htmlspecialchars((string) $row['fecha']) ?></td>
                  <td><?= htmlspecialchars((string) $row['descripcion']) ?></td>
                  <td><?= money((float) $row['monto']) ?></td>
                  <td><span class="badge badge-status <?= htmlspecialchars(status_badge_class((string) $row['estado'])) ?>"><?= htmlspecialchars(status_label((string) $row['estado'])) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
