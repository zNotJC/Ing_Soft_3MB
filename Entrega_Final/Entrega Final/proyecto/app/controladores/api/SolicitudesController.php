<?php

require_once __DIR__ . '/../../nucleo/ApiController.php';
require_once __DIR__ . '/../../modelos/AccesoDatosSolicitudes.php';

final class SolicitudesController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $role = $this->requireRole(['Administrador', 'Docente', 'Tecnico']);
        [$connector, $connection] = $this->connect();
        $model = new AccesoDatosSolicitudes($connection);

        try {
            if ($method === 'GET') {
                ApiResponse::send(200, 'success', 'Solicitudes consultadas correctamente.', $model->obtenerSolicitudes($role === 'Docente' ? (string) $_SESSION['cedula'] : null));
            }
            $input = ApiResponse::input();
            if ($method === 'POST') {
                if ($role !== 'Docente') {
                    ApiResponse::send(403, 'error', 'Solo el Docente puede registrar solicitudes.');
                }
                $tipo = $this->requiredString($input, 'tipoSolicitud', 30);
                $descripcion = $this->requiredString($input, 'descripcion', 500);
                $cantidad = filter_var($input['cantidad'] ?? 1, FILTER_VALIDATE_INT);
                $equipo = trim((string) ($input['equipo'] ?? '')) ?: null;
                $fechaSolicitud = (string) ($input['fechaSolicitud'] ?? date('Y-m-d'));
                $fechaNecesaria = trim((string) ($input['fechaNecesaria'] ?? '')) ?: null;
                $tipos = ['Laboratorio', 'Reparación'];
                if (!in_array($tipo, $tipos, true) || !$cantidad || $cantidad < 1 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSolicitud)) {
                    ApiResponse::send(422, 'error', 'Los datos de la solicitud no son válidos.');
                }
                $model->crearSolicitud((string) $_SESSION['cedula'], $tipo, $descripcion, (int) $cantidad, $equipo, $fechaSolicitud, $fechaNecesaria);
                ApiResponse::send(201, 'success', 'Solicitud registrada correctamente.', ['idSolicitud' => (int) $connection->lastInsertId()]);
            }
            if ($method === 'PUT') {
                $id = $this->positiveId('idSolicitud', $input);
                if ($role === 'Tecnico') {
                    $estado = (string) ($input['estado'] ?? '');
                    if (!in_array($estado, ['Aprobada', 'Rechazada', 'Cancelada'], true)) {
                        ApiResponse::send(422, 'error', 'Estado de solicitud no válido.');
                    }
                    $respuesta = trim((string) ($input['respuesta'] ?? '')) ?: null;
                    $updated = $model->actualizarEstado($id, $estado, $respuesta);
                } elseif ($role === 'Docente') {
                    $tipo = $this->requiredString($input, 'tipoSolicitud', 30);
                    $descripcion = $this->requiredString($input, 'descripcion', 500);
                    $cantidad = filter_var($input['cantidad'] ?? null, FILTER_VALIDATE_INT);
                    $equipo = trim((string) ($input['equipo'] ?? '')) ?: null;
                    $fechaNecesaria = trim((string) ($input['fechaNecesaria'] ?? '')) ?: null;
                    if (!$cantidad || $cantidad < 1) {
                        ApiResponse::send(422, 'error', 'La cantidad debe ser mayor que cero.');
                    }
                    $updated = $model->modificarSolicitud($id, $tipo, $descripcion, (int) $cantidad, $equipo, $fechaNecesaria, (string) $_SESSION['cedula']);
                } else {
                    ApiResponse::send(403, 'error', 'El Administrador no gestiona solicitudes.');
                }
                if (!$updated) {
                    ApiResponse::send(409, 'error', 'La solicitud no existe o no admite esa modificación.');
                }
                ApiResponse::send(200, 'success', 'Solicitud actualizada correctamente.', ['idSolicitud' => $id]);
            }
            if ($method === 'DELETE') {
                if ($role !== 'Docente' && $role !== 'Tecnico') {
                    ApiResponse::send(403, 'error', 'El Administrador no gestiona solicitudes.');
                }
                $id = $this->positiveId('idSolicitud', $input);
                if (!$model->eliminarSolicitud($id, $role === 'Docente' ? (string) $_SESSION['cedula'] : null)) {
                    ApiResponse::send(409, 'error', 'La solicitud no existe o no se puede cancelar.');
                }
                ApiResponse::send(200, 'success', 'Solicitud cancelada correctamente.', ['idSolicitud' => $id]);
            }
            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación de solicitudes.');
        } finally {
            $connector->desconectar();
        }
    }
}
