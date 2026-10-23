<?php

function dispatchApiResource(string $resource): void
{
    $controllers = [
        'tickets' => ['TicketsController', __DIR__ . '/../controllers/api/TicketsController.php'],
        'solicitudes' => ['SolicitudesController', __DIR__ . '/../controllers/api/SolicitudesController.php'],
        'prestamos' => ['PrestamosController', __DIR__ . '/../controllers/api/PrestamosController.php'],
        'dashboard' => ['DashboardController', __DIR__ . '/../controllers/api/DashboardController.php'],
        'historial' => ['HistorialController', __DIR__ . '/../controllers/api/HistorialController.php'],
        'usuarios' => ['UsuariosController', __DIR__ . '/../controllers/api/UsuariosController.php'],
        'equipos' => ['EquiposController', __DIR__ . '/../controllers/api/EquiposController.php'],
        'catalogos' => ['CatalogosController', __DIR__ . '/../controllers/api/CatalogosController.php']
    ];
    if (!isset($controllers[$resource])) {
        require_once __DIR__ . '/../core/ApiResponse.php';
        ApiResponse::send(404, 'error', 'Recurso API no encontrado.');
    }
    [$class, $file] = $controllers[$resource];
    require_once $file;
    (new $class())->handle();
}
