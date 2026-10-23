<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['cedula']) || !in_array((string)($_SESSION['rol']??''), ['Administrador','Tecnico'], true)) { header('Location: index.php?error=permisos'); exit; }
$esAdministrador = (($_SESSION['rol']??'') === 'Administrador');
require __DIR__ . '/../app/vistas/inventario.php';
