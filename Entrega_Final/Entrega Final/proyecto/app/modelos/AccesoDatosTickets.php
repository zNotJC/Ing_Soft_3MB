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
                t.idLaboratorio,
                t.idEquipo,
                e.nombre AS equipo,
                t.titulo,
                t.descripcion,
                t.fechaRegistro,
                t.fechaLimite AS fechaAtencion,
                l.nombre AS laboratorio,
                t.prioridad,
                t.estado AS estado,
                u.usuario AS solicitante
            FROM ticket AS t
            INNER JOIN usuario AS u ON u.cedula = t.cedulaSolicitante
            INNER JOIN laboratorio AS l ON l.idLaboratorio = t.idLaboratorio
            LEFT JOIN equipo AS e ON e.idEquipo = t.idEquipo
            ORDER BY t.idTicket DESC
        ";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerLaboratorios(): array
    {
        return $this->conexion->query(
            "SELECT idLaboratorio, nombre FROM laboratorio WHERE estado = 'Disponible' ORDER BY nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEquipos(): array
    {
        return $this->conexion->query("SELECT idEquipo, nombre, tipo, idLaboratorio, estado FROM equipo WHERE estado IN ('Disponible','Roto','En uso') ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
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
        string $cedulaSolicitante,
        int $idLaboratorio,
        string $prioridad,
        ?int $idEquipo = null
    ): void {
        $sql = "
            INSERT INTO ticket
                (titulo, descripcion, fechaLimite, cedulaSolicitante, idLaboratorio, idEquipo, prioridad, estado)
            VALUES
                (:titulo, :descripcion, NULL, :cedulaSolicitante, :idLaboratorio, :idEquipo, '-', 'En espera')
        ";
        $verificar = $this->conexion->prepare("SELECT COUNT(*) FROM equipo WHERE idEquipo = :idEquipo AND idLaboratorio = :idLaboratorio");
        $verificar->execute(['idEquipo' => $idEquipo, 'idLaboratorio' => $idLaboratorio]);
        if (!$idEquipo || (int)$verificar->fetchColumn() !== 1) {
            throw new InvalidArgumentException('Selecciona un equipo registrado en el laboratorio elegido.');
        }
        $verificarLaboratorio = $this->conexion->prepare("SELECT COUNT(*) FROM laboratorio WHERE idLaboratorio = :idLaboratorio AND estado = 'Disponible'");
        $verificarLaboratorio->execute(['idLaboratorio' => $idLaboratorio]);
        if ((int)$verificarLaboratorio->fetchColumn() !== 1) {
            throw new InvalidArgumentException('El laboratorio seleccionado no está disponible.');
        }
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'cedulaSolicitante' => $cedulaSolicitante,
            'idLaboratorio' => $idLaboratorio,
            'idEquipo' => $idEquipo,
        ]);
    }


    public function aceptarTicket(int $idTicket, string $prioridad): bool
    {
        $q = $this->conexion->prepare("UPDATE ticket SET prioridad=:prioridad, fechaLimite=CURDATE(), estado='En Proceso' WHERE idTicket=:id AND estado IN ('En espera','Pendiente')");
        $q->execute(['prioridad'=>$prioridad, 'id'=>$idTicket]);
        return $q->rowCount() > 0;
    }

    public function denegarTicket(int $idTicket, string $prioridad): bool
    {
        $q = $this->conexion->prepare("UPDATE ticket SET prioridad=:prioridad, estado='Denegado' WHERE idTicket=:id AND estado IN ('En espera','Pendiente')");
        $q->execute(['prioridad'=>$prioridad, 'id'=>$idTicket]);
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
        $estados = ['En espera', 'En Proceso', 'Resuelto', 'Cancelado'];
        if (!in_array($estado, $estados, true)) {
            return false;
        }
        $permitidos = [
            'En Proceso' => "estado = 'En espera' AND prioridad IN ('Baja','Media','Alta') AND fechaLimite IS NOT NULL",
            'En espera' => "estado = 'En espera'",
            'Resuelto' => "estado = 'En Proceso'",
            'Cancelado' => "estado = 'En Proceso'"
        ];
        if (!isset($permitidos[$estado])) return false;
        $consulta = $this->conexion->prepare("
            UPDATE ticket SET estado = :estado
            WHERE idTicket = :idTicket AND " . $permitidos[$estado]
        );
        $consulta->execute(['idTicket' => $idTicket, 'estado' => $estado]);
        return $consulta->rowCount() > 0;
    }
}
