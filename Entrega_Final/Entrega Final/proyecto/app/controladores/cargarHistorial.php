<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$rol = (string) ($_SESSION['rol'] ?? '');
if (empty($_SESSION['cedula']) || !in_array($rol, ['Administrador', 'Docente', 'Tecnico'], true)) {
    header('Location: index.php?error=permisos');
    exit;
}
$esAdministrador = $rol === 'Administrador';
$esDocente = $rol === 'Docente';
$esTecnico = $rol === 'Tecnico';
require __DIR__ . '/../vistas/historial.html';
