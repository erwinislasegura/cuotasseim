<?php
$statusOptions = [
  'activo' => 'Activo',
  'inactivo' => 'Inactivo',
  'pendiente' => 'Pendiente',
  'pagada' => 'Pagada',
  'vencida' => 'Vencida',
  'abonada_parcial' => 'Abonada parcial',
  'anulada' => 'Anulada',
  'aplicado' => 'Aplicado',
  'pendiente_revision' => 'Pendiente revisión',
  'conciliado' => 'Conciliado',
  'sin_conciliar' => 'Sin conciliar',
  'rendida' => 'Rendida',
  'cerrada' => 'Cerrada',
  'abierta' => 'Abierta',
  'exenta' => 'Exenta',
];
?>

<section class="page-header d-flex justify-content-between align-items-start mb-3 gap-2 flex-wrap">
  <div>
    <h1 class="mb-1"><?= htmlspecialchars($title ?? 'Módulo') ?></h1>
    <p class="small mb-2"><?= htmlspecialchars($description ?? '') ?></p>
    <nav aria-label="breadcrumb" class="small">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(url('panel')) ?>">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($title ?? 'Módulo') ?></li>
      </ol>
    </nav>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url($route ?? '')) ?>"><i class="bi bi-plus-circle me-1"></i>Nuevo</a>
    <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars(url(($route ?? '') . '?q=' . urlencode((string) ($query ?? '')) . '&status=' . urlencode((string) ($status ?? '')) . '&from=' . urlencode((string) ($from ?? '')) . '&to=' . urlencode((string) ($to ?? '')) . '&export=excel')) ?>"><i class="bi bi-download me-1"></i>Excel</a>
  </div>
</section>

<?php if (!empty($moduleSummary)): ?>
  <div class="module-grid-summary mb-3">
    <article class="quick-summary">
      <div class="label">Registros totales</div>
      <div class="value"><?= (int) ($moduleSummary['total'] ?? 0) ?></div>
    </article>
    <article class="quick-summary">
      <div class="label">Resultados visibles</div>
      <div class="value"><?= (int) (($rows ?? []) ? count($rows) : 0) ?></div>
    </article>
    <article class="quick-summary">
      <div class="label">Página actual</div>
      <div class="value"><?= (int) ($page ?? 1) ?> / <?= (int) ($pages ?? 1) ?></div>
    </article>
    <article class="quick-summary">
      <div class="label">Estado filtrado</div>
      <div class="value"><?= htmlspecialchars($statusOptions[(string) ($status ?? '')] ?? 'Todos') ?></div>
    </article>
  </div>
<?php endif; ?>

<?php if (!empty($statusCounts ?? [])): ?>
  <div class="d-flex flex-wrap gap-2 mb-3">
    <?php foreach ($statusCounts as $state => $count): ?>
      <span class="badge badge-status <?= htmlspecialchars(status_badge_class((string) $state)) ?>"><?= htmlspecialchars(status_label((string) $state)) ?> · <?= (int) $count ?></span>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if (!empty($flashSuccess ?? '')): ?>
  <div class="alert alert-success small mb-3"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError ?? '')): ?>
  <div class="alert alert-danger small mb-3"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>
