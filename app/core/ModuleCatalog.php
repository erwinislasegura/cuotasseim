<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

class ModuleCatalog
{
    /** @var array<string,array<string,array{table:string,label_column:string,primary:string}>> */
    private static array $foreignKeyCache = [];
    /** @var array<string,array<int,string>> */
    private static array $foreignLabelCache = [];

    public static function config(string $key): ?array
    {
        $modules = [
            'socios' => ['title' => 'Socios', 'description' => 'Administración del padrón de socios con estado, deuda y seguimiento.', 'table' => 'socios', 'route' => 'socios'],
            'tipos_socio' => ['title' => 'Tipos de socio', 'description' => 'Catálogo de categorías y segmentación de socios.', 'table' => 'tipos_socio', 'route' => 'tipos-socio'],
            'periodos' => ['title' => 'Planes', 'description' => 'Creación de planes de cobro por frecuencia.', 'table' => 'periodos', 'route' => 'periodos'],
            'conceptos_cobro' => ['title' => 'Conceptos de cobro', 'description' => 'Definición de conceptos recurrentes y extraordinarios.', 'table' => 'conceptos_cobro', 'route' => 'conceptos-cobro'],
            'cuotas' => ['title' => 'Registro de cuotas', 'description' => 'Selección de socio para revisar cuota por vencer y cuotas pendientes.', 'table' => 'cuotas', 'route' => 'cuotas'],
            'medios_pago' => ['title' => 'Medios de pago', 'description' => 'Catálogo de medios habilitados para recaudación.', 'table' => 'medios_pago', 'route' => 'medios-pago'],
            'pagos' => ['title' => 'Historial de pagos', 'description' => 'Consulta de pagos realizados con detalle completo por operación.', 'table' => 'pagos', 'route' => 'pagos'],
            'tipos_aporte' => ['title' => 'Tipos de aporte', 'description' => 'Clasificación de aportes extraordinarios.', 'table' => 'tipos_aporte', 'route' => 'tipos-aporte'],
            'aportes' => ['title' => 'Aportes', 'description' => 'Seguimiento de aportes extraordinarios por origen y estado.', 'table' => 'aportes', 'route' => 'aportes'],
            'tipos_egreso' => ['title' => 'Tipos de egreso', 'description' => 'Clasificación de egresos para tesorería y rendiciones.', 'table' => 'tipos_egreso', 'route' => 'tipos-egreso'],
            'egresos' => ['title' => 'Egresos', 'description' => 'Control operativo de egresos, comprobantes y estado.', 'table' => 'egresos', 'route' => 'egresos'],
            'rendiciones' => ['title' => 'Rendiciones', 'description' => 'Consulta consolidada de ingresos y egresos por periodo y socio.', 'table' => 'movimientos_tesoreria', 'route' => 'rendiciones', 'read_only' => true],
            'tesoreria' => ['title' => 'Movimientos de tesorería', 'description' => 'Seguimiento de movimientos, diferencias y conciliación.', 'table' => 'movimientos_tesoreria', 'route' => 'tesoreria'],
            'roles' => ['title' => 'Roles', 'description' => 'Administración de perfiles de acceso.', 'table' => 'roles', 'route' => 'roles'],
            'usuarios' => ['title' => 'Usuarios', 'description' => 'Gestión de usuarios y permisos operativos.', 'table' => 'usuarios', 'route' => 'usuarios'],
            'configuracion' => ['title' => 'Configuración general', 'description' => 'Parámetros institucionales y reglas base del sistema.', 'table' => 'configuracion', 'route' => 'configuracion'],
            'auditoria' => ['title' => 'Auditoría', 'description' => 'Trazabilidad por módulo, usuario y acción.', 'table' => 'auditoria', 'route' => 'auditoria', 'read_only' => true],
            'reportes' => ['title' => 'Reportes', 'description' => 'Vista consolidada con foco ejecutivo y operativo.', 'table' => 'cuotas', 'route' => 'reportes', 'read_only' => true],
        ];

        return $modules[$key] ?? null;
    }

