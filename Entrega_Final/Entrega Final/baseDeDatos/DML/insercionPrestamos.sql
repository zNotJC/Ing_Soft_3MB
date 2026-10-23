-- Crear una solicitud de préstamo para un equipo disponible.
-- Antes de ejecutar, define: @idEquipo, @cedulaUsuario, @fechaPrestamo y
-- @fechaDevolucion. Usa fechas en formato 'AAAA-MM-DD'.

INSERT INTO prestamo (
    idEquipo,
    idLaboratorio,
    cedulaUsuario,
    fechaPrestamo,
    fechaDevolucion,
    estado,
    prioridad
)
SELECT
    e.idEquipo,
    e.idLaboratorio,
    @cedulaUsuario,
    @fechaPrestamo,
    @fechaDevolucion,
    'En espera',
    '-'
FROM equipo AS e
WHERE e.idEquipo = @idEquipo
  AND e.estado = 'Disponible'
  AND @cedulaUsuario IS NOT NULL
  AND @fechaPrestamo IS NOT NULL
  AND @fechaDevolucion IS NOT NULL
  AND @fechaDevolucion >= @fechaPrestamo;

SELECT ROW_COUNT() AS prestamos_creados;
