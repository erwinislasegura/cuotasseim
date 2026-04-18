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
        <?php
          $rawFieldValue = $currentRecord[$field] ?? '';
          $value = is_array($rawFieldValue) ? '' : (string) ($rawFieldValue ?: '');
        ?>
        <?php $fieldType = (string) (($formMeta['types'][$field] ?? 'text')); ?>
        <?php $isReadOnlyField = (bool) (($formMeta['readonly'][$field] ?? false) || ($isReadOnly ?? false)); ?>
        <?php $fieldOptions = $formMeta['options'][$field] ?? null; ?>
        <?php $isMultipleField = (bool) ($formMeta['multiple'][$field] ?? false); ?>
        <?php $fieldLabel = (string) (($formMeta['labels'][$field] ?? ucwords(str_replace('_', ' ', (string) $field)))); ?>
        <?php
          $fieldClass = 'col-md-4 col-lg-3';
          if (($route ?? '') === 'socios') {
            if (in_array((string) $field, ['direccion', 'observaciones', 'planes_ids'], true)) {
              $fieldClass = 'col-12';
            } elseif (in_array((string) $field, ['nombres', 'apellidos', 'correo'], true)) {
              $fieldClass = 'col-md-6 col-lg-4';
            }
          }
        ?>
        <div class="<?= htmlspecialchars($fieldClass) ?>">
          <label class="form-label"><?= htmlspecialchars($fieldLabel) ?></label>
          <?php if (is_array($fieldOptions)): ?>
            <?php
              $selectedValues = [];
              if ($isMultipleField) {
                $rawValues = $currentRecord[$field] ?? [];
                if (!is_array($rawValues)) {
                  $rawValues = $rawValues === null || $rawValues === '' ? [] : [(string) $rawValues];
                }
                $selectedValues = array_map(static fn($item): string => (string) $item, $rawValues);
              }
            ?>
            <select name="<?= htmlspecialchars((string) $field) ?><?= $isMultipleField ? '[]' : '' ?>" class="form-select form-select-sm" <?= $isReadOnlyField ? 'disabled' : '' ?> <?= $isMultipleField ? 'multiple size=\"5\"' : '' ?>>
              <?php if (!$isMultipleField): ?>
                <option value="">Seleccionar...</option>
              <?php endif; ?>
              <?php foreach ($fieldOptions as $option): ?>
                <?php
                  $optionValue = (string) ($option['value'] ?? '');
                  $isSelected = $isMultipleField
                    ? in_array($optionValue, $selectedValues, true)
                    : $value === $optionValue;
                ?>
                <option value="<?= htmlspecialchars($optionValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                  <?= htmlspecialchars((string) ($option['label'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if ($isMultipleField): ?>
              <small class="text-muted">Puedes seleccionar más de un plan (Ctrl/Cmd + clic).</small>
            <?php endif; ?>
          <?php else: ?>
            <input type="<?= htmlspecialchars($fieldType) ?>" name="<?= htmlspecialchars((string) $field) ?>" value="<?= htmlspecialchars($value) ?>" class="form-control form-control-sm" <?= $isReadOnlyField ? 'readonly' : '' ?> <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>
          <?php endif; ?>
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

