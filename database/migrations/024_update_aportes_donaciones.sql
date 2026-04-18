-- 024_update_aportes_donaciones
-- Ajusta aportes para registrar donaciones con detalle/comentario.

ALTER TABLE aportes
    ADD COLUMN IF NOT EXISTS comentario VARCHAR(255) NULL AFTER descripcion;

UPDATE aportes
SET comentario = COALESCE(NULLIF(comentario, ''), NULLIF(observacion, ''), NULLIF(descripcion, ''))
WHERE comentario IS NULL OR comentario = '';
