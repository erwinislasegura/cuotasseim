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

        if (($config['route'] ?? '') === 'reportes') {
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
                    'sql' => '(COALESCE(ingreso, 0) + COALESCE(egreso, 0)) >= :filtro_monto_min',
                    'params' => [':filtro_monto_min' => (float) $montoMin],
                ];
            }

            if ($montoMax !== '' && is_numeric($montoMax)) {
                $extraConditions[] = [
                    'sql' => '(COALESCE(ingreso, 0) + COALESCE(egreso, 0)) <= :filtro_monto_max',
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

            $rendicionesResult = $this->buildRendicionesDataset(
                $query,
                $status,
                $from,
                $to,
                $extraFilters
            );

            if (($_GET['export'] ?? '') === 'excel') {
                ModuleCatalog::exportCsv(
                    'reportes_' . date('Ymd_His') . '.csv',
                    ['fecha', 'tipo_movimiento', 'origen_modulo', 'descripcion', 'ingreso', 'egreso', 'saldo_referencial', 'usuario_registro'],
                    $rendicionesResult['filtered_rows']
                );
                return;
            }

            if ((string) ($_GET['report'] ?? '') === 'print') {
                $reportRows = $rendicionesResult['filtered_rows'];
                $reportSummary = [
                    'total_registros' => count($reportRows),
                    'total_ingresos' => 0.0,
                    'total_egresos' => 0.0,
                ];
                $byType = ['ingreso' => 0.0, 'egreso' => 0.0];
                $byOrigin = [];
                $byMonth = [];

                foreach ($reportRows as $row) {
                    $ingreso = (float) ($row['ingreso'] ?? 0);
                    $egreso = (float) ($row['egreso'] ?? 0);
                    $reportSummary['total_ingresos'] += $ingreso;
                    $reportSummary['total_egresos'] += $egreso;

                    $tipo = strtolower(trim((string) ($row['tipo_movimiento'] ?? '')));
                    if (!isset($byType[$tipo])) {
                        $byType[$tipo] = 0.0;
                    }
                    $byType[$tipo] += $ingreso > 0 ? $ingreso : $egreso;

                    $origin = trim((string) ($row['origen_modulo'] ?? 'sin_origen'));
                    if ($origin === '') {
                        $origin = 'sin_origen';
                    }
                    if (!isset($byOrigin[$origin])) {
                        $byOrigin[$origin] = ['ingreso' => 0.0, 'egreso' => 0.0];
                    }
                    $byOrigin[$origin]['ingreso'] += $ingreso;
                    $byOrigin[$origin]['egreso'] += $egreso;

                    $monthKey = date('Y-m', strtotime((string) ($row['fecha'] ?? 'now')));
                    if (!isset($byMonth[$monthKey])) {
                        $byMonth[$monthKey] = ['ingreso' => 0.0, 'egreso' => 0.0];
                    }
                    $byMonth[$monthKey]['ingreso'] += $ingreso;
                    $byMonth[$monthKey]['egreso'] += $egreso;
                }

                ksort($byMonth);
                ksort($byOrigin);

                $this->view('reportes/report', [
                    'title' => 'Informe de reportes',
                    'rows' => $reportRows,
                    'query' => $query,
                    'status' => $status,
                    'from' => $from,
                    'to' => $to,
                    'extraFilters' => $extraFilters,
                    'summary' => $reportSummary,
                    'byType' => $byType,
                    'byOrigin' => $byOrigin,
                    'byMonth' => $byMonth,
                ], 'print');
                return;
            }

            $sociosStmt = Database::connection()->query('SELECT id, nombre_completo, rut, numero_socio FROM socios WHERE deleted_at IS NULL ORDER BY nombre_completo ASC');
            $socios = $sociosStmt->fetchAll();
            $formMeta = [
                'reportes_filter_options' => [
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
                ],
            ];

            $this->view('modules/index', [
                'title' => $config['title'],
                'description' => $config['description'],
                'route' => $config['route'],
                'query' => $query,
                'status' => $status,
                'from' => $from,
                'to' => $to,
                'rows' => $rendicionesResult['rows'],
                'displayRows' => array_map(static function (array $row): array {
                    $row['ingreso'] = '$' . number_format((float) ($row['ingreso'] ?? 0), 0, ',', '.');
                    $row['egreso'] = '$' . number_format((float) ($row['egreso'] ?? 0), 0, ',', '.');
                    $row['saldo_referencial'] = '$' . number_format((float) ($row['saldo_referencial'] ?? 0), 0, ',', '.');
                    return $row;
                }, $rendicionesResult['rows']),
                'columns' => ['fecha', 'tipo_movimiento', 'origen_modulo', 'referencia_id', 'descripcion', 'ingreso', 'egreso', 'saldo_referencial', 'usuario_registro'],
                'formFields' => [],
                'statusField' => 'tipo_movimiento',
                'statusCounts' => $rendicionesResult['status_counts'],
                'moduleSummary' => [
                    'total' => $rendicionesResult['total'],
                    'visibles' => count($rendicionesResult['rows']),
                    'status_counts' => $rendicionesResult['status_counts'],
                    'total_ingresos' => $rendicionesResult['total_ingresos'],
                    'total_egresos' => $rendicionesResult['total_egresos'],
                    'balance' => $rendicionesResult['balance'],
                ],
                'total' => $rendicionesResult['total'],
                'page' => $page,
                'pages' => $rendicionesResult['pages'],
                'token' => Csrf::token(),
                'primaryKey' => 'row_id',
                'currentRecord' => null,
                'viewRecord' => null,
                'viewRecordDisplay' => null,
                'isReadOnly' => true,
                'flashSuccess' => null,
                'flashError' => null,
                'formMeta' => $formMeta,
                'columnLabels' => [
                    'fecha' => 'Fecha',
                    'tipo_movimiento' => 'Tipo',
                    'origen_modulo' => 'Origen',
                    'referencia_id' => 'Referencia',
                    'descripcion' => 'Descripción',
                    'ingreso' => 'Ingreso',
                    'egreso' => 'Egreso',
                    'saldo_referencial' => 'Saldo referencial',
                    'usuario_registro' => 'Registrado por',
                ],
                'extraFilters' => $extraFilters,
                'extraQueryParams' => $extraQueryParams,
            ]);
            return;
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
                    $isUpdate = $id !== null && $id > 0;
                    $previousRecord = $isUpdate ? ModuleCatalog::findById($config['table'], $primaryKey, $id) : null;
                    $savedId = ModuleCatalog::save($config['table'], $primaryKey, $columnsMeta['form'], $_POST, $isUpdate ? $id : null);
                    $targetId = $savedId > 0 ? $savedId : ($isUpdate ? (int) $id : 0);
                    $currentRecord = $targetId > 0 ? ModuleCatalog::findById($config['table'], $primaryKey, $targetId) : null;

                    ModuleCatalog::registerAudit(
                        (string) ($config['route'] ?? $moduleKey),
                        $isUpdate ? 'actualizar' : 'crear',
                        $targetId > 0 ? $targetId : null,
                        is_array($previousRecord) ? $previousRecord : null,
                        is_array($currentRecord) ? $currentRecord : null
                    );

                    $_SESSION['flash_success'] = $isUpdate ? 'Registro actualizado correctamente.' : 'Registro creado correctamente.';
                }

                if (!$isReadOnly && $action === 'delete' && $id !== null && $id > 0) {
                    $deletedRecord = ModuleCatalog::findById($config['table'], $primaryKey, $id);
                    ModuleCatalog::delete($config['table'], $primaryKey, $id, $columnsMeta['has_deleted_at']);
                    ModuleCatalog::registerAudit(
                        (string) ($config['route'] ?? $moduleKey),
                        'eliminar',
                        $id,
                        is_array($deletedRecord) ? $deletedRecord : null,
                        null
                    );
                    $_SESSION['flash_success'] = 'Registro eliminado correctamente.';
                }

                $this->redirect('/' . $config['route']);
            }

            if (($_GET['export'] ?? '') === 'excel') {
                ModuleCatalog::exportCsv($config['route'] . '_' . date('Ymd_His') . '.csv', $data['columns']['all'], $data['rows']);
                return;
            }

            if (($config['route'] ?? '') === 'reportes' && (string) ($_GET['report'] ?? '') === 'print') {
                $fullPerPage = max(1, min(5000, (int) ($data['total'] ?? 0)));
                $fullData = ModuleCatalog::fetchData(
                    $config,
                    $query,
                    1,
                    $fullPerPage,
                    $status !== '' ? $status : null,
                    $from !== '' ? $from : null,
                    $to !== '' ? $to : null,
                    $extraConditions
                );

                $reportRows = $fullData['rows'];
                $reportSummary = [
                    'total_registros' => count($reportRows),
                    'total_ingresos' => 0.0,
                    'total_egresos' => 0.0,
                ];
                $byType = ['ingreso' => 0.0, 'egreso' => 0.0];
                $byOrigin = [];
                $byMonth = [];

                foreach ($reportRows as $row) {
                    $ingreso = (float) ($row['ingreso'] ?? 0);
                    $egreso = (float) ($row['egreso'] ?? 0);
                    $reportSummary['total_ingresos'] += $ingreso;
                    $reportSummary['total_egresos'] += $egreso;

                    $tipo = strtolower(trim((string) ($row['tipo_movimiento'] ?? '')));
                    if (!isset($byType[$tipo])) {
                        $byType[$tipo] = 0.0;
                    }
                    $byType[$tipo] += $ingreso > 0 ? $ingreso : $egreso;

                    $origin = trim((string) ($row['origen_modulo'] ?? 'sin_origen'));
                    if ($origin === '') {
                        $origin = 'sin_origen';
                    }
                    if (!isset($byOrigin[$origin])) {
                        $byOrigin[$origin] = ['ingreso' => 0.0, 'egreso' => 0.0];
                    }
                    $byOrigin[$origin]['ingreso'] += $ingreso;
                    $byOrigin[$origin]['egreso'] += $egreso;

                    $monthKey = date('Y-m', strtotime((string) ($row['fecha'] ?? 'now')));
                    if (!isset($byMonth[$monthKey])) {
                        $byMonth[$monthKey] = ['ingreso' => 0.0, 'egreso' => 0.0];
                    }
                    $byMonth[$monthKey]['ingreso'] += $ingreso;
                    $byMonth[$monthKey]['egreso'] += $egreso;
                }

                ksort($byMonth);
                ksort($byOrigin);

                $this->view('reportes/report', [
                    'title' => 'Informe de reportes',
                    'rows' => $reportRows,
                    'query' => $query,
                    'status' => $status,
                    'from' => $from,
                    'to' => $to,
                    'extraFilters' => $extraFilters,
                    'summary' => $reportSummary,
                    'byType' => $byType,
                    'byOrigin' => $byOrigin,
                    'byMonth' => $byMonth,
                ], 'print');
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

            if (($config['route'] ?? '') === 'auditoria') {
                $usuariosMap = [];
                foreach ($displayRows as $auditRow) {
                    $uid = (int) ($auditRow['usuario_id'] ?? 0);
                    if ($uid > 0) {
                        $usuariosMap[$uid] = $uid;
                    }
                }

                if (!empty($usuariosMap) && ModuleCatalog::tableExists('usuarios')) {
                    $ids = array_keys($usuariosMap);
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmtUsuarios = Database::connection()->prepare('SELECT id, nombre, usuario, correo FROM usuarios WHERE id IN (' . $placeholders . ')');
                    foreach ($ids as $index => $userId) {
                        $stmtUsuarios->bindValue($index + 1, $userId, \PDO::PARAM_INT);
                    }
                    $stmtUsuarios->execute();

                    foreach ($stmtUsuarios->fetchAll() as $userRow) {
                        $userId = (int) ($userRow['id'] ?? 0);
                        if ($userId <= 0) {
                            continue;
                        }
                        $nombre = trim((string) ($userRow['nombre'] ?? ''));
                        $usuario = trim((string) ($userRow['usuario'] ?? ''));
                        $correo = trim((string) ($userRow['correo'] ?? ''));
                        $label = $nombre !== '' ? $nombre : ($usuario !== '' ? $usuario : ('Usuario #' . $userId));
                        if ($usuario !== '' && $usuario !== $label) {
                            $label .= ' · @' . $usuario;
                        }
                        if ($correo !== '') {
                            $label .= ' · ' . $correo;
                        }
                        $usuariosMap[$userId] = $label;
                    }
                }

                $displayRows = array_map(static function (array $auditRow) use ($usuariosMap): array {
                    $uid = (int) ($auditRow['usuario_id'] ?? 0);
                    if ($uid > 0) {
                        $auditRow['usuario_id'] = (string) ($usuariosMap[$uid] ?? ('Usuario #' . $uid));
                    } else {
                        $auditRow['usuario_id'] = 'Sistema';
                    }

                    return $auditRow;
                }, $displayRows);
            }

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

            if (($config['route'] ?? '') === 'auditoria') {
                $columnLabels = [
                    'usuario_id' => 'Usuario',
                    'modulo' => 'Módulo',
                    'accion' => 'Acción',
                    'id_registro' => 'ID registro',
                    'datos_anteriores' => 'Datos anteriores',
                    'datos_nuevos' => 'Datos nuevos',
                    'fecha' => 'Fecha',
                    'ip' => 'IP',
                    'user_agent' => 'Navegador',
                ];
                $visibleColumns = array_values(array_intersect([
                    'fecha',
                    'usuario_id',
                    'modulo',
                    'accion',
                    'id_registro',
                    'ip',
                    'user_agent',
                ], $data['columns']['all']));
                $formFields = [];
            }

            if (($config['route'] ?? '') === 'reportes') {
                $sociosStmt = Database::connection()->query('SELECT id, nombre_completo, rut, numero_socio FROM socios WHERE deleted_at IS NULL ORDER BY nombre_completo ASC');
                $socios = $sociosStmt->fetchAll();

                $columnLabels = [
                    'fecha' => 'Fecha',
                    'tipo_movimiento' => 'Tipo',
                    'origen_modulo' => 'Origen',
                    'referencia_id' => 'Referencia',
                    'descripcion' => 'Descripción',
                    'ingreso' => 'Ingreso',
                    'egreso' => 'Egreso',
                    'saldo_referencial' => 'Saldo referencial',
                    'usuario_registro' => 'Registrado por',
                ];
                $visibleColumns = array_values(array_intersect([
                    'fecha',
                    'tipo_movimiento',
                    'origen_modulo',
                    'referencia_id',
                    'descripcion',
                    'ingreso',
                    'egreso',
                    'saldo_referencial',
                    'usuario_registro',
                ], $data['columns']['all']));
                $formFields = [];
                $formMeta['reportes_filter_options'] = [
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

            if ($config['table'] === 'movimientos_tesoreria' && ($config['route'] ?? '') === 'tesoreria') {
                $formFields = array_values(array_intersect([
                    'fecha',
                    'tipo_movimiento',
                    'descripcion',
                    'ingreso',
                    'egreso',
                    'cuenta_bancaria_id',
                ], $data['columns']['form']));

                $formMeta = [
                    'types' => [
                        'fecha' => 'date',
                        'descripcion' => 'textarea',
                        'ingreso' => 'number',
                        'egreso' => 'number',
                    ],
                    'labels' => [
                        'fecha' => 'Fecha movimiento',
                        'tipo_movimiento' => 'Tipo movimiento',
                        'descripcion' => 'Descripción / motivo',
                        'ingreso' => 'Monto ingreso',
                        'egreso' => 'Monto egreso',
                        'cuenta_bancaria_id' => 'Cuenta bancaria',
                        'origen_modulo' => 'Origen',
                        'saldo_referencial' => 'Saldo referencial',
                    ],
                    'options' => [
                        'tipo_movimiento' => [
                            ['value' => 'ingreso', 'label' => 'Ingreso'],
                            ['value' => 'egreso', 'label' => 'Egreso'],
                        ],
                    ],
                    'required' => [
                        'fecha' => true,
                        'tipo_movimiento' => true,
                        'descripcion' => true,
                    ],
                    'attributes' => [
                        'ingreso' => ['step' => '0.01', 'min' => '0'],
                        'egreso' => ['step' => '0.01', 'min' => '0'],
                    ],
                ];
                $columnLabels = $formMeta['labels'];

                $ajustePagosStmt = Database::connection()->query(
                    'SELECT p.id, p.fecha_pago AS fecha, p.monto_total AS monto, COALESCE(s.nombre_completo, CONCAT(COALESCE(s.nombres, \'\'), \' \', COALESCE(s.apellidos, \'\'))) AS titular
                     FROM pagos p
                     LEFT JOIN socios s ON s.id = p.socio_id
                     WHERE p.deleted_at IS NULL
                     ORDER BY p.id DESC
                     LIMIT 200'
                );
                $ajusteAportesStmt = Database::connection()->query(
                    'SELECT a.id, a.fecha_aporte AS fecha, a.monto AS monto, COALESCE(a.nombre_aportante, COALESCE(s.nombre_completo, CONCAT(COALESCE(s.nombres, \'\'), \' \', COALESCE(s.apellidos, \'\')))) AS titular
                     FROM aportes a
                     LEFT JOIN socios s ON s.id = a.socio_id
                     ORDER BY a.id DESC
                     LIMIT 200'
                );
                $ajusteEgresosStmt = Database::connection()->query(
                    'SELECT e.id, e.fecha AS fecha, e.monto AS monto, e.proveedor_destinatario AS titular
                     FROM egresos e
                     WHERE e.deleted_at IS NULL
                     ORDER BY e.id DESC
                     LIMIT 200'
                );

                $mapAjustes = static function (array $rows): array {
                    return array_map(static function (array $row): array {
                        $id = (string) ($row['id'] ?? '');
                        $fecha = trim((string) ($row['fecha'] ?? ''));
                        $titular = trim((string) ($row['titular'] ?? ''));
                        $monto = number_format((float) ($row['monto'] ?? 0), 0, ',', '.');

                        return [
                            'value' => $id,
                            'label' => '#' . $id . ' · ' . ($fecha !== '' ? $fecha : '-') . ' · $' . $monto . ($titular !== '' ? ' · ' . $titular : ''),
                        ];
                    }, $rows);
                };

                $formMeta['ajustes_origen_options'] = [
                    ['value' => '', 'label' => 'Sin ajuste asociado'],
                    ['value' => 'pagos', 'label' => 'Ajuste sobre Pagos'],
                    ['value' => 'aportes', 'label' => 'Ajuste sobre Aportes'],
                    ['value' => 'egresos', 'label' => 'Ajuste sobre Egresos'],
                ];
                $formMeta['ajustes_referencia_options'] = [
                    'pagos' => $mapAjustes($ajustePagosStmt->fetchAll()),
                    'aportes' => $mapAjustes($ajusteAportesStmt->fetchAll()),
                    'egresos' => $mapAjustes($ajusteEgresosStmt->fetchAll()),
                ];

                if (in_array('cuenta_bancaria_id', $formFields, true)) {
                    $cuentasStmt = Database::connection()->query('SELECT id, banco, tipo_cuenta, numero_cuenta, titular FROM cuentas_bancarias WHERE activa = 1 ORDER BY banco ASC, numero_cuenta ASC');
                    $formMeta['options']['cuenta_bancaria_id'] = array_map(static function (array $item): array {
                        $banco = trim((string) ($item['banco'] ?? ''));
                        $tipo = trim((string) ($item['tipo_cuenta'] ?? ''));
                        $numero = trim((string) ($item['numero_cuenta'] ?? ''));
                        $titular = trim((string) ($item['titular'] ?? ''));
                        $label = implode(' · ', array_filter([$banco, $tipo, $numero !== '' ? ('N° ' . $numero) : '', $titular]));
                        if ($label === '') {
                            $label = 'Cuenta #' . (string) ($item['id'] ?? '');
                        }

                        return [
                            'value' => (string) ($item['id'] ?? ''),
                            'label' => $label,
                        ];
                    }, $cuentasStmt->fetchAll());
                }

                if ($currentRecord === null) {
                    $currentRecord = [
                        'fecha' => date('Y-m-d'),
                        'tipo_movimiento' => 'ingreso',
                        'ingreso' => '',
                        'egreso' => '',
                    ];
                }

                $visibleColumns = array_values(array_intersect([
                    'id',
                    'fecha',
                    'tipo_movimiento',
                    'origen_modulo',
                    'descripcion',
                    'ingreso',
                    'egreso',
                    'saldo_referencial',
                ], $data['columns']['all']));
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

    private function buildRendicionesDataset(string $query, string $status, string $from, string $to, array $extraFilters): array
    {
        $db = Database::connection();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $params = [];
        $whereSql = 'WHERE 1=1';

        if ($from !== '') {
            $whereSql .= ' AND DATE(mt.fecha) >= :from_date';
            $params[':from_date'] = $from;
        }
        if ($to !== '') {
            $whereSql .= ' AND DATE(mt.fecha) <= :to_date';
            $params[':to_date'] = $to;
        }

        $sql = "
            SELECT
                CONCAT('MT-', mt.id) AS row_id,
                mt.id,
                mt.fecha,
                mt.tipo_movimiento,
                mt.origen_modulo,
                mt.referencia_id,
                COALESCE(mt.descripcion, '') AS descripcion,
                COALESCE(mt.ingreso, 0) AS ingreso,
                COALESCE(mt.egreso, 0) AS egreso,
                COALESCE(mt.saldo_referencial, 0) AS saldo_referencial,
                COALESCE(s.id, 0) AS socio_id,
                COALESCE(s.rut, '') AS socio_rut,
                COALESCE(s.nombre_completo, '') AS socio_nombre,
                COALESCE(u.nombre, u.usuario, CONCAT('Usuario #', COALESCE(u.id, 0)), 'Sistema') AS usuario_registro
            FROM movimientos_tesoreria mt
            LEFT JOIN pagos p
              ON mt.origen_modulo = 'pagos'
             AND mt.referencia_id = p.id
            LEFT JOIN aportes a
              ON mt.origen_modulo = 'aportes'
             AND mt.referencia_id = a.id
            LEFT JOIN egresos e
              ON mt.origen_modulo = 'egresos'
             AND mt.referencia_id = e.id
            LEFT JOIN socios s
              ON s.id = CASE
                  WHEN mt.origen_modulo = 'pagos' THEN p.socio_id
                  WHEN mt.origen_modulo = 'aportes' THEN a.socio_id
                  ELSE NULL
              END
            LEFT JOIN usuarios u
              ON u.id = COALESCE(p.usuario_id, a.usuario_id, e.usuario_id)
            {$whereSql}
        ";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $allRows = $stmt->fetchAll();

        $socioFilter = (int) ($extraFilters['socio_id'] ?? 0);
        $socioFilterRut = '';
        $socioFilterNombre = '';
        if ($socioFilter > 0) {
            $stmtSocio = $db->prepare('SELECT nombre_completo, rut FROM socios WHERE id = :id LIMIT 1');
            $stmtSocio->bindValue(':id', $socioFilter, \PDO::PARAM_INT);
            $stmtSocio->execute();
            $socioRow = $stmtSocio->fetch() ?: [];
            $socioFilterRut = mb_strtolower(trim((string) ($socioRow['rut'] ?? '')));
            $socioFilterNombre = mb_strtolower(trim((string) ($socioRow['nombre_completo'] ?? '')));
        }
        $montoMin = (string) ($extraFilters['monto_min'] ?? '');
        $montoMax = (string) ($extraFilters['monto_max'] ?? '');
        $queryLower = mb_strtolower(trim($query));

        $filtered = array_values(array_filter($allRows, static function (array $row) use ($status, $socioFilter, $socioFilterRut, $socioFilterNombre, $montoMin, $montoMax, $queryLower): bool {
            $tipo = (string) ($row['tipo_movimiento'] ?? '');
            if ($status !== '' && $tipo !== $status) {
                return false;
            }

            if ($socioFilter > 0) {
                $rowSocioId = (int) ($row['socio_id'] ?? 0);
                $textHaystack = mb_strtolower(trim((string) (($row['descripcion'] ?? '') . ' ' . ($row['socio_rut'] ?? '') . ' ' . ($row['socio_nombre'] ?? ''))));
                $matchesByText = ($socioFilterRut !== '' && str_contains($textHaystack, $socioFilterRut))
                    || ($socioFilterNombre !== '' && str_contains($textHaystack, $socioFilterNombre));
                if ($rowSocioId !== $socioFilter && !$matchesByText) {
                    return false;
                }
            }

            $monto = (float) (($row['ingreso'] ?? 0) > 0 ? ($row['ingreso'] ?? 0) : ($row['egreso'] ?? 0));
            if ($montoMin !== '' && is_numeric($montoMin) && $monto < (float) $montoMin) {
                return false;
            }
            if ($montoMax !== '' && is_numeric($montoMax) && $monto > (float) $montoMax) {
                return false;
            }

            if ($queryLower !== '') {
                $haystack = mb_strtolower(trim((string) (($row['descripcion'] ?? '') . ' ' . ($row['origen_modulo'] ?? '') . ' ' . ($row['socio_nombre'] ?? '') . ' ' . ($row['socio_rut'] ?? '') . ' ' . ($row['usuario_registro'] ?? ''))));
                if (!str_contains($haystack, $queryLower)) {
                    return false;
                }
            }

            return true;
        }));

        usort($filtered, static function (array $a, array $b): int {
            return strcmp((string) ($b['fecha'] ?? ''), (string) ($a['fecha'] ?? ''));
        });

        $total = count($filtered);
        $pages = max(1, (int) ceil($total / $perPage));
        $offset = max(0, ($page - 1) * $perPage);
        $rows = array_slice($filtered, $offset, $perPage);

        $statusCounts = ['ingreso' => 0, 'egreso' => 0];
        $totalIngresos = 0.0;
        $totalEgresos = 0.0;
        foreach ($filtered as $row) {
            $type = (string) ($row['tipo_movimiento'] ?? '');
            if (!isset($statusCounts[$type])) {
                $statusCounts[$type] = 0;
            }
            $statusCounts[$type]++;
            $totalIngresos += (float) ($row['ingreso'] ?? 0);
            $totalEgresos += (float) ($row['egreso'] ?? 0);
        }

        return [
            'rows' => $rows,
            'filtered_rows' => $filtered,
            'total' => $total,
            'pages' => $pages,
            'status_counts' => $statusCounts,
            'total_ingresos' => $totalIngresos,
            'total_egresos' => $totalEgresos,
            'balance' => $totalIngresos - $totalEgresos,
        ];
    }
}
