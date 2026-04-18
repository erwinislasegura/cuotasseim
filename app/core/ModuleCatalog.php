<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

class ModuleCatalog
{
    public static function config(string $key): ?array
    {
        $modules = [
            'socios' => ['title' => 'Socios', 'description' => 'Administración del padrón de socios con estado, deuda y seguimiento.', 'table' => 'socios', 'route' => 'socios'],
            'tipos_socio' => ['title' => 'Tipos de socio', 'description' => 'Catálogo de categorías y segmentación de socios.', 'table' => 'tipos_socio', 'route' => 'tipos-socio'],
            'periodos' => ['title' => 'Periodos', 'description' => 'Gestión de periodos de cobranza y su ciclo operativo.', 'table' => 'periodos', 'route' => 'periodos'],
            'conceptos_cobro' => ['title' => 'Conceptos de cobro', 'description' => 'Definición de conceptos recurrentes y extraordinarios.', 'table' => 'conceptos_cobro', 'route' => 'conceptos-cobro'],
            'cuotas' => ['title' => 'Cuotas', 'description' => 'Control de cuotas con foco en morosidad y cobranzas.', 'table' => 'cuotas', 'route' => 'cuotas'],
            'medios_pago' => ['title' => 'Medios de pago', 'description' => 'Catálogo de medios habilitados para recaudación.', 'table' => 'medios_pago', 'route' => 'medios-pago'],
            'pagos' => ['title' => 'Pagos', 'description' => 'Registro, imputación y trazabilidad de pagos.', 'table' => 'pagos', 'route' => 'pagos'],
            'tipos_aporte' => ['title' => 'Tipos de aporte', 'description' => 'Clasificación de aportes extraordinarios.', 'table' => 'tipos_aporte', 'route' => 'tipos-aporte'],
            'aportes' => ['title' => 'Aportes', 'description' => 'Seguimiento de aportes extraordinarios por origen y estado.', 'table' => 'aportes', 'route' => 'aportes'],
            'tipos_egreso' => ['title' => 'Tipos de egreso', 'description' => 'Clasificación de egresos para tesorería y rendiciones.', 'table' => 'tipos_egreso', 'route' => 'tipos-egreso'],
            'egresos' => ['title' => 'Egresos', 'description' => 'Control operativo de egresos, comprobantes y estado.', 'table' => 'egresos', 'route' => 'egresos'],
            'rendiciones' => ['title' => 'Rendiciones', 'description' => 'Consolidación de egresos para rendición y cierre.', 'table' => 'rendiciones', 'route' => 'rendiciones'],
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

    public static function fetchData(array $config, string $query, int $page, int $perPage, ?string $status = null, ?string $from = null, ?string $to = null): array
    {
        $db = Database::connection();
        $columnsMeta = self::resolveColumns($db, $config['table']);
        $searchColumns = $columnsMeta['searchable'];

        [$whereSql, $params] = self::buildWhereSql($columnsMeta, $query, $status, $from, $to);

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

        $statusCounts = self::fetchStatusCounts($db, $config['table'], $columnsMeta, $query, $from, $to);

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

    public static function save(string $table, string $primaryKey, array $fields, array $payload, ?int $id = null): void
    {
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

        $db = Database::connection();

        if ($id !== null) {
            $sets = [];
            foreach ($fields as $field) {
                $sets[] = "`{$field}` = :{$field}";
            }
            $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $sets) . ' WHERE `' . $primaryKey . '` = :pk';
            $stmt = $db->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(':' . $field, $value);
            }
            $stmt->bindValue(':pk', $id, PDO::PARAM_INT);
            $stmt->execute();
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
    }

    public static function nextSocioNumber(): string
    {
        $db = Database::connection();
        $stmt = $db->query("SELECT COALESCE(MAX(CAST(numero_socio AS UNSIGNED)), 0) FROM socios");
        $next = ((int) $stmt->fetchColumn()) + 1;

        return str_pad((string) $next, 6, '0', STR_PAD_LEFT);
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
    }

    private static function fetchStatusCounts(PDO $db, string $table, array $columnsMeta, string $query, ?string $from, ?string $to): array
    {
        $statusField = $columnsMeta['status_field'];
        if ($statusField === null) {
            return [];
        }

        [$whereSql, $params] = self::buildWhereSql($columnsMeta, $query, null, $from, $to);
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

    private static function buildWhereSql(array $columnsMeta, string $query, ?string $status, ?string $from, ?string $to): array
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

        $whereSql = empty($conditions) ? '' : ' WHERE ' . implode(' AND ', $conditions);

        return [$whereSql, $params];
    }
}
