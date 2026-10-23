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

                l.nombre AS lugar,

                p.nombre AS prioridad,

                CASE
                    WHEN e.nombre NOT IN ('Resuelto', 'Cancelado')
                         AND t.fechaLimite < CURDATE()
                    THEN 'Vencido'
                    ELSE e.nombre
                END AS estado,

                u.usuario AS solicitante

            FROM TICKET AS t

            INNER JOIN USUARIO AS u
                ON u.cedula = t.cedulaSolicitante

            INNER JOIN LUGAR AS l
                ON l.idLugar = t.idLugar

            INNER JOIN PRIORIDAD AS p
                ON p.idPrioridad = t.idPrioridad

            INNER JOIN ESTADO_TICKET AS e
                ON e.idEstado = t.idEstado

            ORDER BY t.idTicket DESC
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }


    public function obtenerLugares(): array
    {
        $sql = "
            SELECT
                idLugar,
                nombre
            FROM LUGAR
            ORDER BY idLugar
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }


    public function obtenerPrioridades(): array
    {
        $sql = "
            SELECT
                idPrioridad,
                nombre
            FROM PRIORIDAD
            ORDER BY idPrioridad
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }


    public function crearTicket(
        string $titulo,
        string $descripcion,
        string $fechaLimite,
        string $cedulaSolicitante,
        int $idLugar,
        int $idPrioridad
    ): void {

        $sql = "
            INSERT INTO TICKET
            (
                titulo,
                descripcion,
                fechaLimite,
                cedulaSolicitante,
                idLugar,
                idPrioridad,
                idEstado
            )
            VALUES
            (
                :titulo,
                :descripcion,
                :fechaLimite,
                :cedulaSolicitante,
                :idLugar,
                :idPrioridad,

                (
                    SELECT idEstado
                    FROM ESTADO_TICKET
                    WHERE nombre = 'Pendiente'
                )
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "fechaLimite" => $fechaLimite,
            "cedulaSolicitante" => $cedulaSolicitante,
            "idLugar" => $idLugar,
            "idPrioridad" => $idPrioridad
        ]);
    }

    
    public function modificarTicket(
        int $idTicket,
        string $titulo,
        string $descripcion,
        string $fechaLimite,
        int $idLugar,
        int $idPrioridad
    ): bool {
        $sql = "
            UPDATE TICKET
            SET titulo = :titulo, descripcion = :descripcion, fechaLimite = :fechaLimite, idLugar = :idLugar, idPrioridad = :idPrioridad
            WHERE idTicket = :idTicket
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'idTicket' => $idTicket,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'fechaLimite' => $fechaLimite,
            'idLugar' => $idLugar,
            'idPrioridad' => $idPrioridad
        ]);
        return $consulta->rowCount() > 0;
    }

    public function eliminarTicket(int $idTicket): bool
    {
        $sql = "
            UPDATE TICKET
            SET idEstado = (SELECT idEstado FROM ESTADO_TICKET WHERE nombre = 'Cancelado')
            WHERE idTicket = :idTicket
              AND idEstado NOT IN (SELECT idEstado FROM ESTADO_TICKET WHERE nombre IN ('Resuelto', 'Cancelado'))
        ";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['idTicket' => $idTicket]);
        return $consulta->rowCount() > 0;
    }

    public function cambiarEstado(
        int $idTicket,
        string $estado
    ): bool {

        $sql = "
            UPDATE TICKET

            SET idEstado = (
                SELECT idEstado
                FROM ESTADO_TICKET
                WHERE nombre = :estado
            )

            WHERE idTicket = :idTicket


            AND idEstado NOT IN (
                SELECT idEstado
                FROM ESTADO_TICKET
                WHERE nombre IN ('Resuelto', 'Cancelado')
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "idTicket" => $idTicket,
            "estado" => $estado
        ]);
        return $consulta->rowCount() > 0;
    }
}
