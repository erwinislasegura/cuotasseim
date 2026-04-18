# Política de cambios y actualizaciones
1. Toda mejora funcional debe incluir migración SQL nueva.
2. Nunca modificar producción sin script incremental en `database/patches`.
3. Actualizar `seed.sql` si el cambio agrega catálogos o datos base.
4. Actualizar documentación técnica en `docs/` y README.
5. Registrar en changelog interno del repositorio.
