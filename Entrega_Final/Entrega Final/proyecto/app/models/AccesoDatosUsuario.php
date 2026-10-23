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
        $sql = "SELECT u.cedula, u.usuario, u.correo, u.activo, CASE WHEN a.cedula IS NOT NULL THEN 'Administrador' WHEN t.cedula IS NOT NULL THEN 'Tecnico' WHEN d.cedula IS NOT NULL THEN 'Docente' ELSE 'Sin rol' END AS rol FROM USUARIO u LEFT JOIN ADMINISTRADOR a ON a.cedula = u.cedula LEFT JOIN DOCENTE d ON d.cedula = u.cedula LEFT JOIN TECNICO t ON t.cedula = u.cedula ORDER BY u.activo DESC, u.usuario";
        $usuarios = $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($usuarios as &$usuario) {
            $usuario['activo'] = (bool) $usuario['activo'];
        }
        unset($usuario);
        return $usuarios;
    }

    public function crearUsuario(string $cedula, string $usuario, string $correo, string $claveHash, string $rol): void
    {
        $tablas = ['Administrador' => 'ADMINISTRADOR', 'Docente' => 'DOCENTE', 'Tecnico' => 'TECNICO'];
        if (!isset($tablas[$rol])) throw new InvalidArgumentException('Rol no válido.');
        $this->conexion->beginTransaction();
        try {
            $consulta = $this->conexion->prepare('INSERT INTO USUARIO (cedula, usuario, correo, claveHash, activo, sesionActiva) VALUES (:cedula, :usuario, :correo, :claveHash, TRUE, FALSE)');
            $consulta->execute(['cedula' => $cedula, 'usuario' => $usuario, 'correo' => $correo, 'claveHash' => $claveHash]);
            $consultaRol = $this->conexion->prepare('INSERT INTO ' . $tablas[$rol] . ' (cedula) VALUES (:cedula)');
            $consultaRol->execute(['cedula' => $cedula]);
            $this->conexion->commit();
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            throw $error;
        }
    }

    public function actualizarUsuario(string $cedulaOriginal, string $cedulaNueva, string $usuario, string $correo, ?string $claveHash = null, ?string $rol = null): bool
    {
        $tablasRoles = ['ADMINISTRADOR', 'DOCENTE', 'TECNICO'];
        $tablasPorRol = ['Administrador' => 'ADMINISTRADOR', 'Docente' => 'DOCENTE', 'Tecnico' => 'TECNICO'];
        if ($rol !== null && !isset($tablasPorRol[$rol])) throw new InvalidArgumentException('Rol no válido.');
        $this->conexion->beginTransaction();
        try {
            $buscar = $this->conexion->prepare('SELECT cedula, usuario, correo, claveHash, activo, sesionActiva FROM USUARIO WHERE cedula = :cedula');
            $buscar->execute(['cedula' => $cedulaOriginal]);
            $actual = $buscar->fetch(PDO::FETCH_ASSOC);
            if (!$actual) {
                $this->conexion->rollBack();
                return false;
            }
            if ($cedulaNueva !== $cedulaOriginal) {
                $insertarUsuario = $this->conexion->prepare('INSERT INTO USUARIO (cedula, usuario, correo, claveHash, activo, sesionActiva) VALUES (:cedula, :usuario, :correo, :claveHash, :activo, FALSE)');
                $insertarUsuario->execute([
                    'cedula' => $cedulaNueva,
                    'usuario' => $usuario,
                    'correo' => $correo,
                    'claveHash' => $claveHash ?? $actual['claveHash'],
                    'activo' => $actual['activo']
                ]);
                $referencias = $this->conexion->prepare("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = 'USUARIO' AND REFERENCED_COLUMN_NAME = 'cedula'");
                $referencias->execute();
                foreach ($referencias->fetchAll(PDO::FETCH_ASSOC) as $referencia) {
                    $tabla = str_replace('`', '``', $referencia['TABLE_NAME']);
                    $columna = str_replace('`', '``', $referencia['COLUMN_NAME']);
                    $actualizarReferencia = $this->conexion->prepare("UPDATE `{$tabla}` SET `{$columna}` = :nueva WHERE `{$columna}` = :anterior");
                    $actualizarReferencia->execute(['nueva' => $cedulaNueva, 'anterior' => $cedulaOriginal]);
                }
                $eliminarAnterior = $this->conexion->prepare('DELETE FROM USUARIO WHERE cedula = :cedula');
                $eliminarAnterior->execute(['cedula' => $cedulaOriginal]);
            }
            $sql = $claveHash !== null && $claveHash !== ''
                ? 'UPDATE USUARIO SET usuario = :usuario, correo = :correo, claveHash = :claveHash WHERE cedula = :cedula'
                : 'UPDATE USUARIO SET usuario = :usuario, correo = :correo WHERE cedula = :cedula';
            $parametros = ['usuario' => $usuario, 'correo' => $correo, 'cedula' => $cedulaNueva];
            if ($claveHash !== null && $claveHash !== '') $parametros['claveHash'] = $claveHash;
            $actualizar = $this->conexion->prepare($sql);
            $actualizar->execute($parametros);
            if ($rol !== null) {
                foreach ($tablasRoles as $tabla) {
                    $eliminarRol = $this->conexion->prepare('DELETE FROM ' . $tabla . ' WHERE cedula = :cedula');
                    $eliminarRol->execute(['cedula' => $cedulaNueva]);
                }
                $insertarRol = $this->conexion->prepare('INSERT INTO ' . $tablasPorRol[$rol] . ' (cedula) VALUES (:cedula)');
                $insertarRol->execute(['cedula' => $cedulaNueva]);
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

    public function activarUsuario(string $cedula): bool
    {
        $consulta = $this->conexion->prepare('UPDATE USUARIO SET activo = TRUE, sesionActiva = FALSE WHERE cedula = :cedula AND activo = FALSE');
        $consulta->execute(['cedula' => $cedula]);
        return $consulta->rowCount() > 0;
    }

}

?>
