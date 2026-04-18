<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public function run(): void
    {
        $config = require __DIR__ . '/../config/app.php';
        date_default_timezone_set($config['timezone']);

        Session::start();

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        (new Router())->dispatch($method, $uri);
    }
}
