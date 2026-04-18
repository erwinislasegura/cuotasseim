<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    $configuredBase = trim((string) (getenv('APP_URL') ?: ''));

    if ($configuredBase !== '') {
        $base = rtrim($configuredBase, '/');
    } else {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) == 443);
        $scheme = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $base = $scheme . '://' . $host . ($scriptDir === '' || $scriptDir === '.' ? '' : $scriptDir);
    }

    return $base . '/' . ltrim($path, '/');
}
