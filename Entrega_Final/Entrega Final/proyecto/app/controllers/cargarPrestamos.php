<?php

require_once __DIR__ . "/../bootstrap.php";
session_start();

if (
    !isset($_SESSION["cedula"]) ||
    (
        !($_SESSION["administrador"] ?? false)
    )
) {
    header("Location: index.php");
    exit;
}

$esAdministrador = (bool) ($_SESSION["administrador"] ?? false);
$esDocente = false;

require __DIR__ . "/../views/prestamos.php";
