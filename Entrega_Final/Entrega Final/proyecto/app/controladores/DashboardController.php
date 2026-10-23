<?php

require_once __DIR__ . '/../nucleo/ApiController.php';
require_once __DIR__ . '/../modelos/AccesoDatosDashboard.php';
require_once __DIR__ . '/../modelos/AccesoDatosUsuario.php';

final class DashboardController extends ApiController
{
    public function handle(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            $this->methodNotAllowed(['GET']);
        }
        $this->requireRole(['Administrador', 'Tecnico']);
        [$connector, $connection] = $this->connect();
        try {
            $dashboard = new AccesoDatosDashboard($connection);
            $usuarios = new AccesoDatosUsuario($connection);
            $data = [
                'ticketsPendientes' => $dashboard->obtenerTicketsPendientes(),
                'ticketsResueltos' => $dashboard->obtenerTicketsResueltos(),
                'solicitudesActivas' => $dashboard->obtenerSolicitudesActivas(),
                'prestamosActivos' => $dashboard->obtenerPrestamosActivos(),
                'equiposRegistrados' => $dashboard->obtenerEquiposRegistrados(),
                'ultimosAccesos' => $usuarios->obtenerUltimosAccesos()
            ];
            ApiResponse::send(200, 'success', 'Dashboard consultado correctamente.', $data);
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo cargar el dashboard.');
        } finally {
            $connector->desconectar();
        }
    }
}
