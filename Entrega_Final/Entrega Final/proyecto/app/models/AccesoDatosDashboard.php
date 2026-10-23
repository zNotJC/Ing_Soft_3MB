<?php

class AccesoDatosDashboard
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerTicketsPendientes(): int
    {
        $sql = "
            SELECT COUNT(*) AS cantidad
            FROM ticket AS t
            INNER JOIN estado_ticket AS et
                ON et.idEstado = t.idEstado
            WHERE et.nombre = 'Pendiente'
        ";

        $consulta =
            $this->conexion->query($sql);

        return (int) $consulta->fetch()["cantidad"];
    }

    public function obtenerTicketsResueltos(): int
    {
        $sql = "
            SELECT COUNT(*) AS cantidad
            FROM ticket AS t
            INNER JOIN estado_ticket AS et
                ON et.idEstado = t.idEstado
            WHERE et.nombre = 'Resuelto'
        ";

        $consulta =
            $this->conexion->query($sql);

        return (int) $consulta->fetch()["cantidad"];
    }

    public function obtenerSolicitudesActivas(): int
    {
        $sql = "
            SELECT COUNT(*) AS cantidad
            FROM solicitud
            WHERE estado = 'Pendiente'
        ";

        $consulta =
            $this->conexion->query($sql);

        return (int) $consulta->fetch()["cantidad"];
    }

    public function obtenerPrestamosActivos(): int
    {
        $sql = "
            SELECT COUNT(*) AS cantidad
            FROM prestamo AS p
            INNER JOIN estado_prestamo AS ep
                ON ep.idEstado = p.idEstado
            WHERE ep.nombre = 'Activo'
        ";

        $consulta =
            $this->conexion->query($sql);

        return (int) $consulta->fetch()["cantidad"];
    }
}

?>