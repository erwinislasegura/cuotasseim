USE gestion_cuotas;

INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total'),
('Tesorero', 'Operación financiera'),
('Consulta', 'Solo lectura');

INSERT INTO usuarios (nombre, correo, usuario, password, rol_id, activo) VALUES
('Admin General', 'admin@local.test', 'admin', '$2y$10$2QSYU4kkt9NhjzP9MryZz.g1GC2wkjI93G8xw6ZV9w6fPzE6lDPL2', 1, 1),
('Tesorero Demo', 'tesorero@local.test', 'tesorero', '$2y$10$2QSYU4kkt9NhjzP9MryZz.g1GC2wkjI93G8xw6ZV9w6fPzE6lDPL2', 2, 1),
('Consulta Demo', 'consulta@local.test', 'consulta', '$2y$10$2QSYU4kkt9NhjzP9MryZz.g1GC2wkjI93G8xw6ZV9w6fPzE6lDPL2', 3, 1);

INSERT INTO tipos_socio (nombre) VALUES ('Regular'),('Honorario'),('Cooperador'),('Exento'),('Otro');
INSERT INTO estados_socio (nombre) VALUES ('Activo'),('Moroso'),('Suspendido'),('Retirado'),('Honorario');
INSERT INTO medios_pago (nombre) VALUES ('Transferencia'),('Depósito'),('Efectivo'),('Caja'),('Cheque'),('Otro');
INSERT INTO tipos_aporte (nombre) VALUES ('Honorario'),('Donación'),('Cooperación'),('Extraordinario'),('Apoyo especial');
INSERT INTO tipos_egreso (nombre) VALUES ('Rendición'),('Gasto operativo'),('Mantención'),('Compra'),('Apoyo social'),('Administración'),('Otro');

INSERT INTO configuracion (nombre_organizacion, nombre_sistema, direccion, telefono, correo, cuota_por_defecto, texto_comprobante)
VALUES ('Organización Demo', 'Sistema de Gestión de Cuotas', 'Dirección referencial 123', '+56 9 0000 0000', 'info@organizacion.test', 15000, 'Gracias por su pago.');
