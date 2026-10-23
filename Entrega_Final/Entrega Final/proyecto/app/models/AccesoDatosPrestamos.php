<?php

class AccesoDatosPrestamos
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerPrestamos(): array
    {
        $sql = "
            SELECT
                p.idPrestamo,
                e.idEquipo,
                e.nombre AS equipo,
                e.numeroSerie,
                u.cedula,
                u.usuario AS nombre,
                p.fechaPrestamo,
                p.fechaDevolucion,
                ep.idEstado,
                ep.nombre AS estado

            FROM prestamo AS p

            INNER JOIN equipo AS e
                ON e.idEquipo = p.idEquipo

            INNER JOIN usuario AS u
                ON u.cedula = p.cedulaUsuario

            INNER JOIN estado_prestamo AS ep
                ON ep.idEstado = p.idEstado

            ORDER BY p.idPrestamo DESC
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }

    public function obtenerEquipos(): array
    {
        $sql = "
            SELECT
                idEquipo,
                nombre,
                numeroSerie
            FROM equipo
            ORDER BY nombre
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }

    public function obtenerEstados(): array
    {
        $sql = "
            SELECT
                idEstado,
                nombre
            FROM estado_prestamo
            ORDER BY idEstado
        ";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll();
    }

    public function obtenerPrestamo(
        int $idPrestamo
    ): ?array {

        $sql = "
            SELECT
                idPrestamo,
                idEquipo,
                cedulaUsuario,
                fechaPrestamo,
                fechaDevolucion,
                idEstado
            FROM prestamo
            WHERE idPrestamo = :idPrestamo
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "idPrestamo" => $idPrestamo
        ]);

        $prestamo =
            $consulta->fetch();

        return $prestamo ?: null;
    }

    public function crearPrestamo(
        int $idEquipo,
        string $cedulaUsuario,
        string $fechaPrestamo,
        string $fechaDevolucion
    ): void {

        $sql = "
            INSERT INTO prestamo
            (
                idEquipo,
                cedulaUsuario,
                fechaPrestamo,
                fechaDevolucion,
                idEstado
            )
            VALUES
            (
                :idEquipo,
                :cedulaUsuario,
                :fechaPrestamo,
                :fechaDevolucion,

                (
                    SELECT idEstado
                    FROM estado_prestamo
                    WHERE nombre = 'Pendiente'
                )
            )
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idEquipo" => $idEquipo,
            "cedulaUsuario" => $cedulaUsuario,
            "fechaPrestamo" => $fechaPrestamo,
            "fechaDevolucion" => $fechaDevolucion
        ]);
    }

    public function aceptarPrestamo(
        int $idPrestamo
    ): bool {

        $sql = "
            UPDATE prestamo

            SET idEstado = (
                SELECT idEstado
                FROM estado_prestamo
                WHERE nombre = 'Activo'
            )

            WHERE idPrestamo = :idPrestamo

            AND idEstado = (
                SELECT idEstado
                FROM estado_prestamo
                WHERE nombre = 'Pendiente'
            )
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idPrestamo" => $idPrestamo
        ]);
        return $consulta->rowCount() > 0;
    }

    public function modificarPrestamo(
        int $idPrestamo,
        int $idEquipo,
        string $fechaPrestamo,
        string $fechaDevolucion
    ): bool {

        $sql = "
            UPDATE prestamo

            SET
                idEquipo = :idEquipo,
                fechaPrestamo = :fechaPrestamo,
                fechaDevolucion = :fechaDevolucion

            WHERE idPrestamo = :idPrestamo

            AND idEstado IN (
                SELECT idEstado
                FROM estado_prestamo
                WHERE nombre IN ('Pendiente', 'Activo')
            )
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idPrestamo" => $idPrestamo,
            "idEquipo" => $idEquipo,
            "fechaPrestamo" => $fechaPrestamo,
            "fechaDevolucion" => $fechaDevolucion
        ]);
        return $consulta->rowCount() > 0;
    }

    public function cancelarPrestamo(
        int $idPrestamo
    ): bool {

        $sql = "
            UPDATE prestamo

            SET idEstado = (
                SELECT idEstado
                FROM estado_prestamo
                WHERE nombre = 'Cancelado'
            )

            WHERE idPrestamo = :idPrestamo

            AND idEstado IN (
                SELECT idEstado
                FROM estado_prestamo
                WHERE nombre IN ('Pendiente', 'Activo')
            )
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "idPrestamo" => $idPrestamo
        ]);
        return $consulta->rowCount() > 0;
    }

    public function eliminarPrestamo(int $idPrestamo): bool
    {
        $sql = "UPDATE prestamo SET idEstado = (SELECT idEstado FROM estado_prestamo WHERE nombre = 'Cancelado') WHERE idPrestamo = :idPrestamo AND idEstado IN (SELECT idEstado FROM estado_prestamo WHERE nombre IN ('Pendiente', 'Activo'))";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }

    public function devolverPrestamo(int $idPrestamo): bool
    {
        $sql = "UPDATE prestamo SET idEstado = (SELECT idEstado FROM estado_prestamo WHERE nombre = 'Devuelto') WHERE idPrestamo = :idPrestamo AND idEstado = (SELECT idEstado FROM estado_prestamo WHERE nombre = 'Activo')";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }
}
