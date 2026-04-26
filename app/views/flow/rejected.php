<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-danger">
        <div class="card-body">
          <h1 class="h3 text-danger">Pago rechazado</h1>
          <p class="mb-3">El pago no pudo ser completado o fue cancelado.</p>
          <ul class="small mb-3">
            <li><strong>Orden:</strong> <?= htmlspecialchars((string) ($payment['commerceOrder'] ?? '-')) ?></li>
            <li><strong>Monto:</strong> $<?= number_format((float) ($payment['amount'] ?? 0), 0, ',', '.') ?></li>
            <li><strong>Estado Flow:</strong> <?= htmlspecialchars((string) ($status ?? '-')) ?></li>
          </ul>
          <a class="btn btn-primary" href="<?= htmlspecialchars(url('pago-flow')) ?>">Intentar nuevamente</a>
        </div>
      </div>
    </div>
  </div>
</section>
