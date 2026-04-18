<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): void
    {
        $target = preg_match('#^https?://#i', $path)
            ? $path
            : url(ltrim($path, '/'));

        header('Location: ' . $target);
        exit;
    }

    protected function renderModule(string $moduleKey): void
    {
        $config = ModuleCatalog::config($moduleKey);

        if ($config === null) {
            http_response_code(404);
            echo 'Módulo no encontrado';
            return;
        }

        Session::start();

        $query = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $from = trim((string) ($_GET['from'] ?? ''));
        $to = trim((string) ($_GET['to'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $extraFilters = [];
        $extraConditions = [];
        $extraQueryParams = [];

        if (($config['table'] ?? '') === 'rendiciones') {
            $periodPreset = trim((string) ($_GET['periodo'] ?? ''));
            $socioId = (int) ($_GET['socio_id'] ?? 0);
            $montoMin = trim((string) ($_GET['monto_min'] ?? ''));
            $montoMax = trim((string) ($_GET['monto_max'] ?? ''));

            if ($periodPreset !== '') {
                $today = new \DateTimeImmutable('today');
                if ($periodPreset === 'mes_actual') {
                    $from = $today->modify('first day of this month')->format('Y-m-d');
                    $to = $today->modify('last day of this month')->format('Y-m-d');
                } elseif ($periodPreset === 'mes_anterior') {
                    $previousMonth = $today->modify('first day of last month');
                    $from = $previousMonth->format('Y-m-d');
                    $to = $previousMonth->modify('last day of this month')->format('Y-m-d');
                } elseif ($periodPreset === 'trimestre_actual') {
                    $month = (int) $today->format('n');
                    $quarterStartMonth = ((int) floor(($month - 1) / 3) * 3) + 1;
                    $quarterStart = new \DateTimeImmutable($today->format('Y') . '-' . str_pad((string) $quarterStartMonth, 2, '0', STR_PAD_LEFT) . '-01');
                    $from = $quarterStart->format('Y-m-d');
                    $to = $quarterStart->modify('+2 months')->modify('last day of this month')->format('Y-m-d');
                } elseif ($periodPreset === 'anio_actual') {
                    $from = $today->modify('first day of january')->format('Y-m-d');
                    $to = $today->modify('last day of december')->format('Y-m-d');
                }
            }

            if ($socioId > 0) {
                $extraConditions[] = [
                    'sql' => '(
                        (origen_modulo = \'pagos\' AND EXISTS (
                            SELECT 1 FROM pagos p
                            WHERE p.id = movimientos_tesoreria.referencia_id
                              AND p.socio_id = :filtro_socio_id
                        ))
                        OR
                        (origen_modulo = \'aportes\' AND EXISTS (
                            SELECT 1 FROM aportes a
                            WHERE a.id = movimientos_tesoreria.referencia_id
                              AND a.socio_id = :filtro_socio_id
                        ))
                        OR
                        (origen_modulo = \'egresos\' AND EXISTS (
                            SELECT 1
                            FROM egresos e
                            INNER JOIN socios s ON s.id = :filtro_socio_id
                            WHERE e.id = movimientos_tesoreria.referencia_id
                              AND (
                                e.proveedor_destinatario LIKE CONCAT(\'%\', COALESCE(s.nombre_completo, \'\'), \'%\')
                                OR (COALESCE(s.rut, \'\') <> \'\' AND e.proveedor_destinatario LIKE CONCAT(\'%\', s.rut, \'%\'))
                              )
                        ))
                    )',
                    'params' => [
                        ':filtro_socio_id' => $socioId,
                    ],
                ];
            }

            if ($montoMin !== '' && is_numeric($montoMin)) {
                $extraConditions[] = [
                    'sql' => 'monto_total >= :filtro_monto_min',
                    'params' => [':filtro_monto_min' => (float) $montoMin],
                ];
            }

            if ($montoMax !== '' && is_numeric($montoMax)) {
                $extraConditions[] = [
                    'sql' => 'monto_total <= :filtro_monto_max',
                    'params' => [':filtro_monto_max' => (float) $montoMax],
                ];
            }

            $extraFilters = [
                'periodo' => $periodPreset,
                'socio_id' => $socioId > 0 ? (string) $socioId : '',
                'monto_min' => $montoMin,
                'monto_max' => $montoMax,
            ];

            foreach ($extraFilters as $key => $value) {
                if ($value === '') {
                    continue;
                }
                $extraQueryParams[$key] = (string) $value;
            }
        }

        try {
            $data = ModuleCatalog::fetchData(
                $config,
                $query,
                $page,
                $perPage,
                $status !== '' ? $status : null,
                $from !== '' ? $from : null,
                $to !== '' ? $to : null,
                $extraConditions
            );
            $columnsMeta = $data['columns'];
            $primaryKey = $columnsMeta['primary'];
            $isReadOnly = (bool) ($config['read_only'] ?? false);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!Csrf::validate($_POST['_token'] ?? null)) {
                    $_SESSION['flash_error'] = 'Token CSRF inválido.';
                    $this->redirect('/' . $config['route']);
                }

                $action = (string) ($_POST['_action'] ?? 'save');
                $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

                if (!$isReadOnly && $action === 'save') {
                    ModuleCatalog::save($config['table'], $primaryKey, $columnsMeta['form'], $_POST, $id > 0 ? $id : null);
                    $_SESSION['flash_success'] = $id ? 'Registro actualizado correctamente.' : 'Registro creado correctamente.';
                }

                if (!$isReadOnly && $action === 'delete' && $id !== null && $id > 0) {
                    ModuleCatalog::delete($config['table'], $primaryKey, $id, $columnsMeta['has_deleted_at']);
                    $_SESSION['flash_success'] = 'Registro eliminado correctamente.';
                }

                $this->redirect('/' . $config['route']);
            }

            if (($_GET['export'] ?? '') === 'excel') {
                ModuleCatalog::exportCsv($config['route'] . '_' . date('Ymd_His') . '.csv', $data['columns']['all'], $data['rows']);
                return;
            }

            $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
            $viewId = isset($_GET['view']) ? (int) $_GET['view'] : null;
            $currentRecord = null;
            $viewRecord = null;
            $viewRecordDisplay = null;
            $formMeta = [];
            $formFields = $data['columns']['form'];
            $columnLabels = [];
            $visibleColumns = $data['columns']['visible'];
            $displayRows = ModuleCatalog::decorateRowsForDisplay($config['table'], $data['rows']);

            if ($editId !== null && $editId > 0) {
                $currentRecord = ModuleCatalog::findById($config['table'], $primaryKey, $editId);
            }

            if ($viewId !== null && $viewId > 0) {
                $viewRecord = ModuleCatalog::findById($config['table'], $primaryKey, $viewId);
                $viewRecordDisplay = ModuleCatalog::decorateRecordForDisplay($config['table'], $viewRecord);
            }


            if ($config['table'] === 'periodos') {
                $formFields = array_values(array_intersect([
                    'nombre_periodo',
                    'tipo_periodo',
                    'monto_a_pagar',
                ], $data['columns']['form']));

                $formMeta = [
                    'types' => [
                        'monto_a_pagar' => 'number',
                    ],
                    'options' => [
                        'tipo_periodo' => [
                            ['value' => 'mensual', 'label' => 'Mensual'],
                            ['value' => 'trimestral', 'label' => 'Trimestral'],
                            ['value' => 'semestral', 'label' => 'Semestral'],
                            ['value' => 'anual', 'label' => 'Anual'],
                        ],
                    ],
                    'labels' => [
                        'nombre_periodo' => 'Nombre del plan',
                        'tipo_periodo' => 'Frecuencia',
                        'monto_a_pagar' => 'Monto a pagar',
                    ],
                ];

                $columnLabels = $formMeta['labels'];
                $visibleColumns = array_values(array_intersect([
                    'id',
                    'nombre_periodo',
                    'tipo_periodo',
                    'monto_a_pagar',
                    'created_at',
                ], $data['columns']['all']));
            }

            if ($config['table'] === 'socios') {
                $availableFormFields = array_values(array_intersect([
                    'numero_socio',
                    'rut',
                    'nombres',
                    'apellidos',
                    'nombre_completo',
                    'fecha_nacimiento',
                    'fecha_ingreso',
                    'telefono',
                    'correo',
                    'direccion',
                    'comuna',
                    'ciudad',
                    'tipo_socio_id',
                    'estado_socio_id',
                    'activo',
                    'observaciones',
                ], $data['columns']['form']));
                if (!empty($availableFormFields)) {
                    $formFields = $availableFormFields;
                }
                $formFields[] = 'planes_ids';
                $formFields = array_values(array_unique($formFields));

                $formMeta = [
                    'types' => [
                        'fecha_nacimiento' => 'date',
                        'fecha_ingreso' => 'date',
                    ],
                    'readonly' => [
                        'numero_socio' => true,
                        'nombre_completo' => true,
                    ],
                    'options' => [
                        'activo' => [
                            ['value' => '1', 'label' => 'Activo'],
                            ['value' => '0', 'label' => 'Desactivado'],
                        ],
                    ],
                    'labels' => [
                        'tipo_socio_id' => 'Tipo socio',
                        'estado_socio_id' => 'Estado socio',
                        'fecha_ingreso' => 'Fecha de inscripción como socio',
                        'planes_ids' => 'Planes asociados',
                    ],
                    'multiple' => [
                        'planes_ids' => true,
                    ],
                ];
                $columnLabels = $formMeta['labels'];

                $tipoSocioStmt = Database::connection()->query('SELECT id, nombre FROM tipos_socio WHERE activo = 1 ORDER BY nombre ASC');
                $estadoSocioStmt = Database::connection()->query('SELECT id, nombre FROM estados_socio WHERE activo = 1 ORDER BY nombre ASC');
                $formMeta['options']['tipo_socio_id'] = array_map(static fn(array $item): array => [
                    'value' => (string) $item['id'],
                    'label' => (string) $item['nombre'],
                ], $tipoSocioStmt->fetchAll());
                $formMeta['options']['estado_socio_id'] = array_map(static fn(array $item): array => [
                    'value' => (string) $item['id'],
                    'label' => (string) $item['nombre'],
                ], $estadoSocioStmt->fetchAll());
                $planNameColumn = ModuleCatalog::columnExists('periodos', 'nombre_periodo') ? 'nombre_periodo' : null;
                $planTypeColumn = ModuleCatalog::columnExists('periodos', 'tipo_periodo') ? 'tipo_periodo' : null;
                $planAmountColumn = ModuleCatalog::columnExists('periodos', 'monto_a_pagar') ? 'monto_a_pagar' : null;
                $planColumns = array_filter(['id', $planNameColumn, $planTypeColumn, $planAmountColumn]);
                $formMeta['options']['planes_ids'] = [];
                if (!empty($planColumns)) {
                    $planesStmt = Database::connection()->query('SELECT ' . implode(', ', $planColumns) . ' FROM periodos ORDER BY id DESC');
                    $formMeta['options']['planes_ids'] = array_map(static function (array $item) use ($planNameColumn, $planTypeColumn, $planAmountColumn): array {
                        $name = (string) ($item[$planNameColumn ?? ''] ?? ('Plan #' . (string) ($item['id'] ?? '')));
                        $type = $planTypeColumn ? ucfirst((string) ($item[$planTypeColumn] ?? '')) : '';
                        $amount = $planAmountColumn ? number_format((float) ($item[$planAmountColumn] ?? 0), 0, ',', '.') : null;
                        $parts = array_filter([
                            $name,
                            $type !== '' ? $type : null,
                            $amount !== null ? '$' . $amount : null,
                        ]);

                        return [
                            'value' => (string) ($item['id'] ?? ''),
                            'label' => implode(' · ', $parts),
                        ];
                    }, $planesStmt->fetchAll());
                }

                if ($currentRecord === null) {
                    $currentRecord = [
                        'numero_socio' => ModuleCatalog::nextSocioNumber(),
                        'planes_ids' => [],
                    ];
                } else {
                    $currentRecord['planes_ids'] = ModuleCatalog::fetchSocioPlanes((int) ($currentRecord['id'] ?? 0));
                }
            }

            if ($config['table'] === 'aportes') {
                $formFields = ['socio_id', 'monto', 'comentario'];

                $formMeta = [
                    'types' => [
                        'monto' => 'number',
                        'comentario' => 'textarea',
                    ],
                    'labels' => [
                        'socio_id' => 'Socio',
                        'monto' => 'Monto donación',
                        'comentario' => 'Detalle de la donación',
                    ],
                ];
                $columnLabels = $formMeta['labels'];

                $sociosStmt = Database::connection()->query('SELECT id, numero_socio, nombre_completo, rut, telefono, correo FROM socios WHERE deleted_at IS NULL ORDER BY nombre_completo ASC');
                $socios = $sociosStmt->fetchAll();
                $formMeta['options']['socio_id'] = array_map(static function (array $item): array {
                    $numeroSocio = trim((string) ($item['numero_socio'] ?? ''));
                    $nombre = trim((string) ($item['nombre_completo'] ?? ''));
                    $label = $nombre !== '' ? $nombre : ('Socio #' . (string) ($item['id'] ?? ''));
                    if ($numeroSocio !== '') {
                        $label .= ' · N° ' . $numeroSocio;
                    }

                    return [
                        'value' => (string) ($item['id'] ?? ''),
                        'label' => $label,
                    ];
                }, $socios);
                $formMeta['socios_data'] = array_reduce($socios, static function (array $carry, array $item): array {
                    $id = (int) ($item['id'] ?? 0);
                    if ($id <= 0) {
                        return $carry;
                    }

                    $carry[$id] = [
                        'numero_socio' => trim((string) ($item['numero_socio'] ?? '')),
                        'nombre_completo' => trim((string) ($item['nombre_completo'] ?? '')),
                        'rut' => trim((string) ($item['rut'] ?? '')),
                        'telefono' => trim((string) ($item['telefono'] ?? '')),
                        'correo' => trim((string) ($item['correo'] ?? '')),
                    ];

                    return $carry;
                }, []);

                $visibleColumns = array_values(array_intersect([
                    'id',
                    'socio_id',
                    'monto',
                    'comentario',
                    'fecha_aporte',
                    'estado',
                ], $data['columns']['all']));
            }

            if ($config['table'] === 'egresos') {
                $formFields = array_values(array_intersect([
                    'fecha',
                    'tipo_egreso_id',
                    'descripcion',
                    'numero_documento',
                    'monto',
                    'proveedor_destinatario',
                ], $data['columns']['form']));

                $formMeta = [
                    'types' => [
                        'fecha' => 'date',
                        'descripcion' => 'textarea',
                        'monto' => 'number',
                    ],
                    'labels' => [
                        'fecha' => 'Fecha del retiro',
                        'tipo_egreso_id' => 'Tipo de retiro',
                        'proveedor_destinatario' => 'Retirado por / destinatario',
                        'descripcion' => 'Motivo del retiro',
                        'numero_documento' => 'N° comprobante',
                        'monto' => 'Monto retirado',
                    ],
                    'readonly' => [
                        'numero_documento' => true,
                        'proveedor_destinatario' => true,
                    ],
                    'required' => [
                        'fecha' => true,
                        'tipo_egreso_id' => true,
                        'descripcion' => true,
                        'monto' => true,
                        'proveedor_destinatario' => true,
                    ],
                    'attributes' => [
                        'fecha' => ['max' => date('Y-m-d')],
                        'descripcion' => ['placeholder' => 'Detalle breve del motivo del retiro'],
                        'monto' => ['step' => '0.01', 'min' => '0.01', 'placeholder' => '0.00'],
                    ],
                ];
                $columnLabels = $formMeta['labels'];

                $tiposEgresoStmt = Database::connection()->query('SELECT id, nombre FROM tipos_egreso WHERE activo = 1 ORDER BY nombre ASC');
                $formMeta['options']['tipo_egreso_id'] = array_map(static fn(array $item): array => [
                    'value' => (string) ($item['id'] ?? ''),
                    'label' => (string) ($item['nombre'] ?? ''),
                ], $tiposEgresoStmt->fetchAll());

                $formMeta['forma_retiro_options'] = [
                    ['value' => 'transferencia', 'label' => 'Transferencia'],
                    ['value' => 'efectivo', 'label' => 'Efectivo'],
                    ['value' => 'cheque', 'label' => 'Cheque'],
                    ['value' => 'deposito', 'label' => 'Depósito'],
                    ['value' => 'otro', 'label' => 'Otro'],
                ];

                $sociosStmt = Database::connection()->query('SELECT id, nombre_completo, rut, numero_socio, telefono, correo FROM socios WHERE deleted_at IS NULL ORDER BY nombre_completo ASC');
                $socios = $sociosStmt->fetchAll();
                $formMeta['retirante_socios_options'] = array_map(static function (array $item): array {
                    $nombre = trim((string) ($item['nombre_completo'] ?? ''));
                    $rut = trim((string) ($item['rut'] ?? ''));
                    $label = $nombre !== '' ? $nombre : ('Socio #' . (string) ($item['id'] ?? ''));
                    if ($rut !== '') {
                        $label .= ' · ' . $rut;
                    }

                    return [
                        'value' => (string) ($item['id'] ?? ''),
                        'label' => $label,
                    ];
                }, $socios);
                $formMeta['retirante_socios_data'] = array_reduce($socios, static function (array $carry, array $item): array {
                    $id = (int) ($item['id'] ?? 0);
                    if ($id <= 0) {
                        return $carry;
                    }

                    $carry[$id] = [
                        'nombre_completo' => trim((string) ($item['nombre_completo'] ?? '')),
                        'rut' => trim((string) ($item['rut'] ?? '')),
                        'numero_socio' => trim((string) ($item['numero_socio'] ?? '')),
                        'telefono' => trim((string) ($item['telefono'] ?? '')),
                        'correo' => trim((string) ($item['correo'] ?? '')),
                    ];

                    return $carry;
                }, []);

                if ($currentRecord === null) {
                    $currentRecord = [
                        'fecha' => date('Y-m-d'),
                        'numero_documento' => ModuleCatalog::nextEgresoDocumentNumber(),
                        'proveedor_destinatario' => '',
                    ];
                } elseif (empty($currentRecord['numero_documento'])) {
                    $currentRecord['numero_documento'] = ModuleCatalog::nextEgresoDocumentNumber();
                }

                $currentRecord['_forma_retiro'] = '';
                $observacion = trim((string) ($currentRecord['observacion'] ?? ''));
                if (preg_match('/^Forma de retiro:\s*(.+)$/i', $observacion, $matches) === 1) {
                    $currentRecord['_forma_retiro'] = strtolower(trim((string) ($matches[1] ?? '')));
                }

                $visibleColumns = array_values(array_intersect([
                    'id',
                    'fecha',
                    'tipo_egreso_id',
                    'proveedor_destinatario',
                    'descripcion',
                    'monto',
                    'numero_documento',
                    'estado',
                ], $data['columns']['all']));
            }

            if ($config['table'] === 'rendiciones') {
                $sociosStmt = Database::connection()->query('SELECT id, nombre_completo, rut, numero_socio FROM socios WHERE deleted_at IS NULL ORDER BY nombre_completo ASC');
                $socios = $sociosStmt->fetchAll();

                $columnLabels = [
                    'fecha' => 'Fecha',
                    'tipo_movimiento' => 'Tipo',
                    'origen_modulo' => 'Origen',
                    'descripcion' => 'Descripción',
                    'ingreso' => 'Ingreso',
                    'egreso' => 'Egreso',
                    'saldo_referencial' => 'Saldo referencial',
                ];
                $visibleColumns = array_values(array_intersect([
                    'fecha',
                    'tipo_movimiento',
                    'origen_modulo',
                    'descripcion',
                    'ingreso',
                    'egreso',
                    'saldo_referencial',
                ], $data['columns']['all']));
                $formFields = [];
                $formMeta['rendiciones_filter_options'] = [
                    'periodos' => [
                        ['value' => '', 'label' => 'Manual (Desde/Hasta)'],
                        ['value' => 'mes_actual', 'label' => 'Mes actual'],
                        ['value' => 'mes_anterior', 'label' => 'Mes anterior'],
                        ['value' => 'trimestre_actual', 'label' => 'Trimestre actual'],
                        ['value' => 'anio_actual', 'label' => 'Año actual'],
                    ],
                    'socios' => array_map(static function (array $item): array {
                        $nombre = trim((string) ($item['nombre_completo'] ?? ''));
                        $rut = trim((string) ($item['rut'] ?? ''));
                        $numeroSocio = trim((string) ($item['numero_socio'] ?? ''));
                        $label = $nombre !== '' ? $nombre : ('Socio #' . (string) ($item['id'] ?? ''));
                        if ($rut !== '') {
                            $label .= ' · ' . $rut;
                        }
                        if ($numeroSocio !== '') {
                            $label .= ' · N° ' . $numeroSocio;
                        }

                        return [
                            'value' => (string) ($item['id'] ?? ''),
                            'label' => $label,
                        ];
                    }, $socios),
                ];
            }


            $flashSuccess = $_SESSION['flash_success'] ?? null;
            $flashError = $_SESSION['flash_error'] ?? null;
            unset($_SESSION['flash_success'], $_SESSION['flash_error']);

            $this->view('modules/index', [
                'title' => $config['title'],
                'description' => $config['description'],
                'route' => $config['route'],
                'query' => $query,
                'status' => $status,
                'from' => $from,
                'to' => $to,
                'rows' => $data['rows'],
                'displayRows' => $displayRows,
                'columns' => $visibleColumns,
                'formFields' => $formFields,
                'statusField' => $data['columns']['status_field'],
                'statusCounts' => $data['summary']['status_counts'] ?? [],
                'moduleSummary' => $data['summary'],
                'total' => $data['total'],
                'page' => $data['page'],
                'pages' => $data['pages'],
                'token' => Csrf::token(),
                'primaryKey' => $primaryKey,
                'currentRecord' => $currentRecord,
                'viewRecord' => $viewRecord,
                'viewRecordDisplay' => $viewRecordDisplay,
                'isReadOnly' => $isReadOnly,
                'flashSuccess' => $flashSuccess,
                'flashError' => $flashError,
                'formMeta' => $formMeta,
                'columnLabels' => $columnLabels,
                'extraFilters' => $extraFilters,
                'extraQueryParams' => $extraQueryParams,
            ]);
        } catch (Throwable $exception) {
            $this->view('modules/index', [
                'title' => $config['title'],
                'description' => $config['description'],
                'route' => $config['route'],
                'query' => $query,
                'status' => $status,
                'from' => $from,
                'to' => $to,
                'rows' => [],
                'displayRows' => [],
                'columns' => [],
                'formFields' => [],
                'statusField' => null,
                'statusCounts' => [],
                'moduleSummary' => ['total' => 0, 'visibles' => 0, 'status_counts' => []],
                'total' => 0,
                'page' => 1,
                'pages' => 1,
                'token' => Csrf::token(),
                'primaryKey' => 'id',
                'currentRecord' => null,
                'viewRecord' => null,
                'viewRecordDisplay' => null,
                'isReadOnly' => true,
                'error' => 'No fue posible cargar el módulo. Verifica la conexión y migraciones de base de datos.',
                'formMeta' => [],
                'columnLabels' => [],
                'extraFilters' => [],
                'extraQueryParams' => [],
            ]);
        }
    }
}
