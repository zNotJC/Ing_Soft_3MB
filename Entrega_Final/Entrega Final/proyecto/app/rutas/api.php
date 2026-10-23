<?php

function dispatchApiResource(string $resource): void
{
    $controllers = [
        'tickets' => ['TicketsController', __DIR__ . '/../controladores/api/TicketsController.php'],
        'solicitudes' => ['SolicitudesController', __DIR__ . '/../controladores/api/SolicitudesController.php'],
        'prestamos' => ['PrestamosController', __DIR__ . '/../controladores/api/PrestamosController.php'],
        'dashboard' => ['DashboardController', __DIR__ . '/../controladores/api/DashboardController.php'],
        'historial' => ['HistorialController', __DIR__ . '/../controladores/api/HistorialController.php'],
        'usuarios' => ['UsuariosController', __DIR__ . '/../controladores/api/UsuariosController.php'],
        'equipos' => ['EquiposController', __DIR__ . '/../controladores/api/EquiposController.php'],
        'catalogos' => ['CatalogosController', __DIR__ . '/../controladores/api/CatalogosController.php']
    ];
    if (!isset($controllers[$resource])) {
        require_once __DIR__ . '/../nucleo/ApiResponse.php';
        ApiResponse::send(404, 'error', 'Recurso API no encontrado.');
    }
    [$class, $file] = $controllers[$resource];
    require_once $file;
    (new $class())->handle();
}
