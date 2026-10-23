<?php

class AccesoDatosEquipos
{
    private PDO $conexion;

    public function __construct(PDO $conexion) { $this->conexion = $conexion; }

    public function listar(): array
    {
        $sql = "SELECT e.idEquipo, e.nombre, e.idLaboratorio, l.nombre AS laboratorio, e.tipo, e.estado
                FROM equipo e INNER JOIN laboratorio l ON l.idLaboratorio=e.idLaboratorio
                ORDER BY e.tipo, e.nombre";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre, int $idLaboratorio, string $tipo, string $estado): int
    {
        $q=$this->conexion->prepare('INSERT INTO equipo (nombre,idLaboratorio,tipo,estado) VALUES (:nombre,:lab,:tipo,:estado)');
        $q->execute(['nombre'=>$nombre,'lab'=>$idLaboratorio,'tipo'=>$tipo,'estado'=>$estado]);
        return (int)$this->conexion->lastInsertId();
    }

    public function modificar(int $id, string $nombre, int $idLaboratorio, string $tipo, string $estado): bool
    {
        $q=$this->conexion->prepare('UPDATE equipo SET nombre=:nombre,idLaboratorio=:lab,tipo=:tipo,estado=:estado WHERE idEquipo=:id');
        $q->execute(['id'=>$id,'nombre'=>$nombre,'lab'=>$idLaboratorio,'tipo'=>$tipo,'estado'=>$estado]);
        return $q->rowCount()>0;
    }

    public function eliminar(int $id): bool
    {
        $q=$this->conexion->prepare('DELETE FROM equipo WHERE idEquipo=:id AND NOT EXISTS (SELECT 1 FROM prestamo WHERE prestamo.idEquipo=equipo.idEquipo)');
        $q->execute(['id'=>$id]); return $q->rowCount()>0;
    }

    public function laboratorios(): array
    {
        return $this->conexion->query('SELECT idLaboratorio,nombre FROM laboratorio ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function equiposPorTipo(string $tipo): array
    {
        $q=$this->conexion->prepare("SELECT idEquipo,nombre,tipo,estado FROM equipo WHERE tipo=:tipo AND estado='Disponible' ORDER BY nombre");
        $q->execute(['tipo'=>$tipo]); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
}
