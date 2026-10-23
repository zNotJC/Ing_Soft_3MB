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

$usuario = $_SESSION["usuario"];
$esAdministrador = (bool) ($_SESSION["administrador"] ?? false);
$esDocente = (bool) ($_SESSION["docente"] ?? false);
$esTecnico = (bool) ($_SESSION["tecnico"] ?? false);

require __DIR__ . "/../vistas/dashboard.php";
