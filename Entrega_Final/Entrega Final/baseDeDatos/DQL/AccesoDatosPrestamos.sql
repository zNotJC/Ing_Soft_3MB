-- Consultar préstamos con equipo y usuario solicitante.
SELECT p.idPrestamo, e.nombre AS equipo, e.numeroSerie, u.cedula, u.usuario,
       p.fechaPrestamo, p.fechaDevolucion, p.estado, p.prioridad
FROM prestamo AS p
JOIN equipo AS e ON e.idEquipo = p.idEquipo
JOIN usuario AS u ON u.cedula = p.cedulaUsuario
ORDER BY p.idPrestamo DESC;

-- Contar préstamos por estado.
SELECT estado, COUNT(*) AS cantidad
FROM prestamo
GROUP BY estado
ORDER BY estado;
