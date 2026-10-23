<?php
session_start();
if (empty($_SESSION['cedula']) || empty($_SESSION['administrador'])) {
    http_response_code(403);
    header('Location: index.php?error=permisos');
    exit;
}
require __DIR__ . '/../app/views/usuarios.html';
