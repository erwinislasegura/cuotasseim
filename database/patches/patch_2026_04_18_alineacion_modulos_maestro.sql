-- Patch: Alineación de esquema con Prompt Maestro (módulos 1..21)
-- Motor objetivo: MySQL 8+
-- Ejecutar en entorno de desarrollo y validar antes de producción.

START TRANSACTION;

-- =========================
-- Catálogos base
-- =========================
ALTER TABLE tipos_socio
    ADD COLUMN IF NOT EXISTS descripcion VARCHAR(255) NULL AFTER nombre,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER activo,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE estados_socio
    ADD COLUMN IF NOT EXISTS descripcion VARCHAR(255) NULL AFTER nombre,
    ADD COLUMN IF NOT EXISTS color_badge VARCHAR(30) NULL AFTER descripcion,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER activo,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE medios_pago
    ADD COLUMN IF NOT EXISTS descripcion VARCHAR(255) NULL AFTER nombre,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER activo,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE tipos_aporte
    ADD COLUMN IF NOT EXISTS descripcion VARCHAR(255) NULL AFTER nombre,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER activo,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE tipos_egreso
    ADD COLUMN IF NOT EXISTS descripcion VARCHAR(255) NULL AFTER nombre,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER activo,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- =========================
-- Núcleo operativo
-- =========================
ALTER TABLE periodos
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE conceptos_cobro
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER activo,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE pago_detalle
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE rendicion_detalle
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE cuentas_bancarias
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE movimientos_tesoreria
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL AFTER updated_at;

-- =========================
-- Auditoría: compatibilidad
-- =========================
ALTER TABLE auditoria
    ADD COLUMN IF NOT EXISTS registro_id BIGINT UNSIGNED NULL AFTER accion,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER user_agent;

UPDATE auditoria
SET registro_id = id_registro
WHERE registro_id IS NULL AND id_registro IS NOT NULL;

UPDATE auditoria
SET created_at = fecha
WHERE created_at IS NULL AND fecha IS NOT NULL;

-- Índices sugeridos: aplicar según volumen y necesidad operacional.
-- (Se omiten CREATE INDEX automáticos para evitar conflictos por índices existentes).

COMMIT;
