-- Crear un ticket para un equipo existente en un laboratorio disponible.
-- Antes de ejecutar, define: @titulo, @descripcion, @cedulaSolicitante,
-- @idLaboratorio y @idEquipo con valores reales.
-- El ticket comienza en espera, sin prioridad asignada ni fecha de atención.

INSERT INTO ticket (
    titulo,
    descripcion,
    fechaLimite,
    cedulaSolicitante,
    idLaboratorio,
    idEquipo,
    prioridad,
    estado
)
SELECT
    @titulo,
    @descripcion,
    NULL,
    @cedulaSolicitante,
    l.idLaboratorio,
    e.idEquipo,
    '-',
    'En espera'
FROM laboratorio AS l
INNER JOIN equipo AS e
    ON e.idLaboratorio = l.idLaboratorio
WHERE l.idLaboratorio = @idLaboratorio
  AND e.idEquipo = @idEquipo
  AND l.estado = 'Disponible'
  AND @cedulaSolicitante IS NOT NULL
  AND NULLIF(TRIM(@titulo), '') IS NOT NULL
  AND NULLIF(TRIM(@descripcion), '') IS NOT NULL;

SELECT ROW_COUNT() AS tickets_creados;
