-- 023_normaliza_datos_socios
-- Normaliza datos existentes para alinearlos con el formulario de socios.

UPDATE socios
SET nombre_completo = NULLIF(TRIM(CONCAT_WS(' ', COALESCE(nombres, ''), COALESCE(apellidos, ''))), '')
WHERE COALESCE(nombre_completo, '') <> COALESCE(NULLIF(TRIM(CONCAT_WS(' ', COALESCE(nombres, ''), COALESCE(apellidos, ''))), ''), '');

UPDATE socios
SET numero_socio = LPAD(CAST(id AS CHAR), 6, '0')
WHERE numero_socio IS NULL OR TRIM(numero_socio) = '';

UPDATE socios
SET fecha_ingreso = COALESCE(fecha_ingreso, DATE(created_at), CURDATE())
WHERE fecha_ingreso IS NULL;
