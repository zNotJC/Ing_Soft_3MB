-- Consultar tickets con solicitante, laboratorio, prioridad y estado.
SELECT t.idTicket, t.titulo, t.descripcion, t.fechaRegistro, t.fechaLimite,
       l.nombre AS laboratorio, t.prioridad, t.estado, u.usuario AS solicitante
FROM ticket AS t
JOIN usuario AS u ON u.cedula = t.cedulaSolicitante
JOIN laboratorio AS l ON l.idLaboratorio = t.idLaboratorio
ORDER BY t.idTicket DESC;

-- Contar tickets por estado.
SELECT estado, COUNT(*) AS cantidad
FROM ticket
GROUP BY estado
ORDER BY estado;
