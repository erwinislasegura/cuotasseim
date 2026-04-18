-- Limpieza de datos operativos (mantiene catálogos y configuración base)
USE gestion_cuotas;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE rendicion_detalle;
TRUNCATE TABLE pago_detalle;
TRUNCATE TABLE movimientos_tesoreria;
TRUNCATE TABLE rendiciones;
TRUNCATE TABLE egresos;
TRUNCATE TABLE aportes;
TRUNCATE TABLE pagos;
TRUNCATE TABLE cuotas;
TRUNCATE TABLE socio_planes;
TRUNCATE TABLE auditoria;
TRUNCATE TABLE socios;

SET FOREIGN_KEY_CHECKS = 1;
