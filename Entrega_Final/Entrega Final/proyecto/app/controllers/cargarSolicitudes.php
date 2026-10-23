<?php

require_once __DIR__ . "/../bootstrap.php";

session_start();

if (!isset($_SESSION["cedula"]) ||
    (!($_SESSION["administrador"] ?? false) &&
     !($_SESSION["docente"] ?? false) &&
     !($_SESSION["tecnico"] ?? false))) {
    header("Location: index.php");
    exit;
}

$esAdministrador =
    $_SESSION["administrador"] ?? false;

$esDocente =
    $_SESSION["docente"] ?? false;
$esTecnico = $_SESSION["tecnico"] ?? false;

require __DIR__ . "/../views/solicitudes.php";

?>