<?php
session_start();
if (empty($_SESSION['cedula']) || (empty($_SESSION['administrador']) && empty($_SESSION['tecnico']))) {
    http_response_code(403);
    header('Location: index.php?error=permisos');
    exit;
}
$esAdministrador = !empty($_SESSION['administrador']);
require __DIR__ . '/../app/views/inventario.php';
