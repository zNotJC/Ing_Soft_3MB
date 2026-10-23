# Migración v1.4

1. Exporta la base `jojolos` desde phpMyAdmin antes de comenzar.
2. Selecciona la base `jojolos` en phpMyAdmin.
3. Abre SQL y ejecuta `migracionV1_4.sql` una sola vez.
4. Verifica que `equipo` conserve `idEquipo`, `nombre`, `idLaboratorio`, `tipo`, `estado`, y que `solicitud.tipoSolicitud` acepte `Laboratorio` y `Reparación`.

La migración elimina `numeroSerie`, `marca` y `modelo` de `equipo`, por lo que debe realizarse después del respaldo. Las filas de equipos existentes se conservan y los estados antiguos se traducen a los estados nuevos.
