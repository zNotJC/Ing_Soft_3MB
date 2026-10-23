<?php

function dispatchApiResource(string $resource): void
{
    $controllers = [
        'tickets' => ['TicketsController', __DIR__ . '/../controladores/TicketsController.php'],
        'solicitudes' => ['SolicitudesController', __DIR__ . '/../controladores/SolicitudesController.php'],
        'prestamos' => ['PrestamosController', __DIR__ . '/../controladores/PrestamosController.php'],
        'dashboard' => ['DashboardController', __DIR__ . '/../controladores/DashboardController.php'],
        'historial' => ['HistorialController', __DIR__ . '/../controladores/HistorialController.php'],
        'usuarios' => ['UsuariosController', __DIR__ . '/../controladores/UsuariosController.php'],
        'equipos' => ['EquiposController', __DIR__ . '/../controladores/EquiposController.php'],
        'catalogos' => ['CatalogosController', __DIR__ . '/../controladores/CatalogosController.php'],
        'session' => ['SesionController', __DIR__ . '/../controladores/SesionController.php']
    ];
    if (!isset($controllers[$resource])) {
        require_once __DIR__ . '/../nucleo/ApiResponse.php';
        ApiResponse::send(404, 'error', 'Recurso API no encontrado.');
    }
    [$class, $file] = $controllers[$resource];
    require_once $file;
    (new $class())->handle();
}