    public static function resolveColumns(PDO $db, string $table): array
    {
        $stmt = $db->query('DESCRIBE `' . $table . '`');
        $meta = $stmt->fetchAll();

        $all = array_map(static fn(array $row) => (string) $row['Field'], $meta);
        $primaryKey = 'id';

        foreach ($meta as $row) {
            if (($row['Key'] ?? '') === 'PRI') {
                $primaryKey = (string) $row['Field'];
                break;
            }
        }

        $excluded = ['password'];
        $columns = array_values(array_filter($all, static fn(string $column) => !in_array($column, $excluded, true)));

        $formExcluded = [$primaryKey, 'created_at', 'updated_at', 'deleted_at'];
        $form = array_values(array_filter($columns, static fn(string $column) => !in_array($column, $formExcluded, true)));

        $statusFields = ['estado', 'estado_cuota', 'estado_pago', 'activo', 'cerrado', 'tipo_movimiento'];
        $statusField = null;
        foreach ($statusFields as $field) {
            if (in_array($field, $columns, true)) {
                $statusField = $field;
                break;
            }
        }

        $dateField = null;
        foreach (['fecha_pago', 'fecha_vencimiento', 'fecha', 'fecha_aporte', 'fecha_inicio', 'fecha_desde', 'created_at'] as $field) {
            if (in_array($field, $columns, true)) {
                $dateField = $field;
                break;
            }
        }

        return [
            'all' => $columns,
            'visible' => array_slice($columns, 0, 9),
            'searchable' => array_slice(array_values(array_filter($columns, static fn(string $column) => str_contains($column, 'nombre') || str_contains($column, 'numero') || str_contains($column, 'descripcion') || str_contains($column, 'correo') || str_contains($column, 'rut') || str_contains($column, 'modulo'))), 0, 5),
            'form' => array_slice($form, 0, 12),
            'primary' => $primaryKey,
            'status_field' => $statusField,
            'date_field' => $dateField,
            'has_deleted_at' => in_array('deleted_at', $columns, true),
        ];
    }

