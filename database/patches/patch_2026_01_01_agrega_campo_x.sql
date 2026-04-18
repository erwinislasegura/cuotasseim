-- Patch incremental de ejemplo
ALTER TABLE pagos ADD COLUMN observacion_interna VARCHAR(255) NULL AFTER observacion;
