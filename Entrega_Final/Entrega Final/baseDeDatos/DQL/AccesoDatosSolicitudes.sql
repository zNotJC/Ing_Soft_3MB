SELECT
    s.idSolicitud,
    s.cedulaUsuario,
    u.usuario AS nombre,
    s.tipoSolicitud,
    s.descripcion,
    s.cantidad,
    s.equipo,
    s.fechaSolicitud,
    s.fechaNecesaria,
    s.estado,
    s.respuesta
FROM solicitud AS s
INNER JOIN usuario AS u
    ON u.cedula = s.cedulaUsuario
ORDER BY s.idSolicitud DESC;

SELECT
    idSolicitud,
    cedulaUsuario,
    tipoSolicitud,
    descripcion,
    cantidad,
    equipo,
    fechaSolicitud,
    fechaNecesaria,
    estado,
    respuesta
FROM solicitud
WHERE idSolicitud = :idSolicitud;
