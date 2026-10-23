<?php



class AccesoDatosUsuario
{

    private PDO $conexion;


    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    public function buscarUsuario(string $cedula): ?Usuario
    {
        $sql = "
            SELECT
                u.cedula,
                u.usuario,
                u.correo,
                u.claveHash,
                u.activo,
                u.sesionActiva,

                CASE
                    WHEN a.cedula IS NOT NULL THEN 1
                    ELSE 0
                END AS administrador,

                CASE
                    WHEN d.cedula IS NOT NULL THEN 1
                    ELSE 0
                END AS docente,

                CASE
                    WHEN t.cedula IS NOT NULL THEN 1
                    ELSE 0
                END AS tecnico

            FROM USUARIO AS u

            LEFT JOIN ADMINISTRADOR AS a
                ON a.cedula = u.cedula

            LEFT JOIN DOCENTE AS d
                ON d.cedula = u.cedula

            LEFT JOIN TECNICO AS t
                ON t.cedula = u.cedula

            WHERE u.cedula = :cedula
        ";


        $consulta = $this->conexion->prepare($sql);


        $consulta->execute([
            "cedula" => $cedula
        ]);


        $datos = $consulta->fetch(PDO::FETCH_ASSOC);


        if ($datos === false) {
            return null;
        }


        return new Usuario(
            $datos["cedula"],
            $datos["usuario"],
            $datos["correo"],
            $datos["claveHash"],
            (bool) $datos["activo"],
            (bool) $datos["administrador"],
            (bool) $datos["docente"],
            (bool) $datos["tecnico"]
        );
    }

    public function activarSesion(string $cedula): void
    {
        $sql = "
            UPDATE USUARIO
            SET sesionActiva = TRUE
            WHERE cedula = :cedula
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);
    }


    public function desactivarSesion(string $cedula): void
    {
        $sql = "
            UPDATE USUARIO
            SET sesionActiva = FALSE
            WHERE cedula = :cedula
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            "cedula" => $cedula
        ]);
    }

    
   public function registrarAcceso(
        string $cedula,
        string $rol
    ): void
    {
        $sql = "
            INSERT INTO acceso_sistema
            (
                cedulaUsuario,
                rol
            )
            VALUES
            (
                :cedulaUsuario,
                :rol
            )
        ";

        $consulta =
            $this->conexion->prepare($sql);

        $consulta->execute([
            "cedulaUsuario" => $cedula,
            "rol" => $rol
        ]);
    }

    public function obtenerUltimosAccesos(): array
    {
        $sql = "
            SELECT
                a.fechaHora,
                u.usuario,
                a.rol
            FROM acceso_sistema AS a
            INNER JOIN usuario AS u
                ON u.cedula = a.cedulaUsuario
            ORDER BY a.fechaHora DESC
            LIMIT 10
        ";

        $consulta =
            $this->conexion->query($sql);

        return $consulta->fetchAll();
    } 

    public function listarUsuarios(): array
    {
        $sql = "SELECT u.cedula, u.usuario, u.correo, u.activo, CASE WHEN a.cedula IS NOT NULL THEN 'Administrador' WHEN t.cedula IS NOT NULL THEN 'Tecnico' WHEN d.cedula IS NOT NULL THEN 'Docente' ELSE 'Sin rol' END AS rol FROM USUARIO u LEFT JOIN ADMINISTRADOR a ON a.cedula = u.cedula LEFT JOIN DOCENTE d ON d.cedula = u.cedula LEFT JOIN TECNICO t ON t.cedula = u.cedula ORDER BY u.usuario";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearUsuario(string $cedula, string $usuario, string $correo, string $claveHash, string $rol): void
    {
        $this->conexion->beginTransaction();
        try {
            $consulta = $this->conexion->prepare('INSERT INTO USUARIO (cedula, usuario, correo, claveHash, activo) VALUES (:cedula, :usuario, :correo, :claveHash, TRUE)');
            $consulta->execute(['cedula' => $cedula, 'usuario' => $usuario, 'correo' => $correo, 'claveHash' => $claveHash]);
            $tablas = ['Administrador' => 'ADMINISTRADOR', 'Docente' => 'DOCENTE', 'Tecnico' => 'TECNICO'];
            if (!isset($tablas[$rol])) {
                throw new InvalidArgumentException('Rol no válido.');
            }
            $consultaRol = $this->conexion->prepare('INSERT INTO ' . $tablas[$rol] . ' (cedula) VALUES (:cedula)');
            $consultaRol->execute(['cedula' => $cedula]);
            $this->conexion->commit();
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $error;
        }
    }

    public function actualizarUsuario(string $cedula, string $usuario, string $correo, ?string $claveHash = null, ?string $rol = null): bool
    {
        $this->conexion->beginTransaction();
        try {
            if ($claveHash !== null && $claveHash !== '') {
                $sql = 'UPDATE USUARIO SET usuario = :usuario, correo = :correo, claveHash = :claveHash WHERE cedula = :cedula';
                $parametros = ['cedula' => $cedula, 'usuario' => $usuario, 'correo' => $correo, 'claveHash' => $claveHash];
            } else {
                $sql = 'UPDATE USUARIO SET usuario = :usuario, correo = :correo WHERE cedula = :cedula';
                $parametros = ['cedula' => $cedula, 'usuario' => $usuario, 'correo' => $correo];
            }
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute($parametros);
            if ($rol !== null) {
                $tablas = ['Administrador' => 'ADMINISTRADOR', 'Docente' => 'DOCENTE', 'Tecnico' => 'TECNICO'];
                if (!isset($tablas[$rol])) throw new InvalidArgumentException('Rol no válido.');
                foreach (array_values($tablas) as $tabla) {
                    $eliminar = $this->conexion->prepare('DELETE FROM ' . $tabla . ' WHERE cedula = :cedula');
                    $eliminar->execute(['cedula' => $cedula]);
                }
                $insertar = $this->conexion->prepare('INSERT INTO ' . $tablas[$rol] . ' (cedula) VALUES (:cedula)');
                $insertar->execute(['cedula' => $cedula]);
            }
            $this->conexion->commit();
            return true;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $error;
        }
    }

    public function desactivarUsuario(string $cedula): bool
    {
        $consulta = $this->conexion->prepare('UPDATE USUARIO SET activo = FALSE, sesionActiva = FALSE WHERE cedula = :cedula AND activo = TRUE');
        $consulta->execute(['cedula' => $cedula]);
        return $consulta->rowCount() > 0;
    }

}

?>
