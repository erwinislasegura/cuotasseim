<main class="landing-page">
  <section class="landing-hero py-5">
    <div class="container py-4">
      <h1 class="display-6 fw-semibold">Sistema de Gestión de Cuotas</h1>
      <p class="lead mb-4">Control transparente de socios, cuotas, pagos, egresos y tesorería.</p>
      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars(url('login')) ?>">Iniciar sesión</a>
        <a class="btn btn-outline-secondary btn-sm" href="#contacto">Solicitar información</a>
      </div>
    </div>
  </section>

  <section class="container py-5">
    <div class="row g-3">
      <div class="col-md-4"><div class="card card-body h-100"><h2 class="h6">Control mensual</h2><p class="small mb-0 text-muted">Cuotas por período y deuda acumulada.</p></div></div>
      <div class="col-md-4"><div class="card card-body h-100"><h2 class="h6">Tesorería</h2><p class="small mb-0 text-muted">Movimientos integrados de ingresos y egresos.</p></div></div>
      <div class="col-md-4"><div class="card card-body h-100"><h2 class="h6">Auditoría</h2><p class="small mb-0 text-muted">Trazabilidad de operaciones críticas.</p></div></div>
    </div>
  </section>
</main>

<footer id="contacto" class="landing-footer py-4 text-center small mt-auto">
  Sistema de Gestión de Cuotas · 2026 · <a href="<?= htmlspecialchars(url('login')) ?>">Login</a>
</footer>
