<?php

require_once __DIR__ . '/../app/bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (isset($_GET['api'])) {
    require_once __DIR__ . '/../app/rutas/api.php';
    dispatchApiResource((string) $_GET['api']);
}

if (($_GET['accion'] ?? '') === 'logout') {
    require __DIR__ . '/../app/controladores/cerrarSesion.php';
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require __DIR__ . '/../app/controladores/procesarLogin.php';
}

if (isset($_GET['error'])) {
    header('Location: index.html?error=' . rawurlencode((string) $_GET['error']));
    exit;
}

if (!empty($_SESSION['cedula'])) {
    header('Location: paginas/dashboard.html');
    exit;
}

header('Location: index.html');
exit;