<?php if (!empty($error ?? '')): ?>
  <div class="alert alert-danger small mb-3"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-header py-2"><strong class="card-title mb-0"><?= !empty($currentRecord) ? 'Editar registro' : 'Crear registro' ?></strong></div>
  <div class="card-body py-3">
    <form class="row g-2" method="post" action="<?= htmlspecialchars(url($route ?? '')) ?>">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
      <input type="hidden" name="_action" value="save">
      <?php if (!empty($currentRecord)): ?>
        <input type="hidden" name="id" value="<?= (int) ($currentRecord[$primaryKey] ?? 0) ?>">
      <?php endif; ?>

      <?php foreach (($formFields ?? []) as $field): ?>
        <?php $value = (string) (($currentRecord[$field] ?? '') ?: ''); ?>
        <div class="col-md-4 col-lg-3">
          <label class="form-label"><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $field))) ?></label>
          <input type="text" name="<?= htmlspecialchars((string) $field) ?>" value="<?= htmlspecialchars($value) ?>" class="form-control form-control-sm" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>
        </div>
      <?php endforeach; ?>

      <div class="col-12 mt-2 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary btn-sm" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>Guardar</button>
        <button type="reset" class="btn btn-light btn-sm">Limpiar</button>
        <a href="<?= htmlspecialchars(url($route ?? '')) ?>" class="btn btn-outline-secondary btn-sm">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($viewRecord)): ?>
  <div class="card mb-3">
    <div class="card-header py-2"><strong class="card-title mb-0">Ficha resumen</strong></div>
    <div class="card-body small">
      <div class="row g-2">
        <?php foreach ($viewRecord as $field => $value): ?>
          <div class="col-md-4"><strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $field))) ?>:</strong> <?= htmlspecialchars((string) $value) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header py-2 d-flex justify-content-between align-items-center gap-2 flex-wrap">
    <strong class="card-title mb-0">Listado de registros</strong>
    <form method="get" action="<?= htmlspecialchars(url($route ?? '')) ?>" class="row gx-2 gy-2 align-items-end">
      <div class="col-sm-auto">
        <label class="form-label">Buscar</label>
        <input type="text" name="q" value="<?= htmlspecialchars($query ?? '') ?>" class="form-control form-control-sm" placeholder="Buscar...">
      </div>
      <div class="col-sm-auto">
        <label class="form-label">Estado</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">Todos</option>
          <?php foreach ($statusOptions as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (string) ($status ?? '') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-auto">
        <label class="form-label">Desde</label>
        <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($from ?? '')) ?>">
      </div>
      <div class="col-sm-auto">
        <label class="form-label">Hasta</label>
        <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($to ?? '')) ?>">
      </div>
      <div class="col-sm-auto d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" type="submit">Filtrar</button>
        <a class="btn btn-light btn-sm" href="<?= htmlspecialchars(url($route ?? '')) ?>">Limpiar</a>
      </div>
    </form>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-striped table-hover mb-0 align-middle">
        <thead>
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
            <tr><td colspan="<?= (int) (count($columns ?? []) + 2) ?>" class="empty-state">Sin registros para mostrar con los filtros aplicados.</td></tr>
          <?php else: ?>
            <?php foreach (($rows ?? []) as $index => $row): ?>
              <tr>
                <td><?= (int) (($page - 1) * 10 + $index + 1) ?></td>
                <?php foreach (($columns ?? []) as $column): ?>
                  <?php $cellValue = (string) ($row[$column] ?? ''); ?>
                  <td>
                    <?php if ($column === ($statusField ?? '__none__')): ?>
                      <?php
                      $normalized = $cellValue;
                      if ($column === 'activo') {
                        $normalized = $cellValue === '1' ? 'activo' : 'inactivo';
                      } elseif ($column === 'cerrado') {
                        $normalized = $cellValue === '1' ? 'cerrada' : 'abierta';
                      }
                      ?>
                      <span class="badge badge-status <?= htmlspecialchars(status_badge_class($normalized)) ?>"><?= htmlspecialchars(status_label($normalized)) ?></span>
                    <?php else: ?>
                      <?= htmlspecialchars($cellValue) ?>
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="<?= htmlspecialchars(url(($route ?? '') . '?view=' . (int) ($row[$primaryKey] ?? 0))) ?>" class="btn btn-light">Ver</a>
                    <a href="<?= htmlspecialchars(url(($route ?? '') . '?edit=' . (int) ($row[$primaryKey] ?? 0))) ?>" class="btn btn-outline-secondary <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>">Editar</a>
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
  <div class="card-footer py-2 d-flex justify-content-between align-items-center small flex-wrap gap-2">
    <span>Total de registros: <strong><?= (int) ($total ?? 0) ?></strong></span>
    <div class="d-flex align-items-center gap-2">
      <?php $prev = max(1, (int) ($page - 1)); $next = min((int) ($pages ?? 1), (int) ($page + 1));
      $queryBase = '?q=' . urlencode((string) ($query ?? '')) . '&status=' . urlencode((string) ($status ?? '')) . '&from=' . urlencode((string) ($from ?? '')) . '&to=' . urlencode((string) ($to ?? ''));
      ?>
      <a class="btn btn-outline-secondary btn-sm <?= ($page <= 1) ? 'disabled' : '' ?>" href="<?= htmlspecialchars(url(($route ?? '') . $queryBase . '&page=' . $prev)) ?>">Anterior</a>
      <span>Página <?= (int) ($page ?? 1) ?> / <?= (int) ($pages ?? 1) ?></span>
      <a class="btn btn-outline-secondary btn-sm <?= ($page >= $pages) ? 'disabled' : '' ?>" href="<?= htmlspecialchars(url(($route ?? '') . $queryBase . '&page=' . $next)) ?>">Siguiente</a>
    </div>
  </div>
</div>
