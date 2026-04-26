<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthMiddleware;

class Router
{
    /** @var array<int,string> */
    private array $publicRoutes = [
        '/',
        '/login',
        '/pago-flow',
        '/pago-flow/crear',
        '/pago-flow/retorno',
        '/pago-flow/rechazado',
    ];

    public function dispatch(string $method, string $uri): void
    {
        if ($this->requiresAuth($uri) && !AuthMiddleware::handle()) {
            header('Location: ' . url('login'));
            exit;
        }

        $routes = require __DIR__ . '/../config/routes.php';

        foreach ($routes as [$verb, $path, $handler]) {
            if ($verb === $method && $path === $uri) {
                [$controller, $action] = explode('@', $handler);
                $class = 'App\\Controllers\\' . $controller;
                $instance = new $class();
                $instance->{$action}();
                return;
            }
        }

        http_response_code(404);
        echo 'Ruta no encontrada';
    }

    private function requiresAuth(string $uri): bool
    {
        return !in_array($uri, $this->publicRoutes, true);
    }
}
