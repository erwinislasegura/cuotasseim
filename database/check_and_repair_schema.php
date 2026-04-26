<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_DATABASE') ?: 'gestion_cuotas';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );
} catch (Throwable $exception) {
    fwrite(STDERR, 'No fue posible conectar a MySQL: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

$schemaFile = __DIR__ . '/schema.sql';
if (!is_file($schemaFile)) {
    fwrite(STDERR, "No existe database/schema.sql" . PHP_EOL);
    exit(1);
}

$sqlContent = (string) file_get_contents($schemaFile);
$statements = array_filter(array_map('trim', explode(';', $sqlContent)));

$created = 0;
$failed = 0;

foreach ($statements as $statement) {
    if (!preg_match('/^CREATE TABLE\s+([`"]?)([a-zA-Z0-9_]+)\1/i', ltrim($statement), $matches)) {
        continue;
    }

    $tableName = (string) ($matches[2] ?? '');
    if ($tableName === '') {
        continue;
    }

    $check = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name'
    );
    $check->execute(['table_name' => $tableName]);
    $exists = (int) $check->fetchColumn() > 0;

    if ($exists) {
        echo "OK: {$tableName}" . PHP_EOL;
        continue;
    }

    $safeStatement = preg_replace('/^CREATE TABLE\s+/i', 'CREATE TABLE IF NOT EXISTS ', $statement);
    if (!is_string($safeStatement) || trim($safeStatement) === '') {
        continue;
    }

    try {
        $pdo->exec($safeStatement);
        $created++;
        echo "CREADA: {$tableName}" . PHP_EOL;
    } catch (Throwable $exception) {
        $failed++;
        echo "ERROR: {$tableName} -> " . $exception->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "Resumen: creadas={$created}, errores={$failed}" . PHP_EOL;
exit($failed > 0 ? 2 : 0);
