<?php

require_once __DIR__ . '/../bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function redirigirInicioSesion(string $error): never
{
    header('Location: index.html?error=' . rawurlencode($error));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirigirInicioSesion('acceso');
}

$cedula = trim((string) ($_POST['cedula'] ?? ''));
$contrasena = (string) ($_POST['contrasena'] ?? '');

if (!preg_match('/^[0-9]{8}$/', $cedula) || $contrasena === '') {
    redirigirInicioSesion('credenciales');
}

try {
    $conectorPDO = new ConectorPDO();
    $conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);
    $usuario = $login->autenticar($cedula, $contrasena);

    if ($usuario === null) {
        $conectorPDO->desconectar();
        redirigirInicioSesion('credenciales');
    }

    if ($usuario->noTieneRoles() || $usuario->tieneRolesInconsistentes()) {
        $conectorPDO->desconectar();
        redirigirInicioSesion('sinroles');
    }

    session_regenerate_id(true);

    $_SESSION['cedula'] = $usuario->getCedula();
    $_SESSION['usuario'] = $usuario->getUsuario();
    $_SESSION['correo'] = $usuario->getCorreo();
    $_SESSION['administrador'] = $usuario->esAdministrador();
    $_SESSION['docente'] = $usuario->esDocente();
    $_SESSION['tecnico'] = $usuario->esTecnico();
    $_SESSION['rol'] = $usuario->esAdministrador()
        ? 'Administrador'
        : ($usuario->esTecnico() ? 'Tecnico' : 'Docente');

    $accesoDatosUsuario->activarSesion($usuario->getCedula());
    $accesoDatosUsuario->registrarAcceso($usuario->getCedula(), $_SESSION['rol']);
    $conectorPDO->desconectar();

    header('Location: paginas/dashboard.html');
    exit;
} catch (PDOException $error) {
    error_log('Error de base de datos en el inicio de sesión: ' . $error->getMessage());
    redirigirInicioSesion('esquema');
} catch (Throwable $error) {
    error_log('Error en el inicio de sesión: ' . $error->getMessage());
    redirigirInicioSesion('bd');
}
