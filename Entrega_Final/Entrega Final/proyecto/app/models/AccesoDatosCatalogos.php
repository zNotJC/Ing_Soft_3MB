<?php

class AccesoDatosCatalogos
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarLaboratorios(): array
    {
        return $this->conexion->query(
            'SELECT idLaboratorio AS id, nombre FROM laboratorio ORDER BY nombre'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearLaboratorio(string $nombre): int
    {
        $consulta = $this->conexion->prepare('INSERT INTO laboratorio (nombre) VALUES (:nombre)');
        $consulta->execute(['nombre' => $nombre]);
        return (int) $this->conexion->lastInsertId();
    }

    public function modificarLaboratorio(int $id, string $nombre): bool
    {
        $consulta = $this->conexion->prepare(
            'UPDATE laboratorio SET nombre = :nombre WHERE idLaboratorio = :id'
        );
        $consulta->execute(['id' => $id, 'nombre' => $nombre]);
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
