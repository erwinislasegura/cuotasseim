<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h5 mb-3">Iniciar sesión</h1>
        <?php require __DIR__ . '/../layouts/partials/alerts.php'; ?>
        <form method="post" action="/login" class="vstack gap-2">
          <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
          <input class="form-control form-control-sm" type="text" name="usuario" placeholder="Usuario o correo" required>
          <input class="form-control form-control-sm" type="password" name="password" placeholder="Contraseña" required>
          <button class="btn btn-primary btn-sm" type="submit">Ingresar</button>
          <a href="/" class="small">Volver al inicio</a>
        </form>
      </div>
    </div>
  </div>
</div>
