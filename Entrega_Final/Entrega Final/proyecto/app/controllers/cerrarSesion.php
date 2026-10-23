<?php

require_once __DIR__ . "/../bootstrap.php";





session_start();




if (isset($_SESSION["cedula"])) {

    $conectorPDO = new ConectorPDO();

    $conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);

    $accesoDatosUsuario->desactivarSesion($_SESSION["cedula"]);

    $conectorPDO->desconectar();
}




$_SESSION = [];




session_destroy();




header("Location: index.php");

exit;

?>