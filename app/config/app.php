<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Sistema de Gestión de Cuotas',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => getenv('APP_TIMEZONE') ?: 'America/Santiago',
    'session_lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 120),
    'csrf_token_name' => getenv('CSRF_TOKEN_NAME') ?: '_token',
];
