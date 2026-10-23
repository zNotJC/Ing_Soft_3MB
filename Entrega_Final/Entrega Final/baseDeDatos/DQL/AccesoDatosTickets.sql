SELECT
    t.idTicket,
    t.titulo,
    t.descripcion,
    t.fechaRegistro,
    t.fechaLimite,
    t.cedulaSolicitante,
    CONCAT('Doc. ', u.usuario) AS solicitante,
    l.nombre AS lugar,
    p.nombre AS prioridad,
    CASE
        WHEN e.nombre NOT IN ('Resuelto', 'Cancelado')
             AND t.fechaLimite < CURDATE()
        THEN 'Vencido'
        ELSE e.nombre
    END AS estado
FROM ticket AS t
INNER JOIN docente AS d
    ON d.cedula = t.cedulaSolicitante
INNER JOIN usuario AS u
    ON u.cedula = d.cedula
INNER JOIN lugar AS l
    ON l.idLugar = t.idLugar
INNER JOIN prioridad AS p
    ON p.idPrioridad = t.idPrioridad
INNER JOIN estado_ticket AS e
    ON e.idEstado = t.idEstado
ORDER BY t.idTicket DESC;
