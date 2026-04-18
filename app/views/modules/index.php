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
  'ingreso' => 'Ingreso',
  'egreso' => 'Egreso',
];

$isPaymentHistory = ($route ?? '') === 'pagos';
$extraFilters = is_array($extraFilters ?? null) ? $extraFilters : [];
$extraQueryParams = is_array($extraQueryParams ?? null) ? $extraQueryParams : [];
$exportQueryParams = array_merge([
  'q' => (string) ($query ?? ''),
  'status' => (string) ($status ?? ''),
  'from' => (string) ($from ?? ''),
  'to' => (string) ($to ?? ''),
  'export' => 'excel',
], $extraQueryParams);
$reportQueryParams = array_merge([
  'q' => (string) ($query ?? ''),
  'status' => (string) ($status ?? ''),
  'from' => (string) ($from ?? ''),
  'to' => (string) ($to ?? ''),
  'report' => 'print',
], $extraQueryParams);
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
    <?php if (!$isPaymentHistory && !($isReadOnly ?? false)): ?>
      <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(url($route ?? '')) ?>"><i class="bi bi-plus-circle me-1"></i>Nuevo</a>
    <?php endif; ?>
    <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars(url(($route ?? '') . '?' . http_build_query($exportQueryParams))) ?>"><i class="bi bi-download me-1"></i>Excel</a>
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
    <?php if (($route ?? '') === 'rendiciones'): ?>
      <article class="quick-summary">
        <div class="label">Ingresos (filtrados)</div>
        <div class="value">$<?= number_format((float) ($moduleSummary['total_ingresos'] ?? 0), 0, ',', '.') ?></div>
      </article>
      <article class="quick-summary">
        <div class="label">Egresos (filtrados)</div>
        <div class="value">$<?= number_format((float) ($moduleSummary['total_egresos'] ?? 0), 0, ',', '.') ?></div>
      </article>
      <article class="quick-summary">
        <div class="label">Balance</div>
        <?php $balanceResumen = (float) ($moduleSummary['balance'] ?? 0); ?>
        <div class="value" style="color:<?= $balanceResumen >= 0 ? '#15803d' : '#b91c1c' ?>">
          $<?= number_format($balanceResumen, 0, ',', '.') ?>
        </div>
      </article>
    <?php endif; ?>
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

