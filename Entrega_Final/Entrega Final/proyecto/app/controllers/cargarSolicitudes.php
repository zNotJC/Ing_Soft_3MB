<?php

require_once __DIR__ . "/../bootstrap.php";

session_start();

if (
    !isset($_SESSION["cedula"]) ||
    (
        !($_SESSION["docente"] ?? false) &&
        !($_SESSION["tecnico"] ?? false)
    )
) {

    header("Location: index.php");

    exit;
}

$esAdministrador =
    $_SESSION["administrador"];

$esDocente =
    $_SESSION["docente"];
$esTecnico = $_SESSION["tecnico"] ?? false;

require __DIR__ . "/../views/solicitudes.php";

?>