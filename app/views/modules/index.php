<div class="d-flex justify-content-between align-items-start mb-3">
  <div>
    <h1 class="h4 mb-1"><?= htmlspecialchars($title ?? 'Módulo') ?></h1>
    <p class="text-muted small mb-1"><?= htmlspecialchars($description ?? '') ?></p>
    <nav aria-label="breadcrumb" class="small">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(url('panel')) ?>">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($title ?? 'Módulo') ?></li>
      </ol>
    </nav>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-success btn-sm" href="<?= htmlspecialchars(url(($route ?? '') . '?q=' . urlencode((string) ($query ?? '')) . '&export=excel')) ?>">Exportar Excel</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url($route ?? '')) ?>">Nuevo</a>
  </div>
</div>

<?php if (!empty($flashSuccess ?? '')): ?>
  <div class="alert alert-success small"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError ?? '')): ?>
  <div class="alert alert-danger small"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>
<?php if (!empty($error ?? '')): ?>
  <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
  <div class="card-header py-2"><strong class="small"><?= !empty($currentRecord) ? 'Editar registro' : 'Crear registro' ?></strong></div>
  <div class="card-body py-3">
    <form class="row g-2" method="post" action="<?= htmlspecialchars(url($route ?? '')) ?>">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
      <input type="hidden" name="_action" value="save">
      <?php if (!empty($currentRecord)): ?>
        <input type="hidden" name="id" value="<?= (int) ($currentRecord[$primaryKey] ?? 0) ?>">
      <?php endif; ?>

      <?php foreach (($formFields ?? []) as $field): ?>
        <?php $value = (string) (($currentRecord[$field] ?? '') ?: ''); ?>
        <div class="col-md-4">
          <label class="form-label small mb-1"><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $field))) ?></label>
          <input type="text" name="<?= htmlspecialchars((string) $field) ?>" value="<?= htmlspecialchars($value) ?>" class="form-control form-control-sm" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>
        </div>
      <?php endforeach; ?>

      <div class="col-12 mt-2">
        <button type="submit" class="btn btn-primary btn-sm" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>Guardar</button>
        <button type="reset" class="btn btn-outline-secondary btn-sm">Limpiar</button>
        <a href="<?= htmlspecialchars(url($route ?? '')) ?>" class="btn btn-light btn-sm border">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($viewRecord)): ?>
  <div class="card shadow-sm mb-3">
    <div class="card-header py-2"><strong class="small">Detalle del registro</strong></div>
    <div class="card-body small">
      <div class="row g-2">
        <?php foreach ($viewRecord as $field => $value): ?>
          <div class="col-md-4"><strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $field))) ?>:</strong> <?= htmlspecialchars((string) $value) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-header py-2 d-flex justify-content-between align-items-center gap-2">
    <strong class="small">Listado de registros</strong>
    <form method="get" action="<?= htmlspecialchars(url($route ?? '')) ?>" class="d-flex gap-2">
      <input type="text" name="q" value="<?= htmlspecialchars($query ?? '') ?>" class="form-control form-control-sm" placeholder="Buscar...">
      <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
    </form>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <?php foreach (($columns ?? []) as $column): ?>
              <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $column))) ?></th>
            <?php endforeach; ?>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows ?? [])): ?>
            <tr><td colspan="<?= (int) (count($columns ?? []) + 2) ?>" class="text-center text-muted py-4">Sin registros para mostrar.</td></tr>
          <?php else: ?>
            <?php foreach (($rows ?? []) as $index => $row): ?>
              <tr>
                <td><?= (int) (($page - 1) * 10 + $index + 1) ?></td>
                <?php foreach (($columns ?? []) as $column): ?>
                  <td><?= htmlspecialchars((string) ($row[$column] ?? '')) ?></td>
                <?php endforeach; ?>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= htmlspecialchars(url(($route ?? '') . '?view=' . (int) ($row[$primaryKey] ?? 0))) ?>" class="btn btn-outline-secondary">Ver</a>
                    <a href="<?= htmlspecialchars(url(($route ?? '') . '?edit=' . (int) ($row[$primaryKey] ?? 0))) ?>" class="btn btn-outline-primary <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>">Editar</a>
                    <form method="post" action="<?= htmlspecialchars(url($route ?? '')) ?>" onsubmit="return confirm('¿Deseas eliminar este registro?');" class="d-inline-block">
                      <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                      <input type="hidden" name="_action" value="delete">
                      <input type="hidden" name="id" value="<?= (int) ($row[$primaryKey] ?? 0) ?>">
                      <button type="submit" class="btn btn-outline-danger" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer py-2 d-flex justify-content-between align-items-center small">
    <span>Total de registros: <strong><?= (int) ($total ?? 0) ?></strong></span>
    <div class="d-flex align-items-center gap-2">
      <?php $prev = max(1, (int) ($page - 1)); $next = min((int) ($pages ?? 1), (int) ($page + 1)); ?>
      <a class="btn btn-outline-secondary btn-sm <?= ($page <= 1) ? 'disabled' : '' ?>" href="<?= htmlspecialchars(url(($route ?? '') . '?q=' . urlencode((string) ($query ?? '')) . '&page=' . $prev)) ?>">Anterior</a>
      <span>Página <?= (int) ($page ?? 1) ?> / <?= (int) ($pages ?? 1) ?></span>
      <a class="btn btn-outline-secondary btn-sm <?= ($page >= $pages) ? 'disabled' : '' ?>" href="<?= htmlspecialchars(url(($route ?? '') . '?q=' . urlencode((string) ($query ?? '')) . '&page=' . $next)) ?>">Siguiente</a>
    </div>
  </div>
</div>
