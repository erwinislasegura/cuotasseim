-- Patch: Ajuste de módulo periodos para operar como planes.
-- Motor objetivo: MySQL 8+

START TRANSACTION;

ALTER TABLE periodos
    ADD COLUMN IF NOT EXISTS tipo_periodo ENUM('mensual','trimestral','semestral','anual') NULL AFTER nombre_periodo,
    ADD COLUMN IF NOT EXISTS monto_a_pagar DECIMAL(12,2) NULL AFTER tipo_periodo,
    MODIFY COLUMN nombre_periodo VARCHAR(100) NOT NULL,
    MODIFY COLUMN anio SMALLINT NULL,
    MODIFY COLUMN mes TINYINT NULL,
    MODIFY COLUMN fecha_inicio DATE NULL,
    MODIFY COLUMN fecha_fin DATE NULL,
    MODIFY COLUMN fecha_vencimiento DATE NULL;

UPDATE periodos
SET tipo_periodo = 'mensual'
WHERE tipo_periodo IS NULL;

UPDATE periodos
SET monto_a_pagar = 0
WHERE monto_a_pagar IS NULL;

ALTER TABLE periodos
    MODIFY COLUMN tipo_periodo ENUM('mensual','trimestral','semestral','anual') NOT NULL,
    MODIFY COLUMN monto_a_pagar DECIMAL(12,2) NOT NULL DEFAULT 0;

SET @uk_periodo_exists := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'periodos'
      AND index_name = 'uk_periodo'
);
SET @drop_uk_periodo_sql := IF(@uk_periodo_exists > 0, 'ALTER TABLE periodos DROP INDEX uk_periodo', 'SELECT 1');
PREPARE drop_uk_periodo_stmt FROM @drop_uk_periodo_sql;
EXECUTE drop_uk_periodo_stmt;
DEALLOCATE PREPARE drop_uk_periodo_stmt;

CREATE TABLE IF NOT EXISTS socio_planes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    socio_id BIGINT UNSIGNED NOT NULL,
    periodo_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_socio_plan (socio_id, periodo_id),
    CONSTRAINT fk_socio_planes_socio FOREIGN KEY (socio_id) REFERENCES socios(id),
    CONSTRAINT fk_socio_planes_periodo FOREIGN KEY (periodo_id) REFERENCES periodos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
