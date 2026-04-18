<?php

declare(strict_types=1);

$config = require __DIR__ . '/../app/config/database.php';

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    $config['host'],
    $config['port'],
    $config['database'],
    $config['charset']
);

try {
    $pdo = new PDO($dsn, (string) $config['username'], (string) $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $tables = [
        'rendicion_detalle',
        'pago_detalle',
        'movimientos_tesoreria',
        'rendiciones',
        'egresos',
        'aportes',
        'pagos',
        'cuotas',
        'socio_planes',
        'auditoria',
        'socios',
    ];

    $pdo->beginTransaction();
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) {
        $pdo->exec('DELETE FROM `' . $table . '`');
        $pdo->exec('ALTER TABLE `' . $table . '` AUTO_INCREMENT = 1');
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    $pdo->commit();

    echo "✅ Limpieza completada.\n";
    echo "Tablas limpiadas: " . implode(', ', $tables) . "\n";
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, "❌ Error al limpiar la base de datos: " . $exception->getMessage() . "\n");
    exit(1);
}
