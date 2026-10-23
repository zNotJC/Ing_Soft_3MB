<?php

class AccesoDatosPrestamos
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerPrestamos(?string $cedulaUsuario = null): array
    {
        $sql = "
            SELECT p.idPrestamo, e.idEquipo, e.nombre AS equipo, e.tipo AS tipoEquipo,
                   u.cedula, u.usuario AS nombre, p.fechaPrestamo, p.fechaDevolucion,
                   p.estado, p.prioridad
            FROM prestamo AS p
            INNER JOIN equipo AS e ON e.idEquipo = p.idEquipo
            INNER JOIN usuario AS u ON u.cedula = p.cedulaUsuario
            " . ($cedulaUsuario !== null ? "WHERE p.cedulaUsuario = :cedulaUsuario" : "") . "
            ORDER BY p.idPrestamo DESC
        ";
        $consulta = $this->conexion->prepare($sql);
        if ($cedulaUsuario !== null) {
            $consulta->bindValue(':cedulaUsuario', $cedulaUsuario);
        }
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEquipos(): array
    {
        return $this->conexion->query(
            "SELECT idEquipo, nombre, tipo, estado FROM equipo WHERE estado = 'Disponible' ORDER BY nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstados(): array
    {
        return array_map(
            static fn(string $nombre): array => ['nombre' => $nombre],
            ['Pendiente', 'Activo', 'Cancelado', 'Devuelto']
        );
    }

    public function obtenerPrioridades(): array
    {
        return array_map(
            static fn(string $nombre): array => ['nombre' => $nombre],
            ['Baja', 'Media', 'Alta']
        );
    }

    public function crearPrestamo(
        int $idEquipo,
        string $cedulaUsuario,
        string $fechaPrestamo,
        string $fechaDevolucion
    ): void {
        $consulta = $this->conexion->prepare("
            INSERT INTO prestamo
                (idEquipo, cedulaUsuario, fechaPrestamo, fechaDevolucion, estado, prioridad)
            VALUES
                (:idEquipo, :cedulaUsuario, :fechaPrestamo, :fechaDevolucion, 'Pendiente', 'Media')
        ");
        $consulta->execute([
            'idEquipo' => $idEquipo,
            'cedulaUsuario' => $cedulaUsuario,
            'fechaPrestamo' => $fechaPrestamo,
            'fechaDevolucion' => $fechaDevolucion
        ]);
    }

    public function aceptarPrestamo(int $idPrestamo): bool
    {
        $consulta = $this->conexion->prepare("
            UPDATE prestamo SET estado = 'Activo'
            WHERE idPrestamo = :idPrestamo AND estado = 'Pendiente'
        ");
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }

    public function modificarPrestamo(
        int $idPrestamo,
        int $idEquipo,
        string $fechaPrestamo,
        string $fechaDevolucion,
        string $prioridad
    ): bool {
        if (!in_array($prioridad, ['Baja', 'Media', 'Alta'], true)) {
            return false;
        }
        $consulta = $this->conexion->prepare("
            UPDATE prestamo
            SET idEquipo = :idEquipo,
                fechaPrestamo = :fechaPrestamo,
                fechaDevolucion = :fechaDevolucion,
                prioridad = :prioridad
            WHERE idPrestamo = :idPrestamo
              AND estado IN ('Pendiente', 'Activo')
        ");
        $consulta->execute([
            'idPrestamo' => $idPrestamo,
            'idEquipo' => $idEquipo,
            'fechaPrestamo' => $fechaPrestamo,
            'fechaDevolucion' => $fechaDevolucion,
            'prioridad' => $prioridad
        ]);
        return $consulta->rowCount() > 0;
    }

    public function cancelarPrestamo(int $idPrestamo): bool
    {
        $consulta = $this->conexion->prepare("
            UPDATE prestamo SET estado = 'Cancelado'
            WHERE idPrestamo = :idPrestamo AND estado IN ('Pendiente', 'Activo')
        ");
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }

    public function devolverPrestamo(int $idPrestamo): bool
    {
        $consulta = $this->conexion->prepare("
            UPDATE prestamo SET estado = 'Devuelto'
            WHERE idPrestamo = :idPrestamo AND estado = 'Activo'
        ");
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }
}
