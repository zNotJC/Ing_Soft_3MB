<?php

require_once __DIR__ . "/../bootstrap.php";
session_start();

if (
    !isset($_SESSION["cedula"]) ||
    (
        !($_SESSION["administrador"] ?? false) &&
        !($_SESSION["docente"] ?? false)
    )
) {
    header("Location: index.php");
    exit;
}

$esAdministrador = (bool) ($_SESSION["administrador"] ?? false);
$esDocente = (bool) ($_SESSION["docente"] ?? false);

require __DIR__ . "/../views/prestamos.php";
