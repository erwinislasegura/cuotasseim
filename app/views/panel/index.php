<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card card-body"><span class="small text-muted">Socios activos</span><strong><?= (int) $kpis['socios_activos'] ?></strong></div></div>
  <div class="col-md-3"><div class="card card-body"><span class="small text-muted">Recaudado mes</span><strong><?= money($kpis['recaudado_mes']) ?></strong></div></div>
  <div class="col-md-3"><div class="card card-body"><span class="small text-muted">Adeudado total</span><strong><?= money($kpis['adeudado_total']) ?></strong></div></div>
  <div class="col-md-3"><div class="card card-body"><span class="small text-muted">Saldo estimado</span><strong><?= money($kpis['saldo_estimado']) ?></strong></div></div>
</div>
<div class="card">
  <div class="card-header">Resumen operativo</div>
  <div class="card-body small">Base preparada para módulos: socios, cuotas, pagos, egresos, tesorería, reportes y auditoría.</div>
</div>
