<?php

class AccesoDatosEquipos
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $sql = "SELECT e.idEquipo, e.nombre, e.numeroSerie, e.idLugar, l.nombre AS lugar FROM equipo e INNER JOIN lugar l ON l.idLugar = e.idLugar ORDER BY e.idEquipo DESC";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre, string $numeroSerie, int $idLugar): int
    {
        $sql = "INSERT INTO equipo (nombre, numeroSerie, idLugar) VALUES (:nombre, :numeroSerie, :idLugar)";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['nombre' => $nombre, 'numeroSerie' => $numeroSerie, 'idLugar' => $idLugar]);
        return (int) $this->conexion->lastInsertId();
    }

    public function modificar(int $idEquipo, string $nombre, string $numeroSerie, int $idLugar): bool
    {
        $sql = "UPDATE equipo SET nombre = :nombre, numeroSerie = :numeroSerie, idLugar = :idLugar WHERE idEquipo = :idEquipo";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['idEquipo' => $idEquipo, 'nombre' => $nombre, 'numeroSerie' => $numeroSerie, 'idLugar' => $idLugar]);
        return $consulta->rowCount() > 0;
    }

    public function eliminar(int $idEquipo): bool
    {
        $consulta = $this->conexion->prepare("DELETE FROM equipo WHERE idEquipo = :idEquipo AND NOT EXISTS (SELECT 1 FROM prestamo WHERE prestamo.idEquipo = equipo.idEquipo)");
        $consulta->execute(['idEquipo' => $idEquipo]);
        return $consulta->rowCount() > 0;
    }

    public function lugares(): array
    {
        return $this->conexion->query('SELECT idLugar, nombre FROM lugar ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    }
}
