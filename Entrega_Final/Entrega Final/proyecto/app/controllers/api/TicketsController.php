<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../models/AccesoDatosTickets.php';

final class TicketsController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $role = $this->requireRole(['Administrador', 'Docente', 'Tecnico']);
        [$connector, $connection] = $this->connect();
        $model = new AccesoDatosTickets($connection);

        try {
            if ($method === 'GET') {
                $data = [
                    'tickets' => $model->obtenerTickets(),
                    'laboratorios' => $model->obtenerLaboratorios(),
                    'prioridades' => $model->obtenerPrioridades()
                ];
                ApiResponse::send(200, 'success', 'Tickets consultados correctamente.', $data);
            }

            $input = ApiResponse::input();
            if ($method === 'POST') {
                if (!in_array($role, ['Docente', 'Tecnico'], true)) {
                    ApiResponse::send(403, 'error', 'El Administrador solo puede consultar tickets.');
                }
                $titulo = $this->requiredString($input, 'titulo', 150);
                $descripcion = $this->requiredString($input, 'descripcion', 5000);
                $fechaLimite = $this->requiredString($input, 'fechaLimite', 10);
                $idLaboratorio = filter_var($input['idLaboratorio'] ?? null, FILTER_VALIDATE_INT);
                $prioridad = (string) ($input['prioridad'] ?? 'Media');
                if (!$idLaboratorio || !in_array($prioridad, ['Baja', 'Media', 'Alta'], true) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaLimite) || $fechaLimite < date('Y-m-d')) {
                    ApiResponse::send(422, 'error', 'Los datos del ticket no son válidos.');
                }
                $model->crearTicket($titulo, $descripcion, $fechaLimite, (string) $_SESSION['cedula'], (int) $idLaboratorio, $prioridad);
                ApiResponse::send(201, 'success', 'Ticket registrado correctamente.', ['idTicket' => (int) $connection->lastInsertId()]);
            }

            if ($method === 'PUT') {
                if ($role !== 'Tecnico') {
                    ApiResponse::send(403, 'error', 'Solo el Técnico puede gestionar incidencias.');
                }
                $id = $this->positiveId('idTicket', $input);
                if (isset($input['accion']) && $input['accion'] === 'estado') {
                    $estado = (string) ($input['estado'] ?? '');
                    if (!in_array($estado, ['En Proceso', 'Resuelto', 'Cancelado', 'Pendiente'], true)) {
                        ApiResponse::send(422, 'error', 'Estado no válido.');
                    }
                    $updated = $model->cambiarEstado($id, $estado);
                } else {
                    $titulo = $this->requiredString($input, 'titulo', 150);
                    $descripcion = $this->requiredString($input, 'descripcion', 5000);
                    $fechaLimite = $this->requiredString($input, 'fechaLimite', 10);
                    $idLaboratorio = filter_var($input['idLaboratorio'] ?? null, FILTER_VALIDATE_INT);
                    $prioridad = (string) ($input['prioridad'] ?? 'Media');
                    if (!$idLaboratorio || !in_array($prioridad, ['Baja', 'Media', 'Alta'], true) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaLimite)) {
                        ApiResponse::send(422, 'error', 'Los datos del ticket no son válidos.');
                    }
                    $updated = $model->modificarTicket($id, $titulo, $descripcion, $fechaLimite, (int) $idLaboratorio, $prioridad);
                }
                if (!$updated) {
                    ApiResponse::send(404, 'error', 'No se encontró el ticket o no se pudo modificar.');
                }
                ApiResponse::send(200, 'success', 'Ticket actualizado correctamente.', ['idTicket' => $id]);
            }

            if ($method === 'DELETE') {
                if ($role !== 'Tecnico') {
                    ApiResponse::send(403, 'error', 'Solo el Técnico puede gestionar incidencias.');
                }
                $id = $this->positiveId('idTicket', $input);
                if (!$model->eliminarTicket($id)) {
                    ApiResponse::send(409, 'error', 'El ticket no existe o ya está cerrado.');
                }
                ApiResponse::send(200, 'success', 'Ticket cancelado correctamente.', ['idTicket' => $id]);
            }

            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (Throwable $error) {
            error_log('[API tickets] ' . $error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación de tickets. Comprueba las columnas de ticket, laboratorio y usuario.');
        } finally {
            $connector->desconectar();
        }
    }
}
