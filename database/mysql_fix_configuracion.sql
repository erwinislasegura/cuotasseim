-- Script MySQL para asegurar tabla y columnas de configuración.
-- Uso:
--   mysql -u usuario -p nombre_base < database/mysql_fix_configuracion.sql

CREATE TABLE IF NOT EXISTS configuracion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_organizacion VARCHAR(140) NULL,
    nombre_sistema VARCHAR(140) NULL,
    logo VARCHAR(255) NULL,
    rut_organizacion VARCHAR(30) NULL,
    direccion VARCHAR(255) NULL,
    telefono VARCHAR(40) NULL,
    correo VARCHAR(120) NULL,
    sitio_web VARCHAR(120) NULL,
    flow_api_key VARCHAR(120) NULL,
    flow_secret_key VARCHAR(140) NULL,
    flow_modo_sandbox TINYINT(1) NOT NULL DEFAULT 1,
    cuota_por_defecto DECIMAL(12,2) DEFAULT 0,
    moneda VARCHAR(20) DEFAULT 'CLP',
    simbolo_moneda VARCHAR(5) DEFAULT '$',
    texto_comprobante TEXT NULL,
    observaciones_generales TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @schema_name := DATABASE();

SET @needs_flow_api_key := (
    SELECT COUNT(*) = 0
    FROM information_schema.columns
    WHERE table_schema = @schema_name
      AND table_name = 'configuracion'
      AND column_name = 'flow_api_key'
);
SET @sql := IF(@needs_flow_api_key, 'ALTER TABLE configuracion ADD COLUMN flow_api_key VARCHAR(120) NULL AFTER sitio_web', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @needs_flow_secret_key := (
    SELECT COUNT(*) = 0
    FROM information_schema.columns
    WHERE table_schema = @schema_name
      AND table_name = 'configuracion'
      AND column_name = 'flow_secret_key'
);
SET @sql := IF(@needs_flow_secret_key, 'ALTER TABLE configuracion ADD COLUMN flow_secret_key VARCHAR(140) NULL AFTER flow_api_key', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @needs_flow_modo_sandbox := (
    SELECT COUNT(*) = 0
    FROM information_schema.columns
    WHERE table_schema = @schema_name
      AND table_name = 'configuracion'
      AND column_name = 'flow_modo_sandbox'
);
SET @sql := IF(@needs_flow_modo_sandbox, 'ALTER TABLE configuracion ADD COLUMN flow_modo_sandbox TINYINT(1) NOT NULL DEFAULT 1 AFTER flow_secret_key', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO configuracion (
    nombre_organizacion,
    nombre_sistema,
    direccion,
    telefono,
    correo,
    flow_modo_sandbox
)
SELECT
    'Organización',
    'Sistema de Gestión de Cuotas',
    '',
    '',
    '',
    1
WHERE NOT EXISTS (
    SELECT 1 FROM configuracion
);

