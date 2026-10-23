<?php

class AccesoDatosLaboratorios
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function crearLaboratorio(string $nombre, string $estado = 'Disponible'): int
    {
        $consulta = $this->conexion->prepare('INSERT INTO laboratorio (nombre, estado) VALUES (:nombre, :estado)');
        $consulta->execute(['nombre' => $nombre, 'estado' => $estado]);
        return (int) $this->conexion->lastInsertId();
    }

    public function modificarLaboratorio(int $id, string $nombre, string $estado = 'Disponible'): bool
    {
        $consulta = $this->conexion->prepare(
            'UPDATE laboratorio SET nombre = :nombre, estado = :estado WHERE idLaboratorio = :id'
        );
        $consulta->execute(['id' => $id, 'nombre' => $nombre, 'estado' => $estado]);
        return $consulta->rowCount() > 0;
    }

    public function eliminarLaboratorio(int $id): bool
    {
        $consulta = $this->conexion->prepare(
            'DELETE FROM laboratorio WHERE idLaboratorio = :id AND NOT EXISTS (SELECT 1 FROM equipo WHERE equipo.idLaboratorio = laboratorio.idLaboratorio)'
        );
        $consulta->execute(['id' => $id]);
        return $consulta->rowCount() > 0;
    }
}
