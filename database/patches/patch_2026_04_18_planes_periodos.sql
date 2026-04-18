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

ALTER TABLE periodos
    DROP INDEX uk_periodo;

COMMIT;
