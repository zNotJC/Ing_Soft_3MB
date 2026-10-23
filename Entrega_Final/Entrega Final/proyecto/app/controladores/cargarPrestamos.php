<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../modelos/ConectorPDO.php';
require_once __DIR__ . '/../modelos/AccesoDatosPrestamos.php';
session_start();

if (!isset($_SESSION['cedula']) ||
    (!($_SESSION['administrador'] ?? false) && !($_SESSION['docente'] ?? false) && !($_SESSION['tecnico'] ?? false))) {
    header('Location: index.php');
    exit;
}

$esAdministrador = (bool) ($_SESSION['administrador'] ?? false);
$esDocente = (bool) ($_SESSION['docente'] ?? false);
$esTecnico = (bool) ($_SESSION['tecnico'] ?? false);

require __DIR__ . '/../vista/prestamos.php';
