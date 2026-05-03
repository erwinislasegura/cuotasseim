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

        if (($config['route'] ?? '') === 'auditoria') {
            ModuleCatalog::bootstrapAuditIfEmpty();
        }

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
            $origenModulo = trim((string) ($_GET['origen_modulo'] ?? ''));
            $anio = max(0, (int) ($_GET['anio'] ?? 0));

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

            if ($anio > 0) {
                $from = sprintf('%04d-01-01', $anio);
                $to = sprintf('%04d-12-31', $anio);
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
                'origen_modulo' => $origenModulo,
                'anio' => $anio > 0 ? (string) $anio : '',
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
                    ['row_id', 'fecha', 'tipo_movimiento', 'origen_modulo', 'referencia_id', 'socio_nombre', 'socio_rut', 'periodo_a_pagar', 'descripcion', 'ingreso', 'egreso', 'saldo_referencial', 'usuario_registro'],
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
                    'origenes' => [
                        ['value' => '', 'label' => 'Todos los movimientos'],
                        ['value' => 'pago_cuotas', 'label' => 'Pago de cuotas'],
                        ['value' => 'aporte', 'label' => 'Aporte'],
                        ['value' => 'retiro', 'label' => 'Retiro'],
                        ['value' => 'manual', 'label' => 'Manual / ajuste'],
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
                    $descripcion = trim((string) ($row['descripcion'] ?? ''));
                    if (mb_strlen($descripcion) > 64) {
                        $descripcion = mb_substr($descripcion, 0, 61) . '...';
                    }
                    $row['descripcion'] = $descripcion;
                    return $row;
                }, $rendicionesResult['rows']),
                'columns' => ['row_id', 'fecha', 'tipo_movimiento', 'origen_modulo', 'referencia_id', 'socio_nombre', 'socio_rut', 'periodo_a_pagar', 'descripcion', 'ingreso', 'egreso', 'saldo_referencial', 'usuario_registro'],
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
                    'row_id' => '# Movimiento',
                    'tipo_movimiento' => 'Tipo',
                    'origen_modulo' => 'Origen',
                    'referencia_id' => 'Referencia',
                    'socio_nombre' => 'Socio / Titular',
                    'socio_rut' => 'RUT',
                    'periodo_a_pagar' => 'Periodo a pagar',
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
            $this->ensureSchemaTables();

            if (($config['table'] ?? '') === 'configuracion') {
                $this->ensureFlowConfigColumns();
            }

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
                    if (($config['route'] ?? '') === 'configuracion') {
                        if (($id ?? 0) <= 0) {
                            $stmtConfigId = Database::connection()->query('SELECT id FROM configuracion ORDER BY id ASC LIMIT 1');
                            $id = (int) ($stmtConfigId->fetchColumn() ?: 0);
                        }

                        $removeLogo = (string) ($_POST['eliminar_logo'] ?? '') === '1';
                        if ($removeLogo) {
                            $_POST['logo'] = '';
                        }

                        if (!empty($_FILES['logo_file']) && (int) ($_FILES['logo_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                            $tmpPath = (string) ($_FILES['logo_file']['tmp_name'] ?? '');
                            $originalName = (string) ($_FILES['logo_file']['name'] ?? '');
                            $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
                            $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
                            if ($tmpPath !== '' && in_array($ext, $allowed, true)) {
                                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/logos';
                                if (!is_dir($uploadDir)) {
                                    @mkdir($uploadDir, 0775, true);
                                }
                                $filename = 'logo_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                                $targetPath = $uploadDir . '/' . $filename;
                                if (@move_uploaded_file($tmpPath, $targetPath)) {
                                    $_POST['logo'] = 'public/uploads/logos/' . $filename;
                                }
                            }
                        }
                    }

                    $isUpdate = $id !== null && $id > 0;
                    $previousRecord = $isUpdate ? ModuleCatalog::findById($config['table'], $primaryKey, $id) : null;
                    if ($isUpdate && $previousRecord === null) {
                        $isUpdate = false;
                        $id = null;
                    }

                    $saveFields = $columnsMeta['form'];
                    if (($config['table'] ?? '') === 'configuracion') {
                        $saveFields = array_values(array_intersect([
                            'nombre_organizacion',
                            'nombre_sistema',
                            'logo',
                            'rut_organizacion',
                            'direccion',
                            'telefono',
                            'correo',
                            'sitio_web',
                            'flow_api_key',
                            'flow_secret_key',
                            'flow_modo_sandbox',
                        ], $columnsMeta['form']));
                    }

                    $savedId = ModuleCatalog::save($config['table'], $primaryKey, $saveFields, $_POST, $isUpdate ? $id : null);
                    $targetId = $savedId > 0 ? $savedId : ($isUpdate ? (int) $id : 0);
                    $currentRecord = $targetId > 0 ? ModuleCatalog::findById($config['table'], $primaryKey, $targetId) : null;

                    if ($savedId > 0) {
                        ModuleCatalog::registerAudit(
                            (string) ($config['route'] ?? $moduleKey),
                            $isUpdate ? 'actualizar' : 'crear',
                            $targetId > 0 ? $targetId : null,
                            is_array($previousRecord) ? $previousRecord : null,
                            is_array($currentRecord) ? $currentRecord : null
                        );

                        $_SESSION['flash_success'] = $isUpdate ? 'Registro actualizado correctamente.' : 'Registro creado correctamente.';
                    } else {
                        $_SESSION['flash_error'] = 'No se pudo guardar el registro. Verifica que los campos obligatorios tengan datos.';
                    }
                }

                if (!$isReadOnly && $action === 'delete' && $id !== null && $id > 0) {
                    $deletedRecord = ModuleCatalog::findById($config['table'], $primaryKey, $id);

                    try {
                        ModuleCatalog::delete($config['table'], $primaryKey, $id, $columnsMeta['has_deleted_at']);
                        ModuleCatalog::registerAudit(
                            (string) ($config['route'] ?? $moduleKey),
                            'eliminar',
                            $id,
                            is_array($deletedRecord) ? $deletedRecord : null,
                            null
                        );
                        $_SESSION['flash_success'] = 'Registro eliminado correctamente.';
                    } catch (\PDOException $exception) {
                        $errorCode = (string) ($exception->errorInfo[1] ?? '');
                        if ($errorCode === '1451') {
                            $_SESSION['flash_error'] = 'No se puede eliminar el registro porque tiene datos relacionados.';
                        } else {
                            $_SESSION['flash_error'] = 'No se pudo eliminar el registro. Inténtalo nuevamente.';
                        }
                    } catch (\Throwable $exception) {
                        $_SESSION['flash_error'] = 'No se pudo eliminar el registro. Inténtalo nuevamente.';
                    }
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

            if ($config['table'] === 'configuracion' && $currentRecord === null) {
                $stmtConfigActual = Database::connection()->query('SELECT * FROM configuracion ORDER BY id ASC LIMIT 1');
                $currentRecord = $stmtConfigActual->fetch() ?: null;
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

            if ($config['table'] === 'configuracion') {
                $formFields = array_values(array_intersect([
                    'nombre_organizacion',
                    'nombre_sistema',
                    'rut_organizacion',
                    'direccion',
                    'telefono',
                    'correo',
                    'sitio_web',
                    'flow_api_key',
                    'flow_secret_key',
                    'flow_modo_sandbox',
                ], $data['columns']['form']));
                $formMeta = [
                    'types' => [
                        'correo' => 'email',
                        'sitio_web' => 'url',
                        'flow_modo_sandbox' => 'select',
                    ],
                    'options' => [
                        'flow_modo_sandbox' => [
                            ['value' => '1', 'label' => 'Sí (Sandbox)'],
                            ['value' => '0', 'label' => 'No (Producción)'],
                        ],
                    ],
                    'labels' => [
                        'nombre_organizacion' => 'Nombre institución',
                        'nombre_sistema' => 'Nombre sistema',
                        'rut_organizacion' => 'RUT institución',
                        'direccion' => 'Dirección',
                        'telefono' => 'Teléfono',
                        'correo' => 'Correo',
                        'sitio_web' => 'Sitio web',
                        'logo' => 'Logo institucional',
                        'flow_api_key' => 'Flow Api Key',
                        'flow_secret_key' => 'Flow Secret Key',
                        'flow_modo_sandbox' => 'Flow Modo Sandbox',
                    ],
                ];
                $columnLabels = $formMeta['labels'];
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
                    'row_id' => '# Movimiento',
                    'fecha' => 'Fecha',
                    'tipo_movimiento' => 'Tipo',
                    'origen_modulo' => 'Origen',
                    'referencia_id' => 'Referencia',
                    'socio_nombre' => 'Socio / Titular',
                    'socio_rut' => 'RUT',
                    'periodo_a_pagar' => 'Periodo a pagar',
                    'descripcion' => 'Descripción',
                    'ingreso' => 'Ingreso',
                    'egreso' => 'Egreso',
                    'saldo_referencial' => 'Saldo referencial',
                    'usuario_registro' => 'Registrado por',
                ];
                $visibleColumns = array_values(array_intersect([
                    'row_id',
                    'fecha',
                    'tipo_movimiento',
                    'origen_modulo',
                    'referencia_id',
                    'socio_nombre',
                    'socio_rut',
                    'periodo_a_pagar',
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
                    'origenes' => [
                        ['value' => '', 'label' => 'Todos los movimientos'],
                        ['value' => 'pago_cuotas', 'label' => 'Pago de cuotas'],
                        ['value' => 'aporte', 'label' => 'Aporte'],
                        ['value' => 'retiro', 'label' => 'Retiro'],
                        ['value' => 'manual', 'label' => 'Manual / ajuste'],
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

    private function ensureSchemaTables(): void
    {
        static $schemaChecked = false;
        if ($schemaChecked) {
            return;
        }

        $schemaChecked = true;

        try {
            $schemaFile = dirname(__DIR__, 2) . '/database/schema.sql';
            if (!is_file($schemaFile)) {
                return;
            }

            $sqlContent = (string) file_get_contents($schemaFile);
            if ($sqlContent === '') {
                return;
            }

            $db = Database::connection();
            $statements = array_filter(array_map('trim', explode(';', $sqlContent)));

            foreach ($statements as $statement) {
                $normalized = strtoupper(ltrim($statement));
                if (!str_starts_with($normalized, 'CREATE TABLE ')) {
                    continue;
                }

                $safeStatement = preg_replace('/^CREATE TABLE\s+/i', 'CREATE TABLE IF NOT EXISTS ', $statement);
                if (!is_string($safeStatement) || trim($safeStatement) === '') {
                    continue;
                }

                try {
                    $db->exec($safeStatement);
                } catch (\Throwable) {
                    // Evita detener el flujo si existe una dependencia o permiso faltante.
                }
            }
        } catch (\Throwable) {
            // Sin bloqueo: se continuará con el flujo normal y mensaje de error del módulo si aplica.
        }
    }


    private function ensureFlowConfigColumns(): void
    {
        try {
            $db = Database::connection();
            $db->exec("
                CREATE TABLE IF NOT EXISTS configuracion (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    nombre_organizacion VARCHAR(140) NULL,
                    nombre_sistema VARCHAR(140) NULL,
                    logo VARCHAR(255) NULL,
                    rut_organizacion VARCHAR(30) NULL,
                    direccion VARCHAR(255) NULL,
                    telefono VARCHAR(40) NULL,
                    correo VARCHAR(120) NULL,
                    sitio_web VARCHAR(120) NULL,
                    flow_api_key VARCHAR(120) NULL,
                    flow_secret_key VARCHAR(140) NULL,
                    flow_modo_sandbox TINYINT(1) NOT NULL DEFAULT 1,
                    cuota_por_defecto DECIMAL(12,2) DEFAULT 0,
                    moneda VARCHAR(20) DEFAULT 'CLP',
                    simbolo_moneda VARCHAR(5) DEFAULT '$',
                    texto_comprobante TEXT NULL,
                    observaciones_generales TEXT NULL,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $required = [
                'flow_api_key' => "ALTER TABLE configuracion ADD COLUMN flow_api_key VARCHAR(120) NULL AFTER sitio_web",
                'flow_secret_key' => "ALTER TABLE configuracion ADD COLUMN flow_secret_key VARCHAR(140) NULL AFTER flow_api_key",
                'flow_modo_sandbox' => "ALTER TABLE configuracion ADD COLUMN flow_modo_sandbox TINYINT(1) NOT NULL DEFAULT 1 AFTER flow_secret_key",
            ];

            foreach ($required as $column => $sql) {
                $stmt = $db->prepare('SHOW COLUMNS FROM configuracion LIKE :column_name');
                $stmt->bindValue(':column_name', $column);
                $stmt->execute();
                $exists = $stmt->fetch();
                if ($exists) {
                    continue;
                }

                try {
                    $db->exec($sql);
                } catch (\Throwable) {
                    // Sin permisos ALTER o motor restringido: no bloquear la carga del módulo.
                }
            }
        } catch (\Throwable) {
            // Evita romper /configuracion por validaciones de esquema.
        }
    }

    private function buildRendicionesDataset(string $query, string $status, string $from, string $to, array $extraFilters): array
    {
        $db = Database::connection();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $params = [];
        $dateWherePagos = '';
        $dateWhereAportes = '';
        $dateWhereEgresos = '';
        $dateWhereTesoreria = '';

        if ($from !== '') {
            $dateWherePagos .= ' AND DATE(p.fecha_pago) >= :from_date_pagos';
            $dateWhereAportes .= ' AND DATE(a.fecha_aporte) >= :from_date_aportes';
            $dateWhereEgresos .= ' AND DATE(e.fecha) >= :from_date_egresos';
            $dateWhereTesoreria .= ' AND DATE(mt.fecha) >= :from_date_mt';
            $params[':from_date_pagos'] = $from;
            $params[':from_date_aportes'] = $from;
            $params[':from_date_egresos'] = $from;
            $params[':from_date_mt'] = $from;
        }
        if ($to !== '') {
            $dateWherePagos .= ' AND DATE(p.fecha_pago) <= :to_date_pagos';
            $dateWhereAportes .= ' AND DATE(a.fecha_aporte) <= :to_date_aportes';
            $dateWhereEgresos .= ' AND DATE(e.fecha) <= :to_date_egresos';
            $dateWhereTesoreria .= ' AND DATE(mt.fecha) <= :to_date_mt';
            $params[':to_date_pagos'] = $to;
            $params[':to_date_aportes'] = $to;
            $params[':to_date_egresos'] = $to;
            $params[':to_date_mt'] = $to;
        }

        $sql = "
            SELECT
                CONCAT('P-', p.id) AS row_id,
                p.id,
                p.fecha_pago AS fecha,
                'ingreso' AS tipo_movimiento,
                'Pago de cuotas' AS origen_modulo,
                p.id AS referencia_id,
                CONCAT(
                    'Pago cuota ',
                    COALESCE(p.numero_comprobante, CONCAT('#', p.id)),
                    ' · ',
                    COALESCE(s.nombre_completo, 'Socio'),
                    CASE
                        WHEN COALESCE(NULLIF(TRIM(p.periodo_a_pagar), ''), periodos_pago.periodo_a_pagar, '') <> ''
                            THEN CONCAT(' · Periodo: ', COALESCE(NULLIF(TRIM(p.periodo_a_pagar), ''), periodos_pago.periodo_a_pagar))
                        ELSE ''
                    END
                ) AS descripcion,
                COALESCE(p.monto_total, 0) AS ingreso,
                0 AS egreso,
                COALESCE(mt.saldo_referencial, 0) AS saldo_referencial,
                COALESCE(s.id, 0) AS socio_id,
                COALESCE(s.rut, '') AS socio_rut,
                COALESCE(s.nombre_completo, '') AS socio_nombre,
                COALESCE(NULLIF(TRIM(p.periodo_a_pagar), ''), periodos_pago.periodo_a_pagar, '') AS periodo_a_pagar,
                COALESCE(up.nombre, up.usuario, CONCAT('Usuario #', COALESCE(up.id, 0)), 'Sistema') AS usuario_registro
            FROM pagos p
            INNER JOIN socios s ON s.id = p.socio_id
            LEFT JOIN usuarios up ON up.id = p.usuario_id
            LEFT JOIN (
                SELECT
                    pd.pago_id,
                    GROUP_CONCAT(
                        DISTINCT TRIM(
                            CASE
                                WHEN COALESCE(pe.tipo_periodo, 'mensual') = 'mensual' THEN CONCAT(
                                    'Mes ',
                                    ELT(
                                        COALESCE(pe.mes, MONTH(pe.fecha_inicio), MONTH(c.fecha_vencimiento), 1),
                                        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                                        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
                                    ),
                                    ' ',
                                    COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                                )
                                WHEN COALESCE(pe.tipo_periodo, '') = 'trimestral' THEN CONCAT(
                                    'Trimestre ',
                                    ELT(
                                        CEIL(COALESCE(pe.mes, MONTH(pe.fecha_inicio), MONTH(c.fecha_vencimiento), 1) / 3),
                                        'uno', 'dos', 'tres', 'cuatro'
                                    ),
                                    ' ',
                                    COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                                )
                                WHEN COALESCE(pe.tipo_periodo, '') = 'semestral' THEN CONCAT(
                                    'Semestre ',
                                    ELT(
                                        CASE
                                            WHEN COALESCE(pe.mes, MONTH(pe.fecha_inicio), MONTH(c.fecha_vencimiento), 1) <= 6 THEN 1
                                            ELSE 2
                                        END,
                                        'uno',
                                        'dos'
                                    ),
                                    ' ',
                                    COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                                )
                                WHEN COALESCE(pe.tipo_periodo, '') = 'anual' THEN CONCAT(
                                    'Año ',
                                    COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                                )
                                ELSE TRIM(
                                    CONCAT(
                                        COALESCE(pe.nombre_periodo, ''),
                                        CASE
                                            WHEN pe.anio IS NOT NULL THEN CONCAT(' ', pe.anio)
                                            ELSE ''
                                        END
                                    )
                                )
                            END
                        )
                        ORDER BY pe.anio DESC, pe.mes DESC
                        SEPARATOR ', '
                    ) AS periodo_a_pagar
                FROM pago_detalle pd
                INNER JOIN cuotas c ON c.id = pd.cuota_id
                LEFT JOIN periodos pe ON pe.id = c.periodo_id
                GROUP BY pd.pago_id
            ) periodos_pago ON periodos_pago.pago_id = p.id
            LEFT JOIN movimientos_tesoreria mt ON mt.origen_modulo = 'pagos' AND mt.referencia_id = p.id
            WHERE p.deleted_at IS NULL AND p.estado_pago <> 'anulado' {$dateWherePagos}

            UNION ALL

            SELECT
                CONCAT('A-', a.id) AS row_id,
                a.id,
                a.fecha_aporte AS fecha,
                'ingreso' AS tipo_movimiento,
                'Aporte' AS origen_modulo,
                a.id AS referencia_id,
                CONCAT('Aporte ', COALESCE(a.comentario, a.descripcion, ''), ' · ', COALESCE(s.nombre_completo, a.nombre_aportante, 'Aportante')) AS descripcion,
                COALESCE(a.monto, 0) AS ingreso,
                0 AS egreso,
                COALESCE(mt.saldo_referencial, 0) AS saldo_referencial,
                COALESCE(s.id, 0) AS socio_id,
                COALESCE(s.rut, '') AS socio_rut,
                COALESCE(s.nombre_completo, a.nombre_aportante, '') AS socio_nombre,
                '' AS periodo_a_pagar,
                COALESCE(ua.nombre, ua.usuario, CONCAT('Usuario #', COALESCE(ua.id, 0)), 'Sistema') AS usuario_registro
            FROM aportes a
            LEFT JOIN socios s ON s.id = a.socio_id
            LEFT JOIN usuarios ua ON ua.id = a.usuario_id
            LEFT JOIN movimientos_tesoreria mt ON mt.origen_modulo = 'aportes' AND mt.referencia_id = a.id
            WHERE a.estado <> 'anulado' {$dateWhereAportes}

            UNION ALL

            SELECT
                CONCAT('E-', e.id) AS row_id,
                e.id,
                e.fecha AS fecha,
                'egreso' AS tipo_movimiento,
                'Retiro' AS origen_modulo,
                e.id AS referencia_id,
                CONCAT('Retiro ', COALESCE(e.numero_documento, CONCAT('#', e.id)), ' · ', COALESCE(e.proveedor_destinatario, ''), ' · ', COALESCE(e.descripcion, '')) AS descripcion,
                0 AS ingreso,
                COALESCE(e.monto, 0) AS egreso,
                COALESCE(mt.saldo_referencial, 0) AS saldo_referencial,
                0 AS socio_id,
                '' AS socio_rut,
                COALESCE(e.proveedor_destinatario, '') AS socio_nombre,
                '' AS periodo_a_pagar,
                COALESCE(ue.nombre, ue.usuario, CONCAT('Usuario #', COALESCE(ue.id, 0)), 'Sistema') AS usuario_registro
            FROM egresos e
            LEFT JOIN usuarios ue ON ue.id = e.usuario_id
            LEFT JOIN movimientos_tesoreria mt ON mt.origen_modulo = 'egresos' AND mt.referencia_id = e.id
            WHERE e.deleted_at IS NULL AND e.estado <> 'anulado' {$dateWhereEgresos}

            UNION ALL

            SELECT
                CONCAT('MT-', mt.id) AS row_id,
                mt.id,
                mt.fecha,
                mt.tipo_movimiento,
                CASE
                    WHEN mt.origen_modulo LIKE 'ajuste_pagos%' THEN 'Pago de cuotas'
                    WHEN mt.origen_modulo LIKE 'ajuste_aportes%' THEN 'Aporte'
                    WHEN mt.origen_modulo LIKE 'ajuste_egresos%' THEN 'Retiro'
                    WHEN mt.origen_modulo = 'pagos' THEN 'Pago de cuotas'
                    WHEN mt.origen_modulo = 'aportes' THEN 'Aporte'
                    WHEN mt.origen_modulo = 'egresos' THEN 'Retiro'
                    WHEN mt.tipo_movimiento = 'egreso' THEN 'Retiro'
                    WHEN mt.tipo_movimiento = 'ingreso' THEN 'Ingreso manual'
                    ELSE 'Manual'
                END AS origen_modulo,
                mt.referencia_id,
                COALESCE(mt.descripcion, '') AS descripcion,
                COALESCE(mt.ingreso, 0) AS ingreso,
                COALESCE(mt.egreso, 0) AS egreso,
                COALESCE(mt.saldo_referencial, 0) AS saldo_referencial,
                0 AS socio_id,
                '' AS socio_rut,
                '' AS socio_nombre,
                '' AS periodo_a_pagar,
                'Sistema / Ajuste manual' AS usuario_registro
            FROM movimientos_tesoreria mt
            LEFT JOIN pagos p ON mt.origen_modulo = 'pagos' AND mt.referencia_id = p.id
            LEFT JOIN aportes a ON mt.origen_modulo = 'aportes' AND mt.referencia_id = a.id
            LEFT JOIN egresos e ON mt.origen_modulo = 'egresos' AND mt.referencia_id = e.id
            WHERE (
                mt.origen_modulo NOT IN ('pagos', 'aportes', 'egresos')
                OR (mt.origen_modulo = 'pagos' AND p.id IS NULL)
                OR (mt.origen_modulo = 'aportes' AND a.id IS NULL)
                OR (mt.origen_modulo = 'egresos' AND e.id IS NULL)
            ) {$dateWhereTesoreria}
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
        $origenFiltro = mb_strtolower(trim((string) ($extraFilters['origen_modulo'] ?? '')));
        $queryLower = mb_strtolower(trim($query));

        $filtered = array_values(array_filter($allRows, static function (array $row) use ($status, $socioFilter, $socioFilterRut, $socioFilterNombre, $montoMin, $montoMax, $origenFiltro, $queryLower): bool {
            $tipo = mb_strtolower(trim((string) ($row['tipo_movimiento'] ?? '')));
            $statusFilter = mb_strtolower(trim((string) $status));
            if ($statusFilter !== '' && $tipo !== $statusFilter) {
                return false;
            }
            if ($origenFiltro !== '') {
                $origenTexto = mb_strtolower(trim((string) ($row['origen_modulo'] ?? '')));
                $origenNormalizado = 'manual';
                if (
                    str_contains($origenTexto, 'pago')
                    || str_contains($origenTexto, 'cuota')
                ) {
                    $origenNormalizado = 'pago_cuotas';
                } elseif (
                    str_contains($origenTexto, 'aporte')
                    || str_contains($origenTexto, 'abono')
                ) {
                    $origenNormalizado = 'aporte';
                } elseif (
                    str_contains($origenTexto, 'retiro')
                    || str_contains($origenTexto, 'egreso')
                    || str_contains($origenTexto, 'gasto')
                ) {
                    $origenNormalizado = 'retiro';
                }
                if ($origenNormalizado !== $origenFiltro) {
                    return false;
                }
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
                $haystack = mb_strtolower(trim((string) (($row['descripcion'] ?? '') . ' ' . ($row['periodo_a_pagar'] ?? '') . ' ' . ($row['origen_modulo'] ?? '') . ' ' . ($row['socio_nombre'] ?? '') . ' ' . ($row['socio_rut'] ?? '') . ' ' . ($row['usuario_registro'] ?? ''))));
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
