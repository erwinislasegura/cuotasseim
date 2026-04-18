<?php

declare(strict_types=1);

$composerAutoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $relativePath = str_replace('\\', '/', $relativeClass);
        $segments = explode('/', $relativePath);

        if (!empty($segments[0])) {
            $segments[0] = strtolower($segments[0]);
        }

        $file = __DIR__ . '/../app/' . implode('/', $segments) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });

    foreach ([
        __DIR__ . '/../app/helpers/url_helper.php',
        __DIR__ . '/../app/helpers/money_helper.php',
        __DIR__ . '/../app/helpers/date_helper.php',
        __DIR__ . '/../app/helpers/auth_helper.php',
        __DIR__ . '/../app/helpers/status_helper.php',
        __DIR__ . '/../app/helpers/audit_helper.php',
        __DIR__ . '/../app/helpers/form_helper.php',
    ] as $helperFile) {
        if (file_exists($helperFile)) {
            require_once $helperFile;
        }
    }
}

$app = new App\Core\App();
$app->run();
