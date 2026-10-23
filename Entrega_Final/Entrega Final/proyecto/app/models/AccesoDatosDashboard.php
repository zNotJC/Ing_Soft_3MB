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
        $consulta = $this->conexion->query("SELECT COUNT(*) AS cantidad FROM ticket WHERE estado = 'Pendiente'");
        return (int) $consulta->fetch()['cantidad'];
    }

    public function obtenerTicketsResueltos(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) AS cantidad FROM ticket WHERE estado = 'Resuelto'");
        return (int) $consulta->fetch()['cantidad'];
    }

    public function obtenerSolicitudesActivas(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) AS cantidad FROM solicitud WHERE estado = 'Pendiente'");
        return (int) $consulta->fetch()['cantidad'];
    }

    public function obtenerPrestamosActivos(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) AS cantidad FROM prestamo WHERE estado = 'Activo'");
        return (int) $consulta->fetch()['cantidad'];
    }

    public function obtenerEquiposRegistrados(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) AS cantidad FROM equipo");
        return (int) $consulta->fetch()['cantidad'];
    }
}
