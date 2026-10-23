<?php

class AccesoDatosTickets
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerTickets(): array
    {
        $sql = "
            SELECT
                t.idTicket,
                t.titulo,
                t.descripcion,
                t.fechaRegistro,
                t.fechaLimite,
                l.nombre AS laboratorio,
                t.prioridad,
                CASE
                    WHEN t.estado NOT IN ('Resuelto', 'Cancelado')
                         AND t.fechaLimite < CURDATE()
                    THEN 'Vencido'
                    ELSE t.estado
                END AS estado,
                u.usuario AS solicitante
            FROM ticket AS t
            INNER JOIN usuario AS u ON u.cedula = t.cedulaSolicitante
            INNER JOIN laboratorio AS l ON l.idLaboratorio = t.idLaboratorio
            ORDER BY t.idTicket DESC
        ";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerLaboratorios(): array
    {
        return $this->conexion->query(
            "SELECT idLaboratorio, nombre FROM laboratorio ORDER BY nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPrioridades(): array
    {
        return [
            ['nombre' => 'Baja'],
            ['nombre' => 'Media'],
            ['nombre' => 'Alta']
        ];
    }

    public function crearTicket(
        string $titulo,
        string $descripcion,
        string $fechaLimite,
        string $cedulaSolicitante,
        int $idLaboratorio,
        string $prioridad
    ): void {
        $sql = "
            INSERT INTO ticket
                (titulo, descripcion, fechaLimite, cedulaSolicitante, idLaboratorio, prioridad, estado)
            VALUES
                (:titulo, :descripcion, :fechaLimite, :cedulaSolicitante, :idLaboratorio, :prioridad, 'En espera')
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'fechaLimite' => $fechaLimite,
            'cedulaSolicitante' => $cedulaSolicitante,
            'idLaboratorio' => $idLaboratorio,
            'prioridad' => $prioridad
        ]);
    }


    public function revisarTicket(int $idTicket, string $prioridad, string $fechaLimite): bool
    {
        $q = $this->conexion->prepare("UPDATE ticket SET prioridad=:prioridad, fechaLimite=:fecha WHERE idTicket=:id AND estado='En espera'");
        $q->execute(['prioridad'=>$prioridad, 'fecha'=>$fechaLimite, 'id'=>$idTicket]);
        return $q->rowCount() > 0;
    }

    public function modificarTicket(
        int $idTicket,
        string $titulo,
        string $descripcion,
        string $fechaLimite,
        int $idLaboratorio,
        string $prioridad
    ): bool {
        $sql = "
            UPDATE ticket
            SET titulo = :titulo,
                descripcion = :descripcion,
                fechaLimite = :fechaLimite,
                idLaboratorio = :idLaboratorio,
                prioridad = :prioridad
            WHERE idTicket = :idTicket
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'idTicket' => $idTicket,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'fechaLimite' => $fechaLimite,
            'idLaboratorio' => $idLaboratorio,
            'prioridad' => $prioridad
        ]);
        return $consulta->rowCount() > 0;
    }

    public function eliminarTicket(int $idTicket): bool
    {
        $consulta = $this->conexion->prepare("
            UPDATE ticket
            SET estado = 'Cancelado'
            WHERE idTicket = :idTicket
              AND estado NOT IN ('Resuelto', 'Cancelado')
        ");
        $consulta->execute(['idTicket' => $idTicket]);
        return $consulta->rowCount() > 0;
    }

    public function cambiarEstado(int $idTicket, string $estado): bool
    {
        $estados = ['En espera', 'Pendiente', 'En Proceso', 'Resuelto', 'Cancelado'];
        if (!in_array($estado, $estados, true)) {
            return false;
        }
        $consulta = $this->conexion->prepare("
            UPDATE ticket
            SET estado = :estado
            WHERE idTicket = :idTicket
              AND estado NOT IN ('Resuelto', 'Cancelado')
        ");
        $consulta->execute(['idTicket' => $idTicket, 'estado' => $estado]);
        return $consulta->rowCount() > 0;
    }
}
