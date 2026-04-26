# Instalación
1. Copiar `.env.example` a `.env`.
2. Configurar credenciales MySQL.
3. Ejecutar `composer install`.
4. Crear base con `database/schema.sql`.
5. Cargar datos con `database/seed.sql`.
6. Aplicar migraciones con `php database/migration_runner.php`.
7. Verificar/reparar tablas faltantes con `php database/check_and_repair_schema.php` (opcional, recomendado si un módulo no carga).
8. Publicar `public/` como DocumentRoot en Apache.
