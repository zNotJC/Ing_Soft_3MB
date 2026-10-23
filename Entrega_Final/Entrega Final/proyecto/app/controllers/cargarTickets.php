<?php

require_once __DIR__ . "/../bootstrap.php";
require_once __DIR__ . '/../models/ConectorPDO.php';
require_once __DIR__ . '/../models/AccesoDatosTickets.php';

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


$laboratorios =
    $accesoDatosTickets->obtenerLaboratorios();

$prioridades =
    $accesoDatosTickets->obtenerPrioridades();


$esAdministrador = (bool) ($_SESSION["administrador"] ?? false);

$esDocente = (bool) ($_SESSION["docente"] ?? false);
$esTecnico = $_SESSION["tecnico"] ?? false;


$conectorPDO->desconectar();


require __DIR__ . "/../views/tickets.php";