<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

class ModuleCatalog
{
    public static function config(string $key): ?array
    {
        $modules = [
            'socios' => ['title' => 'Socios', 'description' => 'Administración del padrón de socios.', 'table' => 'socios', 'route' => 'socios'],
            'tipos_socio' => ['title' => 'Tipos de socio', 'description' => 'Catálogo de categorías de socios.', 'table' => 'tipos_socio', 'route' => 'tipos-socio'],
            'periodos' => ['title' => 'Periodos', 'description' => 'Gestión de periodos de cobranza.', 'table' => 'periodos', 'route' => 'periodos'],
            'conceptos_cobro' => ['title' => 'Conceptos de cobro', 'description' => 'Catálogo de conceptos para cuotas.', 'table' => 'conceptos_cobro', 'route' => 'conceptos-cobro'],
            'cuotas' => ['title' => 'Cuotas', 'description' => 'Control de cuotas mensuales por socio y periodo.', 'table' => 'cuotas', 'route' => 'cuotas'],
            'medios_pago' => ['title' => 'Medios de pago', 'description' => 'Catálogo de medios de pago aceptados.', 'table' => 'medios_pago', 'route' => 'medios-pago'],
            'pagos' => ['title' => 'Pagos', 'description' => 'Registro y control de pagos.', 'table' => 'pagos', 'route' => 'pagos'],
            'tipos_aporte' => ['title' => 'Tipos de aporte', 'description' => 'Tipos de aportes extraordinarios.', 'table' => 'tipos_aporte', 'route' => 'tipos-aporte'],
            'aportes' => ['title' => 'Aportes', 'description' => 'Registro de aportes extraordinarios.', 'table' => 'aportes', 'route' => 'aportes'],
            'tipos_egreso' => ['title' => 'Tipos de egreso', 'description' => 'Catálogo de tipos de egreso.', 'table' => 'tipos_egreso', 'route' => 'tipos-egreso'],
            'egresos' => ['title' => 'Egresos', 'description' => 'Registro de gastos y salidas.', 'table' => 'egresos', 'route' => 'egresos'],
            'rendiciones' => ['title' => 'Rendiciones', 'description' => 'Agrupación y control de rendiciones.', 'table' => 'rendiciones', 'route' => 'rendiciones'],
            'tesoreria' => ['title' => 'Movimientos de tesorería', 'description' => 'Visualización de movimientos financieros.', 'table' => 'movimientos_tesoreria', 'route' => 'tesoreria'],
            'roles' => ['title' => 'Roles', 'description' => 'Administración de roles del sistema.', 'table' => 'roles', 'route' => 'roles'],
            'usuarios' => ['title' => 'Usuarios', 'description' => 'Administración de usuarios del sistema.', 'table' => 'usuarios', 'route' => 'usuarios'],
            'configuracion' => ['title' => 'Configuración general', 'description' => 'Parámetros globales de la organización.', 'table' => 'configuracion', 'route' => 'configuracion'],
            'auditoria' => ['title' => 'Auditoría', 'description' => 'Trazabilidad de acciones del sistema.', 'table' => 'auditoria', 'route' => 'auditoria'],
            'reportes' => ['title' => 'Reportes', 'description' => 'Vista consolidada de datos para reportería.', 'table' => 'cuotas', 'route' => 'reportes'],
        ];

        return $modules[$key] ?? null;
    }

    public static function resolveColumns(PDO $db, string $table): array
    {
        $stmt = $db->query('DESCRIBE `' . $table . '`');
        $all = array_map(static fn(array $row) => (string) $row['Field'], $stmt->fetchAll());

        $excluded = ['deleted_at', 'password'];
        $columns = array_values(array_filter($all, static fn(string $column) => !in_array($column, $excluded, true)));

        return [
            'all' => $columns,
            'visible' => array_slice($columns, 0, 8),
            'searchable' => array_slice(array_values(array_filter($columns, static fn(string $column) => str_contains($column, 'nombre') || str_contains($column, 'numero') || str_contains($column, 'descripcion') || str_contains($column, 'correo') || str_contains($column, 'rut'))), 0, 3),
            'form' => array_slice(array_values(array_filter($columns, static fn(string $column) => !in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'], true))), 0, 6),
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

    public static function fetchData(array $config, string $query, int $page, int $perPage): array
    {
        $db = Database::connection();
        $columnsMeta = self::resolveColumns($db, $config['table']);
        $searchColumns = $columnsMeta['searchable'];

        $whereSql = '';
        $params = [];

        if ($query !== '' && !empty($searchColumns)) {
            $parts = [];
            foreach ($searchColumns as $idx => $column) {
                $param = ':q' . $idx;
                $parts[] = "`{$column}` LIKE {$param}";
                $params[$param] = '%' . $query . '%';
            }
            $whereSql = ' WHERE ' . implode(' OR ', $parts);
        }

        $countStmt = $db->prepare('SELECT COUNT(*) FROM `' . $config['table'] . '`' . $whereSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $sql = 'SELECT * FROM `' . $config['table'] . '`' . $whereSql . ' ORDER BY 1 DESC LIMIT :limit OFFSET :offset';
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
            'searchable' => $searchColumns,
            'query' => $query,
            'page' => $page,
            'perPage' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
