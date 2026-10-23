<?php

require_once __DIR__ . '/../../nucleo/ApiController.php';
require_once __DIR__ . '/../../modelos/AccesoDatosHistorial.php';

final class HistorialController extends ApiController
{
    public function handle(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            $this->methodNotAllowed(['GET']);
        }
        $this->requireRole(['Administrador', 'Tecnico', 'Docente']);
        [$connector, $connection] = $this->connect();
        try {
            $model = new AccesoDatosHistorial($connection);
            ApiResponse::send(200, 'success', 'Historial consultado correctamente.', ['historial' => $model->obtenerHistorial()]);
        } catch (Throwable $error) {
            error_log('[API historial] ' . $error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo cargar el historial. Comprueba el esquema de las tablas ticket, solicitud y prestamo.');
        } finally {
            $connector->desconectar();
        }
    }
}
