<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $scriptDir = ($scriptDir === '' || $scriptDir === '.') ? '' : $scriptDir;

    $configuredBase = trim((string) (getenv('APP_URL') ?: ''));

    if ($configuredBase !== '') {
        $parsed = parse_url($configuredBase);
        $configuredPath = rtrim((string) ($parsed['path'] ?? ''), '/');

        if ($scriptDir !== '' && ($configuredPath === '' || $configuredPath === '/')) {
            $base = rtrim($configuredBase, '/') . $scriptDir;
        } else {
            $base = rtrim($configuredBase, '/');
        }
    } else {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) == 443);
        $scheme = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $scheme . '://' . $host . $scriptDir;
    }

    return $base . '/' . ltrim($path, '/');
}
