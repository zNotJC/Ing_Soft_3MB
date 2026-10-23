<?php

class AccesoDatosSolicitudes
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerSolicitudes(?string $cedulaUsuario = null): array
    {
        $sql = "
            SELECT
                s.idSolicitud,
                s.cedulaUsuario,
                u.usuario AS nombre,
                s.tipoSolicitud,
                s.descripcion,
                s.equipo,
                s.fechaSolicitud,
                s.fechaNecesaria,
                s.estado,
                s.respuesta
            FROM solicitud AS s
            INNER JOIN usuario AS u
                ON u.cedula = s.cedulaUsuario
            " . ($cedulaUsuario !== null ? "WHERE s.cedulaUsuario = :cedulaUsuario" : "") . "
            ORDER BY s.idSolicitud DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        if ($cedulaUsuario !== null) $consulta->bindValue(':cedulaUsuario', $cedulaUsuario);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    public function obtenerSolicitud(
        int $idSolicitud
    ): ?array {

        $sql = "
            SELECT
                idSolicitud,
                cedulaUsuario,
                tipoSolicitud,
                descripcion,
                equipo,
                fechaSolicitud,
                fechaNecesaria,
                estado,
                respuesta
            FROM solicitud
            WHERE idSolicitud = :idSolicitud
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idSolicitud" => $idSolicitud
        ]);

        $solicitud =
            $consulta->fetch();

        return $solicitud ?: null;
    }

    public function crearSolicitud(
        string $cedulaUsuario,
        string $tipoSolicitud,
        string $descripcion,
        ?string $equipo,
        string $fechaSolicitud,
        ?string $fechaNecesaria
    ): void {

        $sql = "
            INSERT INTO solicitud
            (
                cedulaUsuario,
                tipoSolicitud,
                descripcion,
                cantidad,
                equipo,
                fechaSolicitud,
                fechaNecesaria,
                estado
            )
            VALUES
            (
                :cedulaUsuario,
                :tipoSolicitud,
                :descripcion,
                1,
                :equipo,
                :fechaSolicitud,
                :fechaNecesaria,
                'En espera'
            )
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "cedulaUsuario" => $cedulaUsuario,
            "tipoSolicitud" => $tipoSolicitud,
            "descripcion" => $descripcion,
            "equipo" => $equipo,
            "fechaSolicitud" => $fechaSolicitud,
            "fechaNecesaria" => $fechaNecesaria
        ]);
    }

    public function modificarSolicitud(
        int $idSolicitud,
        string $tipoSolicitud,
        string $descripcion,
        ?string $equipo,
        ?string $fechaNecesaria,
        ?string $cedulaUsuario = null
    ): bool {

        $sql = "
            UPDATE solicitud
            SET
                tipoSolicitud = :tipoSolicitud,
                descripcion = :descripcion,
                cantidad = 1,
                equipo = :equipo,
                fechaNecesaria = :fechaNecesaria
            WHERE idSolicitud = :idSolicitud
            AND estado IN ('En espera','Pendiente')
            " . ($cedulaUsuario !== null ? "AND cedulaUsuario = :cedulaUsuario" : "") . "
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idSolicitud" => $idSolicitud,
            "tipoSolicitud" => $tipoSolicitud,
            "descripcion" => $descripcion,
            "equipo" => $equipo,
            "fechaNecesaria" => $fechaNecesaria
        ] + ($cedulaUsuario !== null ? ["cedulaUsuario" => $cedulaUsuario] : []));
        return $consulta->rowCount() > 0;
    }

    public function aprobarSolicitud(
        int $idSolicitud,
        ?string $respuesta
    ): void {

        $sql = "
            UPDATE solicitud
            SET
                estado = 'Aprobada',
                respuesta = :respuesta
            WHERE idSolicitud = :idSolicitud
            AND estado IN ('En espera','Pendiente')
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idSolicitud" => $idSolicitud,
            "respuesta" => $respuesta
        ]);
    }

    public function rechazarSolicitud(
        int $idSolicitud,
        ?string $respuesta
    ): void {

        $sql = "
            UPDATE solicitud
            SET
                estado = 'Rechazada',
                respuesta = :respuesta
            WHERE idSolicitud = :idSolicitud
            AND estado IN ('En espera','Pendiente')
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idSolicitud" => $idSolicitud,
            "respuesta" => $respuesta
        ]);
    }

    public function cancelarSolicitud(
        int $idSolicitud
    ): void {

        $sql = "
            UPDATE solicitud
            SET
                estado = 'Cancelada'
            WHERE idSolicitud = :idSolicitud
            AND estado IN ('En espera','Pendiente')
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idSolicitud" => $idSolicitud
        ]);
    }

    public function eliminarSolicitud(int $idSolicitud, ?string $cedulaUsuario = null): bool
    {
        $sql = "UPDATE solicitud SET estado = 'Cancelada' WHERE idSolicitud = :idSolicitud AND estado IN ('En espera','Pendiente', 'Aprobada')" . ($cedulaUsuario !== null ? " AND cedulaUsuario = :cedulaUsuario" : "");
        $consulta = $this->conexion->prepare($sql);
        $parametros = ['idSolicitud' => $idSolicitud];
        if ($cedulaUsuario !== null) $parametros['cedulaUsuario'] = $cedulaUsuario;
        $consulta->execute($parametros);
        return $consulta->rowCount() > 0;
    }

    public function actualizarEstado(int $idSolicitud, string $estado, ?string $respuesta = null): bool
    {
        $estadosPermitidos = ['En espera', 'Pendiente', 'Aprobada', 'Rechazada', 'Cancelada', 'Denegada'];
        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }
        $sql = "UPDATE solicitud SET estado = :estado, respuesta = :respuesta WHERE idSolicitud = :idSolicitud";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['idSolicitud' => $idSolicitud, 'estado' => $estado, 'respuesta' => $respuesta]);
        return $consulta->rowCount() > 0;
    }
}
