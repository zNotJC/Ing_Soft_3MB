<?php
require_once __DIR__ . '/../bootstrap.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$cedula = (string) ($_SESSION['cedula'] ?? '');
if ($cedula !== '') {
    try {
        $conectorPDO = new ConectorPDO();
        $conexion = $conectorPDO->establecerConexion();
        $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
        $accesoDatosUsuario->desactivarSesion($cedula);
        $conectorPDO->desconectar();
    } catch (Throwable $error) {
        error_log('No se pudo actualizar el estado de sesión al cerrar: ' . $error->getMessage());
    }
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
}
session_destroy();
header('Location: index.html');
exit;
