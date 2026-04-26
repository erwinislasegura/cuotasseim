# QA técnico — Hallazgos críticos y altos (2026-04-26)

## Alcance revisado
- Registro de cuotas y periodicidades (mensual, trimestral, semestral, anual).
- Aportes, egresos/retiros y trazabilidad en tesorería.
- Flujo de pago con Flow y registro automático de pagos.

## Hallazgos
1. **Crítico — Migraciones base no crean estructura funcional**: varias migraciones clave de periodos/cuotas/pagos/aportes/egresos/tesorería están en `SELECT 1`, por lo que un entorno nuevo levantado por `migration_runner.php` no queda operativo.
2. **Alto — Inconsistencia catálogo/rutas vs documentación**: la documentación declara módulos Rendiciones, Tesorería y Auditoría, pero no existen rutas dedicadas para esos módulos en `routes.php`, dificultando validar “todo el flujo” desde navegación normal.
3. **Alto — Flow permite considerar socios/cuotas no vigentes**: búsqueda por RUT no filtra socios eliminados/inactivos y listado de cuotas pendientes no filtra `deleted_at`, abriendo riesgo de cobrar registros que deberían estar fuera de operación.
4. **Alto — Confirmación y retorno Flow comparten endpoint de retorno visual**: `urlConfirmation` y `urlReturn` apuntan a la misma ruta de vista (`/pago-flow/retorno`) en vez de separar callback técnico y retorno de usuario, aumentando riesgo de duplicados/errores operacionales.
5. **Alto — Errores de registro Flow se silencian**: en `registrarPagoAprobadoFlow`, cualquier excepción se captura sin log ni propagación, dejando pagos “aceptados” en pantalla pero potencialmente no registrados en BD.
6. **Medio — Filtro de reportes usa claves de origen inconsistentes**: UI ofrece `aporte`/`retiro`, mientras tesorería persiste `aportes`/`egresos`; el filtro por origen puede devolver resultados incompletos.
7. **Medio — Egreso fuerza `cuenta_bancaria_id = null` al guardar**: se pierde asociación bancaria en retiros/egresos, afectando conciliación por cuenta.

## Riesgo de negocio
- Cobros erróneos a socios no vigentes.
- Pérdida de trazabilidad en tesorería y conciliación bancaria.
- Falsos positivos de “pago aceptado” sin persistencia real.
- Ambientes nuevos sin estructura completa si dependen de migraciones incrementales.
