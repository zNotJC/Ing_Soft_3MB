<?php

class AccesoDatosHistorial
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerHistorial(): array
    {
        $sql = "
            SELECT id, tipo, detalle, usuario, estado, fecha
            FROM (
                SELECT t.idTicket AS id, 'Ticket' AS tipo,
                       CONCAT(t.titulo, ': ', t.descripcion) AS detalle,
                       u.usuario AS usuario, t.estado AS estado, t.fechaRegistro AS fecha
                FROM ticket AS t
                INNER JOIN usuario AS u ON u.cedula = t.cedulaSolicitante
                UNION ALL
                SELECT s.idSolicitud AS id, 'Solicitud' AS tipo, s.descripcion AS detalle,
                       u.usuario AS usuario, s.estado AS estado, CAST(s.fechaSolicitud AS DATETIME) AS fecha
                FROM solicitud AS s
                INNER JOIN usuario AS u ON u.cedula = s.cedulaUsuario
                UNION ALL
                SELECT p.idPrestamo AS id, 'Préstamo' AS tipo,
                       CONCAT('Equipo: ', e.nombre) AS detalle,
                       u.usuario AS usuario, p.estado AS estado, CAST(p.fechaPrestamo AS DATETIME) AS fecha
                FROM prestamo AS p
                INNER JOIN equipo AS e ON e.idEquipo = p.idEquipo
                INNER JOIN usuario AS u ON u.cedula = p.cedulaUsuario
            ) AS eventos
            ORDER BY fecha DESC
        ";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