<?php if (($route ?? '') === 'socios'): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const nombres = document.querySelector('input[name="nombres"]');
      const apellidos = document.querySelector('input[name="apellidos"]');
      const nombreCompleto = document.querySelector('input[name="nombre_completo"]');

      if (!nombres || !apellidos || !nombreCompleto) {
        return;
      }

      const actualizarNombreCompleto = function () {
        const fullName = [nombres.value.trim(), apellidos.value.trim()].filter(Boolean).join(' ');
        nombreCompleto.value = fullName;
      };

      nombres.addEventListener('input', actualizarNombreCompleto);
      apellidos.addEventListener('input', actualizarNombreCompleto);
      actualizarNombreCompleto();
    });
  </script>
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
    <div class="table-responsive module-table-responsive">
      <table class="table table-sm table-striped table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>#</th>
            <?php foreach (($columns ?? []) as $column): ?>
              <?php $columnLabel = (string) (($columnLabels[$column] ?? ucwords(str_replace('_', ' ', (string) $column)))); ?>
              <th><?= htmlspecialchars($columnLabel) ?></th>
            <?php endforeach; ?>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows ?? [])): ?>
            <tr><td colspan="<?= (int) (count($columns ?? []) + 2) ?>" class="empty-state">Sin registros para mostrar con los filtros aplicados.</td></tr>
          <?php else: ?>
            <?php foreach (($rows ?? []) as $index => $row): ?>
              <?php $displayRow = $displayRows[$index] ?? $row; ?>
              <tr>
                <td><?= (int) (($page - 1) * 10 + $index + 1) ?></td>
                <?php foreach (($columns ?? []) as $column): ?>
                  <?php $cellValue = (string) ($displayRow[$column] ?? $row[$column] ?? ''); ?>
                  <td>
                    <?php if ($column === ($statusField ?? '__none__')): ?>
                      <?php
                      $normalized = (string) ($row[$column] ?? '');
                      if ($column === 'activo') {
                        $normalized = $normalized === '1' ? 'activo' : 'inactivo';
                      } elseif ($column === 'cerrado') {
                        $normalized = $normalized === '1' ? 'cerrada' : 'abierta';
                      }
                      ?>
                      <span class="badge badge-status <?= htmlspecialchars(status_badge_class($normalized)) ?>"><?= htmlspecialchars(status_label($normalized)) ?></span>
                    <?php else: ?>
                      <?= htmlspecialchars($cellValue) ?>
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
                <td class="text-end">
                  <?php
                    $recordDetails = [];
                    foreach (($displayRow ?? []) as $field => $value) {
                      $recordDetails[] = [
                        'label' => (string) ($columnLabels[$field] ?? ucwords(str_replace('_', ' ', (string) $field))),
                        'value' => (string) $value,
                      ];
                    }
                    $recordDetailsJson = htmlspecialchars((string) json_encode($recordDetails, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
                  ?>
                  <div class="dropdown table-actions-dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle table-actions-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Acciones
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <button
                          type="button"
                          class="dropdown-item js-open-record-modal"
                          data-bs-toggle="modal"
                          data-bs-target="#recordDetailModal"
                          data-record='<?= $recordDetailsJson ?>'>
                          Ver detalle
                        </button>
                      </li>
                      <li>
                        <a href="<?= htmlspecialchars(url(($route ?? '') . '?edit=' . (int) ($row[$primaryKey] ?? 0))) ?>" class="dropdown-item <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>">Editar</a>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <form method="post" action="<?= htmlspecialchars(url($route ?? '')) ?>" onsubmit="return confirm('¿Deseas eliminar este registro?');">
                          <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
                          <input type="hidden" name="_action" value="delete">
                          <input type="hidden" name="id" value="<?= (int) ($row[$primaryKey] ?? 0) ?>">
                          <button type="submit" class="dropdown-item text-danger" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>Eliminar</button>
                        </form>
                      </li>
                    </ul>
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

<div class="modal fade" id="recordDetailModal" tabindex="-1" aria-labelledby="recordDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content modal-detail-content border-0 shadow-sm">
      <div class="modal-header modal-detail-header">
        <div>
          <h2 class="modal-title fs-6 fw-semibold mb-1" id="recordDetailModalLabel">
            <i class="bi bi-card-list me-1"></i>Detalle del registro
          </h2>
          <p class="modal-detail-subtitle mb-0"><?= htmlspecialchars((string) ($title ?? 'Módulo')) ?></p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body modal-detail-body">
        <section id="recordDetailSummary" class="record-detail-summary mb-2"></section>
        <div class="row g-2" id="recordDetailModalBody"></div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('recordDetailModal');
    const detailBody = document.getElementById('recordDetailModalBody');
    const detailSummary = document.getElementById('recordDetailSummary');
    if (!detailModal || !detailBody || !detailSummary) {
      return;
    }

    if (window.bootstrap) {
      document.querySelectorAll('.table-actions-dropdown-toggle').forEach(function (toggle) {
        new window.bootstrap.Dropdown(toggle, {
          popperConfig: function (defaultConfig) {
            const baseModifiers = Array.isArray(defaultConfig.modifiers) ? defaultConfig.modifiers : [];
            return Object.assign({}, defaultConfig, {
              strategy: 'fixed',
              modifiers: baseModifiers.concat([
                {
                  name: 'preventOverflow',
                  options: {
                    boundary: document.body
                  }
                },
                {
                  name: 'flip',
                  options: {
                    fallbackPlacements: ['top-end', 'bottom-end']
                  }
                }
              ])
            });
          }
        });
      });
    }

    const escapeHtml = function (value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    };

    const renderRecordSummary = function (details) {
      const summaryFields = details.filter(function (item) {
        const label = String(item && item.label ? item.label : '').toLowerCase();
        return ['id', 'numero', 'nombre', 'estado', 'fecha'].some(function (keyword) {
          return label.indexOf(keyword) !== -1;
        });
      }).slice(0, 3);

      if (summaryFields.length === 0) {
        detailSummary.innerHTML = '';
        return;
      }

      detailSummary.innerHTML = summaryFields.map(function (item) {
        return '<div class="record-summary-chip"><span class="record-summary-chip-label">' + escapeHtml(item.label) + '</span><span class="record-summary-chip-value">' + escapeHtml(item.value || '-') + '</span></div>';
      }).join('');
    };

    const renderRecordDetails = function (payload) {
      const details = Array.isArray(payload) ? payload : [];
      const compactDetails = details.filter(function (item) {
        if (!item) {
          return false;
        }
        const rawValue = item.value === null || item.value === undefined ? '' : String(item.value).trim();
        if (rawValue === '' || rawValue === '-') {
          return false;
        }

        const rawLabel = String(item.label || '').toLowerCase();
        const technicalFields = ['created at', 'updated at', 'deleted at'];
        if (technicalFields.some(function (field) { return rawLabel.indexOf(field) !== -1; })) {
          return false;
        }

        return true;
      });
      renderRecordSummary(compactDetails);
      if (compactDetails.length === 0) {
        detailBody.innerHTML = '<div class="col-12"><div class="record-detail-item text-muted">No hay datos para mostrar.</div></div>';
        return;
      }

      detailBody.innerHTML = compactDetails.map(function (item) {
        const label = escapeHtml(item && item.label ? item.label : '');
        const value = escapeHtml(item && item.value ? item.value : '-');
        return '<div class="col-12 col-md-6"><article class="record-detail-item"><div class="record-detail-label">' + label + '</div><div class="record-detail-value">' + value + '</div></article></div>';
      }).join('');
    };

    document.querySelectorAll('.js-open-record-modal').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        const rawRecord = trigger.getAttribute('data-record');
        try {
          renderRecordDetails(JSON.parse(rawRecord || '[]'));
        } catch (error) {
          renderRecordDetails([]);
        }
      });
    });

    <?php if (!empty($viewRecordDisplay)): ?>
      const initialRecord = <?= json_encode(array_map(static function ($field, $value) use ($columnLabels): array {
          return [
            'label' => (string) ($columnLabels[$field] ?? ucwords(str_replace('_', ' ', (string) $field))),
            'value' => (string) $value,
          ];
      }, array_keys($viewRecordDisplay), $viewRecordDisplay), JSON_UNESCAPED_UNICODE) ?>;
      renderRecordDetails(initialRecord);
      if (window.bootstrap) {
        const modalInstance = new window.bootstrap.Modal(detailModal);
        modalInstance.show();
      }
    <?php endif; ?>
  });
</script>
