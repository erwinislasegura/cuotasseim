# Módulos
Panel, Socios, Cuotas, Pagos, Aportes, Egresos, Rendiciones, Tesorería, Reportes, Usuarios, Roles, Configuración y Auditoría.

Cada módulo está preconectado bajo MVC y rutas limpias para crecer con CRUD completo.

## Tesorería (automatizada)
- La vista de **Tesorería** permite registrar movimientos manuales (origen `manual`) desde un formulario dedicado.
- Los movimientos se generan y actualizan automáticamente desde:
  - **Pagos** (ingresos).
  - **Aportes** (ingresos).
  - **Egresos** (egresos).
- Si un registro cambia a estado distinto de `aplicado` o se elimina, su movimiento asociado se retira de tesorería.
- El `saldo_referencial` se recalcula automáticamente en cada sincronización.
