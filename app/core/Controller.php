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

        try {
            $data = ModuleCatalog::fetchData($config, $query, $page, $perPage, $status !== '' ? $status : null, $from !== '' ? $from : null, $to !== '' ? $to : null);
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
            $formMeta = [];
            $formFields = $data['columns']['form'];
            $columnLabels = [];
            $visibleColumns = $data['columns']['visible'];

            if ($editId !== null && $editId > 0) {
                $currentRecord = ModuleCatalog::findById($config['table'], $primaryKey, $editId);
            }

            if ($viewId !== null && $viewId > 0) {
                $viewRecord = ModuleCatalog::findById($config['table'], $primaryKey, $viewId);
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

                if ($currentRecord === null) {
                    $currentRecord = ['numero_socio' => ModuleCatalog::nextSocioNumber()];
                }
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
                'isReadOnly' => $isReadOnly,
                'flashSuccess' => $flashSuccess,
                'flashError' => $flashError,
                'formMeta' => $formMeta,
                'columnLabels' => $columnLabels,
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
                'isReadOnly' => true,
                'error' => 'No fue posible cargar el módulo. Verifica la conexión y migraciones de base de datos.',
                'formMeta' => [],
                'columnLabels' => [],
            ]);
        }
    }
}
