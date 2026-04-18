<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    public function dispatch(string $method, string $uri): void
    {
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
}
