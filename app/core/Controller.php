<?php

declare(strict_types=1);

namespace App\Core;

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
}
