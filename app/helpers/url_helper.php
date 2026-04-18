<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    $base = rtrim(getenv('APP_URL') ?: '', '/');
    return $base . '/' . ltrim($path, '/');
}
