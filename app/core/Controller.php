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

        $query = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        try {
            $data = ModuleCatalog::fetchData($config, $query, $page, $perPage);

            if (($_GET['export'] ?? '') === 'excel') {
                ModuleCatalog::exportCsv($config['route'] . '_' . date('Ymd_His') . '.csv', $data['columns']['all'], $data['rows']);
                return;
            }

            $this->view('modules/index', [
                'title' => $config['title'],
                'description' => $config['description'],
                'route' => $config['route'],
                'query' => $query,
                'rows' => $data['rows'],
                'columns' => $data['columns']['visible'],
                'formFields' => $data['columns']['form'],
                'total' => $data['total'],
                'page' => $data['page'],
                'pages' => $data['pages'],
            ]);
        } catch (Throwable $exception) {
            $this->view('modules/index', [
                'title' => $config['title'],
                'description' => $config['description'],
                'route' => $config['route'],
                'query' => $query,
                'rows' => [],
                'columns' => [],
                'formFields' => [],
                'total' => 0,
                'page' => 1,
                'pages' => 1,
                'error' => 'No fue posible cargar el módulo. Verifica la conexión y migraciones de base de datos.',
            ]);
        }
    }
}
