<?php

require_once __DIR__ . "/../bootstrap.php";

session_start();


if (
    !isset($_SESSION["cedula"]) ||
    (
        !($_SESSION["administrador"] ?? false) &&
        !($_SESSION["docente"] ?? false) &&
        !($_SESSION["tecnico"] ?? false)
    )
) {

    header("Location: index.php");

    exit;
}




$conectorPDO = new ConectorPDO();

$conexion =
    $conectorPDO->establecerConexion();


$accesoDatosTickets =
    new AccesoDatosTickets($conexion);


$lugares =
    $accesoDatosTickets->obtenerLugares();

$prioridades =
    $accesoDatosTickets->obtenerPrioridades();


$esAdministrador =
    $_SESSION["administrador"];

$esDocente =
    $_SESSION["docente"];
$esTecnico = $_SESSION["tecnico"] ?? false;


$conectorPDO->desconectar();


require __DIR__ . "/../views/tickets.php";