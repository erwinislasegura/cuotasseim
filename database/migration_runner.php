<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db = getenv('DB_DATABASE') ?: 'gestion_cuotas';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

$pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  version VARCHAR(20) NOT NULL UNIQUE,
  nombre_archivo VARCHAR(255) NOT NULL,
  fecha_ejecucion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ejecutado_por VARCHAR(100) NULL,
  observacion VARCHAR(255) NULL
)");

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

foreach ($files as $file) {
    $version = basename($file, '.sql');
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM migrations WHERE version = :version');
    $stmt->execute(['version' => $version]);

    if ((int)$stmt->fetchColumn() > 0) {
        continue;
    }

    $sql = file_get_contents($file);
    $pdo->exec($sql);

    $insert = $pdo->prepare('INSERT INTO migrations (version, nombre_archivo, ejecutado_por, observacion) VALUES (:version, :file, :by, :obs)');
    $insert->execute([
        'version' => $version,
        'file' => basename($file),
        'by' => get_current_user() ?: 'cli',
        'obs' => 'Migración aplicada automáticamente',
    ]);

    echo "Aplicada: {$version}" . PHP_EOL;
}
