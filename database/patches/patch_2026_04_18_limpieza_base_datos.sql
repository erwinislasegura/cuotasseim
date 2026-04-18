-- Limpieza de datos operativos (mantiene catálogos y configuración base)
USE gestion_cuotas;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM rendicion_detalle;
ALTER TABLE rendicion_detalle AUTO_INCREMENT = 1;
DELETE FROM pago_detalle;
ALTER TABLE pago_detalle AUTO_INCREMENT = 1;
DELETE FROM movimientos_tesoreria;
ALTER TABLE movimientos_tesoreria AUTO_INCREMENT = 1;
DELETE FROM rendiciones;
ALTER TABLE rendiciones AUTO_INCREMENT = 1;
DELETE FROM egresos;
ALTER TABLE egresos AUTO_INCREMENT = 1;
DELETE FROM aportes;
ALTER TABLE aportes AUTO_INCREMENT = 1;
DELETE FROM pagos;
ALTER TABLE pagos AUTO_INCREMENT = 1;
DELETE FROM cuotas;
ALTER TABLE cuotas AUTO_INCREMENT = 1;
DELETE FROM socio_planes;
ALTER TABLE socio_planes AUTO_INCREMENT = 1;
DELETE FROM auditoria;
ALTER TABLE auditoria AUTO_INCREMENT = 1;
DELETE FROM socios;
ALTER TABLE socios AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;
