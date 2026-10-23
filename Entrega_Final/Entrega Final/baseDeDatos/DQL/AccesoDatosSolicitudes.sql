-- Consultar solicitudes junto con la cuenta que las creó.
SELECT s.idSolicitud, s.cedulaUsuario, u.usuario, s.tipoSolicitud, s.descripcion,
       s.cantidad, s.equipo, s.fechaSolicitud, s.fechaNecesaria, s.estado, s.respuesta
FROM solicitud AS s
JOIN usuario AS u ON u.cedula = s.cedulaUsuario
ORDER BY s.idSolicitud DESC;

-- Filtrar las solicitudes de una cuenta.
SELECT idSolicitud, tipoSolicitud, descripcion, cantidad, equipo,
       fechaSolicitud, fechaNecesaria, estado, respuesta
FROM solicitud
WHERE cedulaUsuario = :cedulaUsuario
ORDER BY idSolicitud DESC;
