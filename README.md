# Sistema de Gestión de Cuotas (MVC PHP + MySQL)

Proyecto base profesional para administrar socios, cuotas, pagos, aportes, egresos, tesorería, reportes y auditoría con trazabilidad SQL.

## Stack
- PHP 8.1+
- MySQL 8 / MariaDB
- PDO + prepared statements
- Bootstrap 5
- MVC propio + Front Controller + rutas limpias

## Estructura
- `app/`: núcleo MVC (core, controladores, modelos, vistas, helpers, middleware).
- `public/`: punto de entrada web y assets.
- `database/`: schema, seed, migraciones, patches y runner.
- `docs/`: instalación, módulos, base de datos, flujo y política de cambios.

## Instalación rápida
```bash
cp .env.example .env
composer install
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
php database/migration_runner.php
```

## Limpieza de base de datos (datos operativos)
Para limpiar datos transaccionales y dejar solo catálogos/configuración base:

```bash
php database/cleanup.php
```

## Accesos seed
- Usuario: `admin` / Password de ejemplo: `Admin123*`
- Usuario: `tesorero` / Password de ejemplo: `Admin123*`
- Usuario: `consulta` / Password de ejemplo: `Admin123*`

## Reglas críticas incorporadas
- Toda mejora futura debe incluir migración SQL + patch incremental + documentación.
- No borrar físicamente datos críticos (soft delete).
- Auditoría para operaciones sensibles.
- Integración contable: pagos/aportes/egresos impactan tesorería.

## Evitar errores por binarios
- Este repositorio evita adjuntar binarios en commits de código fuente.
- Mantener imágenes y evidencias fuera de la lógica principal o en formatos comprimidos controlados.
- No versionar archivos pesados en `public/uploads/` (ya ignorado en `.gitignore`).