<?php if (!$isPaymentHistory && !($isReadOnly ?? false)): ?>
  <div class="card mb-3">
    <div class="card-header py-2"><strong class="card-title mb-0"><?= !empty($currentRecord) ? 'Editar registro' : 'Crear registro' ?></strong></div>
    <div class="card-body py-3">
      <form class="row g-2" method="post" action="<?= htmlspecialchars(url($route ?? '')) ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($token ?? '') ?>">
        <input type="hidden" name="_action" value="save">
        <?php if (!empty($currentRecord)): ?>
          <input type="hidden" name="id" value="<?= (int) ($currentRecord[$primaryKey] ?? 0) ?>">
        <?php endif; ?>

        <?php if (($route ?? '') === 'egresos'): ?>
          <div class="col-md-4">
            <label class="form-label">¿Quién retira?</label>
            <select name="retirante_tipo" id="retiranteTipo" class="form-select form-select-sm" required>
              <option value="socio">Socio</option>
              <option value="tercero">Tercero</option>
            </select>
          </div>
          <div class="col-md-8" id="retiranteSocioWrap">
            <label class="form-label">Seleccionar socio (Nombre · RUT)</label>
            <select name="retirante_socio_id" id="retiranteSocioId" class="form-select form-select-sm">
              <option value="">Seleccionar socio...</option>
              <?php foreach (($formMeta['retirante_socios_options'] ?? []) as $option): ?>
                <option value="<?= htmlspecialchars((string) ($option['value'] ?? '')) ?>"><?= htmlspecialchars((string) ($option['label'] ?? '')) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 d-none" id="retiranteSocioInfo">
            <div class="alert alert-light border small mb-0">
              <div class="fw-semibold mb-2">Datos del socio que retira</div>
              <div class="row g-2">
                <div class="col-md-6"><span class="text-muted">Nombre:</span> <span data-egreso-socio-field="nombre_completo">-</span></div>
                <div class="col-md-6"><span class="text-muted">RUT:</span> <span data-egreso-socio-field="rut">-</span></div>
                <div class="col-md-6"><span class="text-muted">N° Socio:</span> <span data-egreso-socio-field="numero_socio">-</span></div>
                <div class="col-md-6"><span class="text-muted">Teléfono:</span> <span data-egreso-socio-field="telefono">-</span></div>
                <div class="col-12"><span class="text-muted">Correo:</span> <span data-egreso-socio-field="correo">-</span></div>
              </div>
            </div>
          </div>
          <div class="col-md-6 d-none" id="retiranteNombreWrap">
            <label class="form-label">Nombre del tercero</label>
            <input type="text" name="retirante_nombre" id="retiranteNombre" class="form-control form-control-sm" placeholder="Nombre completo">
          </div>
          <div class="col-md-6 d-none" id="retiranteRutWrap">
            <label class="form-label">RUT del tercero</label>
            <input type="text" name="retirante_rut" id="retiranteRut" class="form-control form-control-sm" placeholder="12.345.678-9">
          </div>
          <div class="col-md-4">
            <label class="form-label">Forma de retiro</label>
            <select name="forma_retiro" id="formaRetiro" class="form-select form-select-sm" required>
              <option value="">Seleccionar...</option>
              <?php foreach (($formMeta['forma_retiro_options'] ?? []) as $option): ?>
                <?php $selectedFormaRetiro = (string) (($currentRecord['_forma_retiro'] ?? '') ?: ''); ?>
                <option value="<?= htmlspecialchars((string) ($option['value'] ?? '')) ?>" <?= $selectedFormaRetiro === (string) ($option['value'] ?? '') ? 'selected' : '' ?>>
                  <?= htmlspecialchars((string) ($option['label'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
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

            if (($route ?? '') === 'aportes') {
              if ((string) $field === 'socio_id') {
                $fieldClass = 'col-md-8';
              } elseif ((string) $field === 'monto') {
                $fieldClass = 'col-md-4';
              } elseif ((string) $field === 'comentario') {
                $fieldClass = 'col-12';
              }
            }

            if (($route ?? '') === 'egresos') {
              if ((string) $field === 'proveedor_destinatario') {
                $fieldClass = 'd-none';
              } elseif ((string) $field === 'descripcion') {
                $fieldClass = 'col-12';
              } elseif ((string) $field === 'tipo_egreso_id') {
                $fieldClass = 'col-md-6';
              } elseif (in_array((string) $field, ['numero_documento', 'monto'], true)) {
                $fieldClass = 'col-md-4';
              }
            }
          ?>
          <?php
            $isRequiredField = (bool) ($formMeta['required'][$field] ?? false);
            $fieldAttributes = $formMeta['attributes'][$field] ?? [];
            $attributeString = '';
            foreach ($fieldAttributes as $attributeName => $attributeValue) {
              $attributeString .= ' ' . htmlspecialchars((string) $attributeName) . '="' . htmlspecialchars((string) $attributeValue) . '"';
            }
          ?>
          <div class="<?= htmlspecialchars($fieldClass) ?>" data-flow-order="<?= htmlspecialchars((string) $field) ?>">
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
              <select name="<?= htmlspecialchars((string) $field) ?><?= $isMultipleField ? '[]' : '' ?>" class="form-select form-select-sm" <?= $isReadOnlyField ? 'disabled' : '' ?> <?= $isMultipleField ? 'multiple size=\"5\"' : '' ?> <?= $isRequiredField ? 'required' : '' ?><?= $attributeString ?>>
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
              <?php if ($fieldType === 'textarea'): ?>
                <textarea name="<?= htmlspecialchars((string) $field) ?>" class="form-control form-control-sm" rows="3" <?= $isReadOnlyField ? 'readonly' : '' ?> <?= ($isReadOnly ?? false) ? 'disabled' : '' ?> <?= $isRequiredField ? 'required' : '' ?><?= $attributeString ?>><?= htmlspecialchars($value) ?></textarea>
              <?php else: ?>
                <input type="<?= htmlspecialchars($fieldType) ?>" name="<?= htmlspecialchars((string) $field) ?>" value="<?= htmlspecialchars($value) ?>" class="form-control form-control-sm" <?= $isReadOnlyField ? 'readonly' : '' ?> <?= ($isReadOnly ?? false) ? 'disabled' : '' ?> <?= $isRequiredField ? 'required' : '' ?><?= $attributeString ?>>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <?php if (($route ?? '') === 'egresos'): ?>
          <div class="col-12">
            <div class="egreso-flow-hint small">
              <i class="bi bi-diagram-3 me-1"></i>
              Flujo recomendado: tipo de retirante → datos de quien retira → forma de retiro → fecha → tipo → motivo → comprobante → monto.
              Presiona <kbd>Enter</kbd> para avanzar al siguiente campo.
            </div>
            <div id="egresoMontoPreview" class="small text-muted mt-2"></div>
          </div>
        <?php endif; ?>

        <div class="col-12 mt-2 d-flex gap-2 flex-wrap">
          <button type="submit" class="btn btn-primary btn-sm" <?= ($isReadOnly ?? false) ? 'disabled' : '' ?>>Guardar</button>
          <button type="reset" class="btn btn-light btn-sm">Limpiar</button>
          <a href="<?= htmlspecialchars(url($route ?? '')) ?>" class="btn btn-outline-secondary btn-sm">Cancelar</a>
        </div>
      </form>

      <?php if (($route ?? '') === 'aportes'): ?>
        <div id="aporteSocioInfo" class="alert alert-light border small mt-3 mb-0 d-none">
          <div class="fw-semibold mb-2">Datos del socio seleccionado</div>
          <div class="row g-2">
            <div class="col-md-6"><span class="text-muted">Nombre:</span> <span data-socio-field="nombre_completo">-</span></div>
            <div class="col-md-6"><span class="text-muted">N° Socio:</span> <span data-socio-field="numero_socio">-</span></div>
            <div class="col-md-6"><span class="text-muted">RUT:</span> <span data-socio-field="rut">-</span></div>
            <div class="col-md-6"><span class="text-muted">Teléfono:</span> <span data-socio-field="telefono">-</span></div>
            <div class="col-12"><span class="text-muted">Correo:</span> <span data-socio-field="correo">-</span></div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

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

<?php if (($route ?? '') === 'aportes'): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const socioSelect = document.querySelector('select[name="socio_id"]');
      const socioInfo = document.getElementById('aporteSocioInfo');
      const sociosData = <?= json_encode(($formMeta['socios_data'] ?? []), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) ?>;

      if (!socioSelect || !socioInfo) {
        return;
      }

      const updateSocioInfo = function () {
        const socioId = socioSelect.value;
        const socio = sociosData[socioId] || null;

        if (!socio) {
          socioInfo.classList.add('d-none');
          return;
        }

        socioInfo.classList.remove('d-none');
        socioInfo.querySelectorAll('[data-socio-field]').forEach(function (node) {
          const field = node.getAttribute('data-socio-field');
          const value = socio[field] || '';
          node.textContent = value !== '' ? value : '-';
        });
      };

      socioSelect.addEventListener('change', updateSocioInfo);
      updateSocioInfo();
    });
  </script>
<?php endif; ?>

<?php if (($route ?? '') === 'egresos'): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.querySelector('form[action$="/egresos"]');
      if (!form) {
        return;
      }

      const retiranteTipo = document.getElementById('retiranteTipo');
      const retiranteSocioWrap = document.getElementById('retiranteSocioWrap');
      const retiranteSocioId = document.getElementById('retiranteSocioId');
      const retiranteSocioInfo = document.getElementById('retiranteSocioInfo');
      const retiranteNombreWrap = document.getElementById('retiranteNombreWrap');
      const retiranteRutWrap = document.getElementById('retiranteRutWrap');
      const retiranteNombre = document.getElementById('retiranteNombre');
      const retiranteRut = document.getElementById('retiranteRut');
      const proveedorDestinatario = form.querySelector('[name="proveedor_destinatario"]');
      const sociosData = <?= json_encode(($formMeta['retirante_socios_data'] ?? []), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) ?>;

      const flowFields = [
        'retirante_tipo',
        'retirante_socio_id',
        'retirante_nombre',
        'retirante_rut',
        'forma_retiro',
        'fecha',
        'tipo_egreso_id',
        'descripcion',
        'numero_documento',
        'monto'
      ];

      const focusNextField = function (currentName) {
        const currentIndex = flowFields.indexOf(currentName);
        if (currentIndex < 0) {
          return;
        }
        const nextName = flowFields[currentIndex + 1];
        if (!nextName) {
          return;
        }
        const nextField = form.querySelector('[name="' + nextName + '"]');
        if (nextField && !nextField.disabled && !nextField.readOnly) {
          nextField.focus();
        }
      };

      flowFields.forEach(function (fieldName) {
        const field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) {
          return;
        }

        field.addEventListener('keydown', function (event) {
          if (event.key === 'Enter' && field.tagName !== 'TEXTAREA') {
            event.preventDefault();
            focusNextField(fieldName);
          }
        });
      });

      const syncSocioInfo = function () {
        if (!retiranteSocioId || !retiranteSocioInfo) {
          return;
        }
        const socioId = retiranteSocioId.value;
        const socio = sociosData[socioId] || null;
        if (!socio) {
          retiranteSocioInfo.classList.add('d-none');
          return;
        }
        retiranteSocioInfo.classList.remove('d-none');
        retiranteSocioInfo.querySelectorAll('[data-egreso-socio-field]').forEach(function (node) {
          const field = node.getAttribute('data-egreso-socio-field');
          const value = socio[field] || '';
          node.textContent = value !== '' ? value : '-';
        });
      };

      const syncRetiranteMode = function () {
        const isSocio = !retiranteTipo || retiranteTipo.value === 'socio';

        if (retiranteSocioWrap) {
          retiranteSocioWrap.classList.toggle('d-none', !isSocio);
        }
        if (retiranteSocioInfo) {
          if (!isSocio) {
            retiranteSocioInfo.classList.add('d-none');
          } else {
            syncSocioInfo();
          }
        }
        if (retiranteNombreWrap) {
          retiranteNombreWrap.classList.toggle('d-none', isSocio);
        }
        if (retiranteRutWrap) {
          retiranteRutWrap.classList.toggle('d-none', isSocio);
        }
        if (retiranteSocioId) {
          retiranteSocioId.required = isSocio;
        }
        if (retiranteNombre) {
          retiranteNombre.required = !isSocio;
        }
        if (retiranteRut) {
          retiranteRut.required = !isSocio;
        }
      };

      if (retiranteTipo) {
        retiranteTipo.addEventListener('change', syncRetiranteMode);
      }
      if (retiranteSocioId) {
        retiranteSocioId.addEventListener('change', syncSocioInfo);
      }
      syncRetiranteMode();

      const fecha = form.querySelector('[name="fecha"]');
      if (fecha && !fecha.value) {
        fecha.value = new Date().toISOString().slice(0, 10);
      }

      const montoInput = form.querySelector('[name="monto"]');
      const montoPreview = document.getElementById('egresoMontoPreview');
      const renderMontoPreview = function () {
        if (!montoInput || !montoPreview) {
          return;
        }
        const value = Number(montoInput.value || 0);
        if (value <= 0) {
          montoPreview.textContent = '';
          return;
        }
        const formatted = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(value);
        montoPreview.textContent = 'Monto con formato: ' + formatted;
      };

      if (montoInput) {
        montoInput.addEventListener('input', renderMontoPreview);
        renderMontoPreview();
      }

      form.addEventListener('submit', function (event) {
        if (!proveedorDestinatario) {
          return;
        }

        const isSocio = !retiranteTipo || retiranteTipo.value === 'socio';
        if (isSocio) {
          const socioId = retiranteSocioId ? retiranteSocioId.value : '';
          const socio = sociosData[socioId] || null;
          if (!socio) {
            event.preventDefault();
            if (retiranteSocioId) {
              retiranteSocioId.focus();
            }
            return;
          }
          const nombre = String(socio.nombre_completo || '').trim();
          const rut = String(socio.rut || '').trim();
          proveedorDestinatario.value = rut !== '' ? (nombre + ' · ' + rut) : nombre;
          return;
        }

        const nombreTercero = retiranteNombre ? retiranteNombre.value.trim() : '';
        const rutTercero = retiranteRut ? retiranteRut.value.trim() : '';
        if (nombreTercero === '' || rutTercero === '') {
          event.preventDefault();
          if (nombreTercero === '' && retiranteNombre) {
            retiranteNombre.focus();
          } else if (retiranteRut) {
            retiranteRut.focus();
          }
          return;
        }
        proveedorDestinatario.value = nombreTercero + ' · ' + rutTercero;
      });

      const firstField = form.querySelector('[name="retirante_tipo"], [name="retirante_socio_id"], [name="fecha"]');
      if (firstField) {
        firstField.focus();
      }
    });
  </script>
