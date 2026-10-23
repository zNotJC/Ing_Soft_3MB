SELECT
    p.idPrestamo,
    e.idEquipo,
    e.nombre AS equipo,
    e.numeroSerie,
    u.cedula,
    u.usuario AS nombre,
    p.fechaPrestamo,
    p.fechaDevolucion,
    ep.idEstado,
    ep.nombre AS estado
FROM prestamo AS p
INNER JOIN equipo AS e
    ON e.idEquipo = p.idEquipo
INNER JOIN usuario AS u
    ON u.cedula = p.cedulaUsuario
INNER JOIN estado_prestamo AS ep
    ON ep.idEstado = p.idEstado
ORDER BY p.idPrestamo DESC;

SELECT idEquipo, nombre, numeroSerie
FROM equipo
ORDER BY nombre;

SELECT idEstado, nombre
FROM estado_prestamo
ORDER BY idEstado;

SELECT idPrestamo, idEquipo, cedulaUsuario, fechaPrestamo, fechaDevolucion, idEstado
FROM prestamo
WHERE idPrestamo = :idPrestamo;
