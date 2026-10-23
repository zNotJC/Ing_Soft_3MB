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
        $historial = [];

        $sqlTickets = "
            SELECT
                t.idTicket AS id,
                'Ticket' AS tipo,
                CONCAT(
                    t.titulo,
                    ': ',
                    t.descripcion
                ) AS detalle,
                u.usuario AS usuario,
                et.nombre AS estado,
                t.fechaRegistro AS fecha
            FROM ticket AS t
            INNER JOIN usuario AS u
                ON u.cedula = t.cedulaSolicitante
            INNER JOIN estado_ticket AS et
                ON et.idEstado = t.idEstado
        ";

        $consultaTickets =
            $this->conexion->query($sqlTickets);

        foreach ($consultaTickets->fetchAll() as $ticket) {

            $historial[] = $ticket;
        }

        $sqlSolicitudes = "
            SELECT
                s.idSolicitud AS id,
                'Solicitud' AS tipo,
                s.descripcion AS detalle,
                u.usuario AS usuario,
                s.estado AS estado,
                s.fechaSolicitud AS fecha
            FROM solicitud AS s
            INNER JOIN usuario AS u
                ON u.cedula = s.cedulaUsuario
        ";

        $consultaSolicitudes =
            $this->conexion->query($sqlSolicitudes);

        foreach ($consultaSolicitudes->fetchAll() as $solicitud) {

            $historial[] = $solicitud;
        }

        $sqlPrestamos = "
            SELECT
                p.idPrestamo AS id,
                'Préstamo' AS tipo,
                CONCAT(
                    'Equipo: ',
                    e.nombre
                ) AS detalle,
                u.usuario AS usuario,
                ep.nombre AS estado,
                p.fechaPrestamo AS fecha
            FROM prestamo AS p
            INNER JOIN equipo AS e
                ON e.idEquipo = p.idEquipo
            INNER JOIN usuario AS u
                ON u.cedula = p.cedulaUsuario
            INNER JOIN estado_prestamo AS ep
                ON ep.idEstado = p.idEstado
        ";

        $consultaPrestamos =
            $this->conexion->query($sqlPrestamos);

        foreach ($consultaPrestamos->fetchAll() as $prestamo) {

            $historial[] = $prestamo;
        }

        usort(
            $historial,
            function ($a, $b) {

                return strcmp(
                    $b["fecha"],
                    $a["fecha"]
                );
            }
        );

        return $historial;
    }
}

?>