<?php endif; ?>

<div class="card">
  <div class="card-header py-2 d-flex justify-content-between align-items-center gap-2 flex-wrap">
    <strong class="card-title mb-0"><?= $isPaymentHistory ? 'Historial de pagos' : 'Listado de registros' ?></strong>
    <?php if (($route ?? '') === 'rendiciones'): ?>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="<?= htmlspecialchars(url(($route ?? '') . '?' . http_build_query($exportQueryParams))) ?>">
          <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
        </a>
        <a class="btn btn-outline-dark btn-sm" target="_blank" href="<?= htmlspecialchars(url(($route ?? '') . '?' . http_build_query($reportQueryParams))) ?>">
          <i class="bi bi-printer me-1"></i>Imprimir informe
        </a>
      </div>
    <?php endif; ?>
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
      <?php if (($route ?? '') === 'rendiciones'): ?>
        <div class="col-sm-auto">
          <label class="form-label">Periodo</label>
          <select name="periodo" class="form-select form-select-sm">
            <?php foreach (($formMeta['rendiciones_filter_options']['periodos'] ?? []) as $periodOption): ?>
              <?php $periodValue = (string) ($periodOption['value'] ?? ''); ?>
              <option value="<?= htmlspecialchars($periodValue) ?>" <?= (string) ($extraFilters['periodo'] ?? '') === $periodValue ? 'selected' : '' ?>>
                <?= htmlspecialchars((string) ($periodOption['label'] ?? '')) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-auto">
          <label class="form-label">Socio</label>
          <select name="socio_id" class="form-select form-select-sm">
            <option value="">Todos</option>
            <?php foreach (($formMeta['rendiciones_filter_options']['socios'] ?? []) as $socioOption): ?>
              <?php $socioValue = (string) ($socioOption['value'] ?? ''); ?>
              <option value="<?= htmlspecialchars($socioValue) ?>" <?= (string) ($extraFilters['socio_id'] ?? '') === $socioValue ? 'selected' : '' ?>>
                <?= htmlspecialchars((string) ($socioOption['label'] ?? '')) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-sm-auto">
          <label class="form-label">Monto min</label>
          <input type="number" step="0.01" min="0" name="monto_min" value="<?= htmlspecialchars((string) ($extraFilters['monto_min'] ?? '')) ?>" class="form-control form-control-sm" placeholder="0">
        </div>
        <div class="col-sm-auto">
          <label class="form-label">Monto máx</label>
          <input type="number" step="0.01" min="0" name="monto_max" value="<?= htmlspecialchars((string) ($extraFilters['monto_max'] ?? '')) ?>" class="form-control form-control-sm" placeholder="0">
        </div>
      <?php endif; ?>
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
                    $detailFields = array_values(array_unique(array_filter(array_merge(
                      [(string) ($primaryKey ?? 'id')],
                      array_map(static fn($item): string => (string) $item, ($formFields ?? []))
                    ))));

                    foreach ($detailFields as $field) {
                      $label = (string) ($columnLabels[$field] ?? ucwords(str_replace('_', ' ', $field)));
                      $value = (string) ($displayRow[$field] ?? $row[$field] ?? '');

                      if ($field === 'proveedor_destinatario' && ($route ?? '') === 'egresos') {
                        $label = 'Retirado por / destinatario';
                      }

                      $recordDetails[] = [
                        'label' => $label,
                        'value' => $value,
                      ];
                    }

                    if (($route ?? '') === 'egresos') {
                      $formaRetiro = trim((string) ($row['observacion'] ?? ''));
                      $formaRetiro = preg_replace('/^Forma de retiro:\s*/i', '', $formaRetiro ?? '') ?? '';
                      $recordDetails[] = [
                        'label' => 'Forma de retiro',
                        'value' => $formaRetiro !== '' ? $formaRetiro : '-',
                      ];
                    }
                    $recordDetailsJson = htmlspecialchars((string) json_encode($recordDetails, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
                    $egresoPrintPayload = [];
                    if (($route ?? '') === 'egresos') {
                      $egresoPrintPayload = [
                        'numero_comprobante' => (string) ($row['numero_documento'] ?? ''),
                        'fecha' => (string) ($row['fecha'] ?? ''),
                        'tipo_egreso' => (string) ($displayRow['tipo_egreso_id'] ?? $row['tipo_egreso_id'] ?? ''),
                        'destinatario' => (string) ($row['proveedor_destinatario'] ?? ''),
                        'descripcion' => (string) ($row['descripcion'] ?? ''),
                        'monto' => (string) ($row['monto'] ?? ''),
                        'forma_retiro' => (string) ($row['observacion'] ?? ''),
                      ];
                    }
                    $egresoPrintPayloadJson = htmlspecialchars((string) json_encode($egresoPrintPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8');
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
                      <?php if (!$isPaymentHistory): ?>
                        <?php if (($route ?? '') === 'egresos'): ?>
                          <li>
                            <button
                              type="button"
                              class="dropdown-item js-print-egreso"
                              data-egreso='<?= $egresoPrintPayloadJson ?>'>
                              Imprimir comprobante
                            </button>
                          </li>
                        <?php endif; ?>
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
                      <?php endif; ?>
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

    const formatCurrency = function (rawAmount) {
      const amount = Number(rawAmount || 0);
      if (!Number.isFinite(amount) || amount <= 0) {
        return '-';
      }
      return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(amount);
    };

    const printEgresoVoucher = function (payload) {
      const comprobante = payload && payload.numero_comprobante ? payload.numero_comprobante : '-';
      const fecha = payload && payload.fecha ? payload.fecha : '-';
      const tipoEgreso = payload && payload.tipo_egreso ? payload.tipo_egreso : '-';
      const destinatario = payload && payload.destinatario ? payload.destinatario : '-';
      const descripcion = payload && payload.descripcion ? payload.descripcion : '-';
      const monto = formatCurrency(payload && payload.monto ? payload.monto : 0);
      const formaRetiroRaw = payload && payload.forma_retiro ? payload.forma_retiro : '-';
      const formaRetiro = String(formaRetiroRaw).replace(/^Forma de retiro:\s*/i, '') || '-';
      const fechaImpresion = new Date().toLocaleString('es-CL');
      const organization = <?= json_encode((string) ($_SESSION['app_name'] ?? 'Sistema de Gestión de Cuotas'), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG) ?>;

      const html = '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Comprobante egreso ' + escapeHtml(comprobante) + '</title>' +
        '<style>body{font-family:Arial,sans-serif;color:#203040;margin:24px;}h1{font-size:18px;margin:0 0 6px;}small{color:#5a6d82;}table{width:100%;border-collapse:collapse;margin-top:16px;}th,td{border:1px solid #d7e1eb;padding:8px;text-align:left;font-size:13px;}th{background:#f3f8fd;width:34%;}.header{border-bottom:2px solid #1d4c7d;padding-bottom:8px;margin-bottom:10px;}.amount{font-size:20px;font-weight:700;color:#163a5f;text-align:right;margin-top:12px;}.footer{margin-top:24px;font-size:12px;color:#607487;display:flex;justify-content:space-between;}.sign{margin-top:36px;border-top:1px solid #9eb2c7;padding-top:8px;width:240px;}</style>' +
        '</head><body><div class="header"><h1>Comprobante de Egreso</h1><small>' + escapeHtml(organization) + '</small></div>' +
        '<table><tr><th>N° comprobante</th><td>' + escapeHtml(comprobante) + '</td></tr>' +
        '<tr><th>Fecha</th><td>' + escapeHtml(fecha) + '</td></tr>' +
        '<tr><th>Tipo de egreso</th><td>' + escapeHtml(tipoEgreso) + '</td></tr>' +
        '<tr><th>Retirado por / destinatario</th><td>' + escapeHtml(destinatario) + '</td></tr>' +
        '<tr><th>Descripción</th><td>' + escapeHtml(descripcion) + '</td></tr>' +
        '<tr><th>Forma de retiro</th><td>' + escapeHtml(formaRetiro) + '</td></tr></table>' +
        '<div class="amount">Monto: ' + escapeHtml(monto) + '</div>' +
        '<div class="footer"><span>Impreso: ' + escapeHtml(fechaImpresion) + '</span><span>Documento generado por el sistema.</span></div>' +
        '<div class="sign">Firma responsable</div></body></html>';

      const printWindow = window.open('', '_blank', 'width=900,height=700');
      if (!printWindow) {
        return;
      }

      printWindow.document.open();
      printWindow.document.write(html);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
    };

    document.querySelectorAll('.js-print-egreso').forEach(function (button) {
      button.addEventListener('click', function () {
        const rawPayload = button.getAttribute('data-egreso');
        let payload = {};
        try {
          payload = JSON.parse(rawPayload || '{}');
        } catch (error) {
          payload = {};
        }
        printEgresoVoucher(payload);
      });
    });

    <?php if (!empty($viewRecordDisplay)): ?>
      const initialRecord = <?= json_encode((static function () use ($viewRecordDisplay, $formFields, $columnLabels, $primaryKey, $route): array {
          $details = [];
          $detailFields = array_values(array_unique(array_filter(array_merge(
              [(string) ($primaryKey ?? 'id')],
              array_map(static fn($item): string => (string) $item, ($formFields ?? []))
          ))));

          foreach ($detailFields as $field) {
              $label = (string) ($columnLabels[$field] ?? ucwords(str_replace('_', ' ', $field)));
              $value = (string) ($viewRecordDisplay[$field] ?? '');
              if ($field === 'proveedor_destinatario' && (string) ($route ?? '') === 'egresos') {
                  $label = 'Retirado por / destinatario';
              }

              $details[] = [
                  'label' => $label,
                  'value' => $value,
              ];
          }

          if ((string) ($route ?? '') === 'egresos') {
              $formaRetiro = trim((string) ($viewRecordDisplay['observacion'] ?? ''));
              $formaRetiro = preg_replace('/^Forma de retiro:\s*/i', '', $formaRetiro ?? '') ?? '';
              $details[] = [
                  'label' => 'Forma de retiro',
                  'value' => $formaRetiro !== '' ? $formaRetiro : '-',
              ];
          }

          return $details;
      })(), JSON_UNESCAPED_UNICODE) ?>;
      renderRecordDetails(initialRecord);
      if (window.bootstrap) {
        const modalInstance = new window.bootstrap.Modal(detailModal);
        modalInstance.show();
      }
    <?php endif; ?>
  });
</script>