    public static function exportCsv(string $fileName, array $columns, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $output = fopen('php://output', 'wb');
        if ($output === false) {
            return;
        }

        fputcsv($output, $columns, ';');
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $column) {
                $line[] = (string) ($row[$column] ?? '');
            }
            fputcsv($output, $line, ';');
        }
        fclose($output);
    }

    public static function fetchData(
        array $config,
        string $query,
        int $page,
        int $perPage,
        ?string $status = null,
        ?string $from = null,
        ?string $to = null,
        array $extraConditions = []
    ): array
    {
        $db = Database::connection();
        $columnsMeta = self::resolveColumns($db, $config['table']);
        $searchColumns = $columnsMeta['searchable'];

        [$whereSql, $params] = self::buildWhereSql($columnsMeta, $query, $status, $from, $to, $extraConditions);

        $countStmt = $db->prepare('SELECT COUNT(*) FROM `' . $config['table'] . '`' . $whereSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $sql = 'SELECT * FROM `' . $config['table'] . '`' . $whereSql . ' ORDER BY `' . $columnsMeta['primary'] . '` DESC LIMIT :limit OFFSET :offset';
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $statusCounts = self::fetchStatusCounts($db, $config['table'], $columnsMeta, $query, $from, $to, $extraConditions);

        $summary = [
            'total' => $total,
            'visibles' => (int) count($stmt->fetchAll(PDO::FETCH_ASSOC)),
            'status_field' => $columnsMeta['status_field'],
            'status_counts' => $statusCounts,
        ];

        // Re-run list fetch because count for visibles consumed cursor.
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'rows' => $stmt->fetchAll(),
            'total' => $total,
            'columns' => $columnsMeta,
            'query' => $query,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
        ];
    }

    public static function findById(string $table, string $primaryKey, int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM `' . $table . '` WHERE `' . $primaryKey . '` = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function decorateRowsForDisplay(string $table, array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        return array_map(static fn(array $row): array => self::decorateRecordForDisplay($table, $row), $rows);
    }

    public static function decorateRecordForDisplay(string $table, ?array $record): ?array
    {
        if ($record === null) {
            return null;
        }

        $db = Database::connection();
        $foreignKeys = self::foreignKeysForTable($db, $table);
        if (empty($foreignKeys)) {
            return $record;
        }

        foreach ($foreignKeys as $column => $reference) {
            if (!array_key_exists($column, $record)) {
                continue;
            }

            $rawValue = $record[$column];
            if ($rawValue === null || $rawValue === '') {
                continue;
            }

            $id = (int) $rawValue;
            if ($id <= 0) {
                continue;
            }

            $label = self::foreignLabel((string) $reference['table'], (string) $reference['label_column'], $id, (string) $reference['primary']);
            if ($label !== null && $label !== '') {
                $record[$column] = $label;
            }
        }

        return $record;
    }

    public static function save(string $table, string $primaryKey, array $fields, array $payload, ?int $id = null): void
    {
        $persistFields = $fields;
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = trim((string) ($payload[$field] ?? ''));
            if ($data[$field] === '') {
                $data[$field] = null;
            }
        }

        if ($table === 'socios') {
            $data['nombre_completo'] = trim(implode(' ', array_filter([
                (string) ($data['nombres'] ?? ''),
                (string) ($data['apellidos'] ?? ''),
            ])));
            if ($data['nombre_completo'] === '') {
                $data['nombre_completo'] = null;
            }

            if ($id === null && empty($data['numero_socio'])) {
                $data['numero_socio'] = self::nextSocioNumber();
            }
        }

        if ($table === 'periodos') {
            $data['anio'] = (string) ($data['anio'] ?? date('Y'));
            $data['mes'] = (string) ($data['mes'] ?? date('n'));
            $data['cerrado'] = (string) ($data['cerrado'] ?? '0');
        }

        if ($table === 'aportes') {
            $comentario = trim((string) ($data['comentario'] ?? $payload['comentario'] ?? ''));
            $comentario = $comentario !== '' ? $comentario : null;

            if (self::columnExists('aportes', 'comentario')) {
                $data['comentario'] = $comentario;
                if (!in_array('comentario', $persistFields, true)) {
                    $persistFields[] = 'comentario';
                }
            }

            if (self::columnExists('aportes', 'descripcion')) {
                $data['descripcion'] = $comentario;
                if (!in_array('descripcion', $persistFields, true)) {
                    $persistFields[] = 'descripcion';
                }
            }

            if (self::columnExists('aportes', 'observacion')) {
                $data['observacion'] = $comentario;
                if (!in_array('observacion', $persistFields, true)) {
                    $persistFields[] = 'observacion';
                }
            }

            $socioId = (int) ($data['socio_id'] ?? 0);
            if ($socioId > 0) {
                $stmtSocio = Database::connection()->prepare('SELECT nombre_completo, nombres, apellidos FROM socios WHERE id = :id LIMIT 1');
                $stmtSocio->bindValue(':id', $socioId, PDO::PARAM_INT);
                $stmtSocio->execute();
                $socio = $stmtSocio->fetch() ?: [];
                $nombreAportante = trim((string) ($socio['nombre_completo'] ?? ''));
                if ($nombreAportante === '') {
                    $nombreAportante = trim(implode(' ', array_filter([
                        (string) ($socio['nombres'] ?? ''),
                        (string) ($socio['apellidos'] ?? ''),
                    ])));
                }

                if (self::columnExists('aportes', 'nombre_aportante')) {
                    $data['nombre_aportante'] = $nombreAportante !== '' ? $nombreAportante : null;
                    if (!in_array('nombre_aportante', $persistFields, true)) {
                        $persistFields[] = 'nombre_aportante';
                    }
                }
            }

            if (self::columnExists('aportes', 'fecha_aporte')) {
                $data['fecha_aporte'] = (string) ($data['fecha_aporte'] ?? date('Y-m-d'));
                if (!in_array('fecha_aporte', $persistFields, true)) {
                    $persistFields[] = 'fecha_aporte';
                }
            }

            if (self::columnExists('aportes', 'estado')) {
                $data['estado'] = (string) ($data['estado'] ?? 'aplicado');
                if (!in_array('estado', $persistFields, true)) {
                    $persistFields[] = 'estado';
                }
            }

            if (self::columnExists('aportes', 'usuario_id')) {
                $usuario = Auth::user();
                $data['usuario_id'] = (string) ((int) ($usuario['id'] ?? $_SESSION['user_id'] ?? 1));
                if (!in_array('usuario_id', $persistFields, true)) {
                    $persistFields[] = 'usuario_id';
                }
            }

            if (self::columnExists('aportes', 'tipo_aporte_id')) {
                $tipoAporteId = 0;
                $stmtTipo = Database::connection()->query("SELECT id FROM tipos_aporte WHERE activo = 1 AND LOWER(nombre) LIKE 'don%' ORDER BY id ASC LIMIT 1");
                $tipoAporteId = (int) ($stmtTipo->fetchColumn() ?: 0);
                if ($tipoAporteId <= 0) {
                    $stmtTipo = Database::connection()->query('SELECT id FROM tipos_aporte WHERE activo = 1 ORDER BY id ASC LIMIT 1');
                    $tipoAporteId = (int) ($stmtTipo->fetchColumn() ?: 0);
                }
                $data['tipo_aporte_id'] = $tipoAporteId > 0 ? (string) $tipoAporteId : null;
                if (!in_array('tipo_aporte_id', $persistFields, true)) {
                    $persistFields[] = 'tipo_aporte_id';
                }
            }
        }

        if ($table === 'egresos') {
            if (self::columnExists('egresos', 'fecha') && empty($data['fecha'])) {
                $data['fecha'] = date('Y-m-d');
                if (!in_array('fecha', $persistFields, true)) {
                    $persistFields[] = 'fecha';
                }
            }

            if (self::columnExists('egresos', 'numero_documento')) {
                $numeroComprobante = trim((string) ($data['numero_documento'] ?? ''));
                if ($numeroComprobante === '') {
                    $numeroComprobante = self::nextEgresoDocumentNumber();
                }
                $data['numero_documento'] = $numeroComprobante;
                if (!in_array('numero_documento', $persistFields, true)) {
                    $persistFields[] = 'numero_documento';
                }
            }

            $formaRetiro = strtolower(trim((string) ($payload['forma_retiro'] ?? '')));
            if (self::columnExists('egresos', 'observacion')) {
                $observacion = null;
                if ($formaRetiro !== '') {
                    $formaRetiroLabel = ucfirst($formaRetiro);
                    $observacion = 'Forma de retiro: ' . $formaRetiroLabel;
                }
                $data['observacion'] = $observacion;
                if (!in_array('observacion', $persistFields, true)) {
                    $persistFields[] = 'observacion';
                }
            }

            if (self::columnExists('egresos', 'cuenta_bancaria_id')) {
                $data['cuenta_bancaria_id'] = null;
                if (!in_array('cuenta_bancaria_id', $persistFields, true)) {
                    $persistFields[] = 'cuenta_bancaria_id';
                }
            }

            if (self::columnExists('egresos', 'estado')) {
                $data['estado'] = (string) ($data['estado'] ?? 'aplicado');
                if (!in_array('estado', $persistFields, true)) {
                    $persistFields[] = 'estado';
                }
            }

            if (self::columnExists('egresos', 'usuario_id')) {
                $usuario = Auth::user();
                $data['usuario_id'] = (string) ((int) ($usuario['id'] ?? $_SESSION['user_id'] ?? 1));
                if (!in_array('usuario_id', $persistFields, true)) {
                    $persistFields[] = 'usuario_id';
                }
            }
        }

        if ($table === 'movimientos_tesoreria') {
            if (empty($data['fecha'])) {
                $data['fecha'] = date('Y-m-d');
                if (!in_array('fecha', $persistFields, true)) {
                    $persistFields[] = 'fecha';
                }
            }

            $tipo = strtolower(trim((string) ($data['tipo_movimiento'] ?? 'ingreso')));
            if (!in_array($tipo, ['ingreso', 'egreso'], true)) {
                $tipo = 'ingreso';
            }
            $data['tipo_movimiento'] = $tipo;
            if (!in_array('tipo_movimiento', $persistFields, true)) {
                $persistFields[] = 'tipo_movimiento';
            }

            $ingreso = (float) ($data['ingreso'] ?? 0);
            $egreso = (float) ($data['egreso'] ?? 0);
            if ($tipo === 'ingreso') {
                $ingreso = $ingreso > 0 ? $ingreso : 0;
                $egreso = 0.0;
            } else {
                $egreso = $egreso > 0 ? $egreso : 0;
                $ingreso = 0.0;
            }
            $data['ingreso'] = (string) $ingreso;
            $data['egreso'] = (string) $egreso;
            if (!in_array('ingreso', $persistFields, true)) {
                $persistFields[] = 'ingreso';
            }
            if (!in_array('egreso', $persistFields, true)) {
                $persistFields[] = 'egreso';
            }

            $data['origen_modulo'] = trim((string) ($data['origen_modulo'] ?? 'manual')) ?: 'manual';
            if (!in_array('origen_modulo', $persistFields, true)) {
                $persistFields[] = 'origen_modulo';
            }

            $referenciaId = (int) ($data['referencia_id'] ?? 0);
            $data['referencia_id'] = $referenciaId > 0 ? (string) $referenciaId : null;
            if (!in_array('referencia_id', $persistFields, true)) {
                $persistFields[] = 'referencia_id';
            }

            if (!in_array('saldo_referencial', $persistFields, true)) {
                $persistFields[] = 'saldo_referencial';
            }
            $data['saldo_referencial'] = '0';
        }

        $db = Database::connection();

        if ($id !== null) {
            $sets = [];
            foreach ($persistFields as $field) {
                $sets[] = "`{$field}` = :{$field}";
            }
            $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $sets) . ' WHERE `' . $primaryKey . '` = :pk';
            $stmt = $db->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(':' . $field, $value);
            }
            $stmt->bindValue(':pk', $id, PDO::PARAM_INT);
            $stmt->execute();

            if (in_array($table, ['pagos', 'aportes', 'egresos'], true)) {
                self::syncTreasuryMovementFromSource($table, $id);
            }

            if ($table === 'socios' && self::tableExists('socio_planes')) {
                self::syncSocioPlanes($id, self::normalizeIds($payload['planes_ids'] ?? []));
            }
            if ($table === 'movimientos_tesoreria') {
                self::recalculateTreasuryBalance();
            }
            return;
        }

        $insertData = array_filter($data, static fn($value) => $value !== null);

        if (empty($insertData)) {
            return;
        }

        $columns = array_keys($insertData);
        $params = array_map(static fn(string $column) => ':' . $column, $columns);

        $sql = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $columns) . '`) VALUES (' . implode(',', $params) . ')';
        $stmt = $db->prepare($sql);
        foreach ($insertData as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }
        $stmt->execute();

        $newId = (int) $db->lastInsertId();
        if ($newId > 0 && in_array($table, ['pagos', 'aportes', 'egresos'], true)) {
            self::syncTreasuryMovementFromSource($table, $newId);
        }

        if ($table === 'socios' && self::tableExists('socio_planes')) {
            $socioId = $newId;
            if ($socioId > 0) {
                self::syncSocioPlanes($socioId, self::normalizeIds($payload['planes_ids'] ?? []));
            }
        }
        if ($table === 'movimientos_tesoreria') {
            self::recalculateTreasuryBalance();
        }
    }

    public static function fetchSocioPlanes(int $socioId): array
    {
        if ($socioId <= 0 || !self::tableExists('socio_planes')) {
            return [];
        }

        $stmt = Database::connection()->prepare('SELECT periodo_id FROM socio_planes WHERE socio_id = :socio_id ORDER BY periodo_id ASC');
        $stmt->bindValue(':socio_id', $socioId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(static fn(array $row): string => (string) ($row['periodo_id'] ?? ''), $stmt->fetchAll());
    }

    private static function syncSocioPlanes(int $socioId, array $planIds): void
    {
        if ($socioId <= 0) {
            return;
        }

        $db = Database::connection();
        $db->beginTransaction();
        try {
            $deleteStmt = $db->prepare('DELETE FROM socio_planes WHERE socio_id = :socio_id');
            $deleteStmt->bindValue(':socio_id', $socioId, PDO::PARAM_INT);
            $deleteStmt->execute();

            if (!empty($planIds)) {
                $insertStmt = $db->prepare('INSERT INTO socio_planes (socio_id, periodo_id) VALUES (:socio_id, :periodo_id)');
                foreach ($planIds as $planId) {
                    $insertStmt->bindValue(':socio_id', $socioId, PDO::PARAM_INT);
                    $insertStmt->bindValue(':periodo_id', $planId, PDO::PARAM_INT);
                    $insertStmt->execute();
                }
            }
            $db->commit();
        } catch (\Throwable $exception) {
            $db->rollBack();
            throw $exception;
        }
    }

    private static function normalizeIds($raw): array
    {
        if (!is_array($raw)) {
            $raw = $raw === null || $raw === '' ? [] : [$raw];
        }

        $ids = array_map(static fn($item): int => (int) $item, $raw);
        $ids = array_values(array_unique(array_filter($ids, static fn(int $id): bool => $id > 0)));

        return $ids;
    }

    public static function tableExists(string $table): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name'
        );
        $stmt->bindValue(':table_name', $table);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function columnExists(string $table, string $column): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table_name AND column_name = :column_name'
        );
        $stmt->bindValue(':table_name', $table);
        $stmt->bindValue(':column_name', $column);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function nextSocioNumber(): string
    {
        $db = Database::connection();
        $stmt = $db->query("SELECT COALESCE(MAX(CAST(numero_socio AS UNSIGNED)), 0) FROM socios");
        $next = ((int) $stmt->fetchColumn()) + 1;

        return str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public static function nextEgresoDocumentNumber(): string
    {
        $prefix = 'EGR-' . date('Ymd') . '-';
        $stmt = Database::connection()->prepare('SELECT numero_documento FROM egresos WHERE numero_documento LIKE :prefix ORDER BY id DESC LIMIT 1');
        $stmt->bindValue(':prefix', $prefix . '%');
        $stmt->execute();
        $lastNumber = (string) ($stmt->fetchColumn() ?: '');

        $sequence = 1;
        if ($lastNumber !== '' && preg_match('/(\d+)$/', $lastNumber, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function delete(string $table, string $primaryKey, int $id, bool $softDelete): void
    {
        if ($softDelete) {
            $stmt = Database::connection()->prepare('UPDATE `' . $table . '` SET deleted_at = NOW() WHERE `' . $primaryKey . '` = :id');
        } else {
            $stmt = Database::connection()->prepare('DELETE FROM `' . $table . '` WHERE `' . $primaryKey . '` = :id');
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if (in_array($table, ['pagos', 'aportes', 'egresos'], true)) {
            self::deleteTreasuryMovement($table, $id);
            self::recalculateTreasuryBalance();
        }
        if ($table === 'movimientos_tesoreria') {
            self::recalculateTreasuryBalance();
        }
    }

    private static function syncTreasuryMovementFromSource(string $sourceTable, int $sourceId): void
    {
        if ($sourceId <= 0 || !self::tableExists('movimientos_tesoreria')) {
            return;
        }

        $snapshot = self::treasurySnapshotForSource($sourceTable, $sourceId);
        if ($snapshot === null) {
            self::deleteTreasuryMovement($sourceTable, $sourceId);
            self::recalculateTreasuryBalance();
            return;
        }

        $db = Database::connection();
        $findStmt = $db->prepare(
            'SELECT id FROM movimientos_tesoreria
             WHERE origen_modulo = :origen_modulo AND referencia_id = :referencia_id
             ORDER BY id ASC'
        );
        $findStmt->bindValue(':origen_modulo', $snapshot['origen_modulo']);
        $findStmt->bindValue(':referencia_id', $sourceId, PDO::PARAM_INT);
        $findStmt->execute();
        $existingIds = array_map(static fn(array $row): int => (int) ($row['id'] ?? 0), $findStmt->fetchAll());

        if (empty($existingIds)) {
            $insertStmt = $db->prepare(
                'INSERT INTO movimientos_tesoreria
                (cuenta_bancaria_id, fecha, tipo_movimiento, origen_modulo, referencia_id, descripcion, ingreso, egreso, saldo_referencial)
                VALUES (:cuenta_bancaria_id, :fecha, :tipo_movimiento, :origen_modulo, :referencia_id, :descripcion, :ingreso, :egreso, 0)'
            );
            self::bindTreasurySnapshot($insertStmt, $snapshot, $sourceId);
            $insertStmt->execute();
        } else {
            $keeperId = (int) $existingIds[0];
            $updateStmt = $db->prepare(
                'UPDATE movimientos_tesoreria
                 SET cuenta_bancaria_id = :cuenta_bancaria_id,
                     fecha = :fecha,
                     tipo_movimiento = :tipo_movimiento,
                     descripcion = :descripcion,
                     ingreso = :ingreso,
                     egreso = :egreso
                 WHERE id = :id'
            );
            self::bindTreasurySnapshot($updateStmt, $snapshot, $sourceId);
            $updateStmt->bindValue(':id', $keeperId, PDO::PARAM_INT);
            $updateStmt->execute();

            if (count($existingIds) > 1) {
                $idsToDelete = array_slice($existingIds, 1);
                $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
                $deleteDuplicatesStmt = $db->prepare('DELETE FROM movimientos_tesoreria WHERE id IN (' . $placeholders . ')');
                foreach ($idsToDelete as $index => $duplicateId) {
                    $deleteDuplicatesStmt->bindValue($index + 1, $duplicateId, PDO::PARAM_INT);
                }
                $deleteDuplicatesStmt->execute();
            }
        }

        self::recalculateTreasuryBalance();
    }

    private static function bindTreasurySnapshot(\PDOStatement $stmt, array $snapshot, int $sourceId): void
    {
        $stmt->bindValue(':cuenta_bancaria_id', $snapshot['cuenta_bancaria_id'], $snapshot['cuenta_bancaria_id'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':fecha', $snapshot['fecha']);
        $stmt->bindValue(':tipo_movimiento', $snapshot['tipo_movimiento']);
        $stmt->bindValue(':origen_modulo', $snapshot['origen_modulo']);
        $stmt->bindValue(':referencia_id', $sourceId, PDO::PARAM_INT);
        $stmt->bindValue(':descripcion', $snapshot['descripcion']);
        $stmt->bindValue(':ingreso', $snapshot['ingreso']);
        $stmt->bindValue(':egreso', $snapshot['egreso']);
    }

    private static function treasurySnapshotForSource(string $sourceTable, int $sourceId): ?array
    {
        $db = Database::connection();

        if ($sourceTable === 'pagos') {
            $stmt = $db->prepare(
                'SELECT p.id, p.socio_id, p.fecha_pago, p.monto_total, p.cuenta_bancaria_id, p.numero_comprobante, p.referencia_externa, p.observacion, p.estado_pago, p.deleted_at,
                        COALESCE(s.nombre_completo, CONCAT(COALESCE(s.nombres, \'\'), \' \', COALESCE(s.apellidos, \'\'))) AS socio_nombre
                 FROM pagos p
                 LEFT JOIN socios s ON s.id = p.socio_id
                 WHERE p.id = :id
                 LIMIT 1'
            );
            $stmt->bindValue(':id', $sourceId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch() ?: null;
            if ($row === null) {
                return null;
            }

            if ((string) ($row['estado_pago'] ?? '') !== 'aplicado' || !empty($row['deleted_at'])) {
                return null;
            }

            $descripcion = 'Pago socio';
            $socioNombre = trim((string) ($row['socio_nombre'] ?? ''));
            if ($socioNombre !== '') {
                $descripcion .= ': ' . $socioNombre;
            }
            $comprobante = trim((string) ($row['numero_comprobante'] ?? ''));
            if ($comprobante !== '') {
                $descripcion .= ' · Comprobante ' . $comprobante;
            }

            return [
                'cuenta_bancaria_id' => isset($row['cuenta_bancaria_id']) ? (int) $row['cuenta_bancaria_id'] : null,
                'fecha' => (string) ($row['fecha_pago'] ?? date('Y-m-d')),
                'tipo_movimiento' => 'ingreso',
                'origen_modulo' => 'pagos',
                'descripcion' => $descripcion,
                'ingreso' => (float) ($row['monto_total'] ?? 0),
                'egreso' => 0.0,
            ];
        }

        if ($sourceTable === 'aportes') {
            $stmt = $db->prepare(
                'SELECT a.id, a.nombre_aportante, a.fecha_aporte, a.monto, a.estado, a.comprobante, a.descripcion,
                        a.socio_id, COALESCE(s.nombre_completo, CONCAT(COALESCE(s.nombres, \'\'), \' \', COALESCE(s.apellidos, \'\'))) AS socio_nombre
                 FROM aportes a
                 LEFT JOIN socios s ON s.id = a.socio_id
                 WHERE a.id = :id
                 LIMIT 1'
            );
            $stmt->bindValue(':id', $sourceId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch() ?: null;
            if ($row === null) {
                return null;
            }

            if ((string) ($row['estado'] ?? '') !== 'aplicado') {
                return null;
            }

            $aportante = trim((string) ($row['nombre_aportante'] ?? ''));
            if ($aportante === '') {
                $aportante = trim((string) ($row['socio_nombre'] ?? ''));
            }
            $descripcion = 'Aporte';
            if ($aportante !== '') {
                $descripcion .= ': ' . $aportante;
            }
            $comprobante = trim((string) ($row['comprobante'] ?? ''));
            if ($comprobante !== '') {
                $descripcion .= ' · Comprobante ' . $comprobante;
            }

            return [
                'cuenta_bancaria_id' => null,
                'fecha' => (string) ($row['fecha_aporte'] ?? date('Y-m-d')),
                'tipo_movimiento' => 'ingreso',
                'origen_modulo' => 'aportes',
                'descripcion' => $descripcion,
                'ingreso' => (float) ($row['monto'] ?? 0),
                'egreso' => 0.0,
            ];
        }

        if ($sourceTable === 'egresos') {
            $stmt = $db->prepare(
                'SELECT e.id, e.fecha, e.descripcion, e.monto, e.numero_documento, e.proveedor_destinatario, e.estado, e.deleted_at, e.cuenta_bancaria_id
                 FROM egresos e
                 WHERE e.id = :id
                 LIMIT 1'
            );
            $stmt->bindValue(':id', $sourceId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch() ?: null;
            if ($row === null) {
                return null;
            }

            if ((string) ($row['estado'] ?? '') !== 'aplicado' || !empty($row['deleted_at'])) {
                return null;
            }

            $descripcion = 'Egreso';
            $motivo = trim((string) ($row['descripcion'] ?? ''));
            if ($motivo !== '') {
                $descripcion .= ': ' . $motivo;
            }
            $destinatario = trim((string) ($row['proveedor_destinatario'] ?? ''));
            if ($destinatario !== '') {
                $descripcion .= ' · Destinatario ' . $destinatario;
            }

            return [
                'cuenta_bancaria_id' => isset($row['cuenta_bancaria_id']) ? (int) $row['cuenta_bancaria_id'] : null,
                'fecha' => (string) ($row['fecha'] ?? date('Y-m-d')),
                'tipo_movimiento' => 'egreso',
                'origen_modulo' => 'egresos',
                'descripcion' => $descripcion,
                'ingreso' => 0.0,
                'egreso' => (float) ($row['monto'] ?? 0),
            ];
        }

        return null;
    }

    private static function deleteTreasuryMovement(string $sourceTable, int $sourceId): void
    {
        if ($sourceId <= 0 || !self::tableExists('movimientos_tesoreria')) {
            return;
        }

        $stmt = Database::connection()->prepare(
            'DELETE FROM movimientos_tesoreria
             WHERE origen_modulo = :origen_modulo
               AND referencia_id = :referencia_id'
        );
        $stmt->bindValue(':origen_modulo', $sourceTable);
        $stmt->bindValue(':referencia_id', $sourceId, PDO::PARAM_INT);
        $stmt->execute();
    }

    private static function recalculateTreasuryBalance(): void
    {
        if (!self::tableExists('movimientos_tesoreria')) {
            return;
        }

        $db = Database::connection();
        $stmt = $db->query(
            'SELECT id, ingreso, egreso
             FROM movimientos_tesoreria
             ORDER BY fecha ASC, id ASC'
        );
        $rows = $stmt->fetchAll();

        $runningBalance = 0.0;
        $updateStmt = $db->prepare('UPDATE movimientos_tesoreria SET saldo_referencial = :saldo WHERE id = :id');
        foreach ($rows as $row) {
            $runningBalance += (float) ($row['ingreso'] ?? 0) - (float) ($row['egreso'] ?? 0);
            $updateStmt->bindValue(':saldo', $runningBalance);
            $updateStmt->bindValue(':id', (int) ($row['id'] ?? 0), PDO::PARAM_INT);
            $updateStmt->execute();
        }
    }

    private static function fetchStatusCounts(PDO $db, string $table, array $columnsMeta, string $query, ?string $from, ?string $to, array $extraConditions = []): array
    {
        $statusField = $columnsMeta['status_field'];
        if ($statusField === null) {
            return [];
        }

        [$whereSql, $params] = self::buildWhereSql($columnsMeta, $query, null, $from, $to, $extraConditions);
        $sql = 'SELECT `' . $statusField . '` AS estado, COUNT(*) AS total FROM `' . $table . '`' . $whereSql . ' GROUP BY `' . $statusField . '` ORDER BY total DESC';
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $rawState = (string) ($row['estado'] ?? '');
            $state = $rawState;
            if ($statusField === 'activo') {
                $state = $rawState === '1' ? 'activo' : 'inactivo';
            } elseif ($statusField === 'cerrado') {
                $state = $rawState === '1' ? 'cerrada' : 'abierta';
            }
            $counts[$state] = (int) $row['total'];
        }

        return $counts;
    }

    private static function foreignKeysForTable(PDO $db, string $table): array
    {
        if (isset(self::$foreignKeyCache[$table])) {
            return self::$foreignKeyCache[$table];
        }

        $sql = 'SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table
              AND REFERENCED_TABLE_NAME IS NOT NULL';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table', $table);
        $stmt->execute();

        $map = [];
        foreach ($stmt->fetchAll() as $fk) {
            $column = (string) ($fk['COLUMN_NAME'] ?? '');
            $refTable = (string) ($fk['REFERENCED_TABLE_NAME'] ?? '');
            $refPrimary = (string) ($fk['REFERENCED_COLUMN_NAME'] ?? 'id');
            if ($column === '' || $refTable === '') {
                continue;
            }

            $map[$column] = [
                'table' => $refTable,
                'label_column' => self::preferredLabelColumn($db, $refTable),
                'primary' => $refPrimary,
            ];
        }

        self::$foreignKeyCache[$table] = $map;

        return $map;
    }

    private static function preferredLabelColumn(PDO $db, string $table): string
    {
        $stmt = $db->query('SHOW COLUMNS FROM `' . $table . '`');
        $columns = array_map(static fn(array $column): string => (string) ($column['Field'] ?? ''), $stmt->fetchAll());
        $priority = [
            'nombre_completo',
            'nombre',
            'nombre_periodo',
            'numero_socio',
            'numero_rendicion',
            'descripcion',
            'correo',
            'usuario',
        ];

        foreach ($priority as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return 'id';
    }

    private static function foreignLabel(string $table, string $labelColumn, int $id, string $primaryColumn = 'id'): ?string
    {
        $cacheKey = $table . '.' . $labelColumn . '.' . $primaryColumn;
        if (!isset(self::$foreignLabelCache[$cacheKey])) {
            self::$foreignLabelCache[$cacheKey] = [];
        }

        if (array_key_exists($id, self::$foreignLabelCache[$cacheKey])) {
            return self::$foreignLabelCache[$cacheKey][$id];
        }

        $sql = 'SELECT `' . $labelColumn . '` FROM `' . $table . '` WHERE `' . $primaryColumn . '` = :id LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $value = $stmt->fetchColumn();

        self::$foreignLabelCache[$cacheKey][$id] = $value !== false ? (string) $value : null;

        return self::$foreignLabelCache[$cacheKey][$id];
    }

    private static function buildWhereSql(array $columnsMeta, string $query, ?string $status, ?string $from, ?string $to, array $extraConditions = []): array
    {
        $searchColumns = $columnsMeta['searchable'];
        $conditions = [];
        $params = [];

        if ($columnsMeta['has_deleted_at']) {
            $conditions[] = '`deleted_at` IS NULL';
        }

        if ($query !== '' && !empty($searchColumns)) {
            $parts = [];
            foreach ($searchColumns as $idx => $column) {
                $param = ':q' . $idx;
                $parts[] = "`{$column}` LIKE {$param}";
                $params[$param] = '%' . $query . '%';
            }
            $conditions[] = '(' . implode(' OR ', $parts) . ')';
        }

        if ($status !== null && $status !== '' && $columnsMeta['status_field'] !== null) {
            $statusField = $columnsMeta['status_field'];
            if (in_array($statusField, ['activo', 'cerrado'], true)) {
                $params[':status'] = in_array($status, ['activo', 'cerrada'], true) ? 1 : 0;
            } else {
                $params[':status'] = $status;
            }
            $conditions[] = "`{$statusField}` = :status";
        }

        if ($columnsMeta['date_field'] !== null) {
            $dateField = $columnsMeta['date_field'];
            if ($from !== null && $from !== '') {
                $conditions[] = "DATE(`{$dateField}`) >= :from";
                $params[':from'] = $from;
            }
            if ($to !== null && $to !== '') {
                $conditions[] = "DATE(`{$dateField}`) <= :to";
                $params[':to'] = $to;
            }
        }

        foreach ($extraConditions as $index => $extraCondition) {
            if (!is_array($extraCondition)) {
                continue;
            }

            $sql = trim((string) ($extraCondition['sql'] ?? ''));
            if ($sql === '') {
                continue;
            }

            $conditions[] = '(' . $sql . ')';
            $conditionParams = $extraCondition['params'] ?? [];
            if (!is_array($conditionParams)) {
                continue;
            }

            foreach ($conditionParams as $rawName => $value) {
                $name = (string) $rawName;
                if ($name === '') {
                    continue;
                }

                $paramName = str_starts_with($name, ':') ? $name : ':' . $name;
                $params[$paramName] = $value;
            }
        }

        $whereSql = empty($conditions) ? '' : ' WHERE ' . implode(' AND ', $conditions);

        return [$whereSql, $params];
    }
}
