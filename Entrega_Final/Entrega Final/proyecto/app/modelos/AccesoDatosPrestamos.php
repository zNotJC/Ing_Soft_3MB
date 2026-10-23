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
            ['En espera', 'Pendiente', 'Aceptado', 'Activo', 'Cancelado', 'Devuelto', 'Denegado']
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
                (idEquipo, idLaboratorio, cedulaUsuario, fechaPrestamo, fechaDevolucion, estado, prioridad)
            SELECT e.idEquipo, e.idLaboratorio, :cedulaUsuario, :fechaPrestamo, :fechaDevolucion, 'En espera', '-'
            FROM equipo e
            WHERE e.idEquipo = :idEquipo AND e.estado = 'Disponible'
        ");
        $consulta->execute([
            'idEquipo' => $idEquipo,
            'cedulaUsuario' => $cedulaUsuario,
            'fechaPrestamo' => $fechaPrestamo,
            'fechaDevolucion' => $fechaDevolucion
        ]);
        if ($consulta->rowCount() !== 1) {
            throw new RuntimeException('El equipo no existe o no está disponible.');
        }
    }

    public function revisarPrestamo(int $idPrestamo, string $prioridad): bool
    {
        if (!in_array($prioridad, ['Baja', 'Media', 'Alta'], true)) return false;
        $consulta = $this->conexion->prepare("UPDATE prestamo SET prioridad = :prioridad WHERE idPrestamo = :idPrestamo AND estado IN ('En espera','Pendiente') AND prioridad = '-'");
        $consulta->execute(['idPrestamo' => $idPrestamo, 'prioridad' => $prioridad]);
        return $consulta->rowCount() > 0;
    }

    public function denegarPrestamo(int $idPrestamo): bool
    {
        $consulta = $this->conexion->prepare("UPDATE prestamo SET estado = 'Denegado' WHERE idPrestamo = :idPrestamo AND estado IN ('En espera','Pendiente')");
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }

    public function aceptarPrestamo(int $idPrestamo): bool
    {
        $this->conexion->beginTransaction();
        try {
            $consulta = $this->conexion->prepare("
                SELECT idEquipo FROM prestamo
                WHERE idPrestamo = :idPrestamo AND estado IN ('En espera','Pendiente')
                  AND prioridad IN ('Baja','Media','Alta') FOR UPDATE
            ");
            $consulta->execute(['idPrestamo' => $idPrestamo]);
            $prestamo = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$prestamo) { $this->conexion->rollBack(); return false; }
            $equipo = $this->conexion->prepare("UPDATE equipo SET estado = 'En uso' WHERE idEquipo = :idEquipo AND estado = 'Disponible'");
            $equipo->execute(['idEquipo' => $prestamo['idEquipo']]);
            if ($equipo->rowCount() !== 1) { $this->conexion->rollBack(); return false; }
            $actualizar = $this->conexion->prepare("UPDATE prestamo SET estado = 'Aceptado' WHERE idPrestamo = :idPrestamo AND estado IN ('En espera','Pendiente')");
            $actualizar->execute(['idPrestamo' => $idPrestamo]);
            if ($actualizar->rowCount() !== 1) { $this->conexion->rollBack(); return false; }
            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $e;
        }
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
              AND estado IN ('En espera','Pendiente')
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
            WHERE idPrestamo = :idPrestamo AND estado IN ('En espera','Pendiente')
        ");
        $consulta->execute(['idPrestamo' => $idPrestamo]);
        return $consulta->rowCount() > 0;
    }

    public function devolverPrestamo(int $idPrestamo): bool
    {
        $this->conexion->beginTransaction();
        try {
            $consulta = $this->conexion->prepare("SELECT idEquipo FROM prestamo WHERE idPrestamo = :idPrestamo AND estado IN ('Aceptado','Activo') FOR UPDATE");
            $consulta->execute(['idPrestamo' => $idPrestamo]);
            $prestamo = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$prestamo) { $this->conexion->rollBack(); return false; }
            $actualizar = $this->conexion->prepare("UPDATE prestamo SET estado = 'Devuelto' WHERE idPrestamo = :idPrestamo AND estado IN ('Aceptado','Activo')");
            $actualizar->execute(['idPrestamo' => $idPrestamo]);
            $equipo = $this->conexion->prepare("UPDATE equipo SET estado = 'Disponible' WHERE idEquipo = :idEquipo AND estado = 'En uso'");
            $equipo->execute(['idEquipo' => $prestamo['idEquipo']]);
            $this->conexion->commit();
            return $actualizar->rowCount() > 0;
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $e;
        }
    }
}
