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
                    'equipos' => $model->obtenerEquipos(),
                    'prioridades' => $model->obtenerPrioridades()
                ];
                ApiResponse::send(200, 'success', 'Tickets consultados correctamente.', $data);
            }

            $input = ApiResponse::input();
            if ($method === 'POST') {
                if ($role !== 'Docente') {
                    ApiResponse::send(403, 'error', 'El Administrador solo puede consultar tickets.');
                }
                $titulo = $this->requiredString($input, 'titulo', 150);
                $descripcion = $this->requiredString($input, 'descripcion', 5000);
                $fechaLimite = null;
                $idLaboratorio = filter_var($input['idLaboratorio'] ?? null, FILTER_VALIDATE_INT);
                $idEquipo = filter_var($input['idEquipo'] ?? null, FILTER_VALIDATE_INT);
                $idEquipo = $idEquipo ? (int) $idEquipo : null;
                $prioridad = '-';
                if (!$idLaboratorio) {
                    ApiResponse::send(422, 'error', 'Los datos del ticket no son válidos.');
                }
                $model->crearTicket($titulo, $descripcion, $fechaLimite, (string) $_SESSION['cedula'], (int) $idLaboratorio, $prioridad, $idEquipo);
                ApiResponse::send(201, 'success', 'Ticket registrado correctamente.', ['idTicket' => (int) $connection->lastInsertId()]);
            }

            if ($method === 'PUT') {
                if ($role !== 'Tecnico') {
                    ApiResponse::send(403, 'error', 'Solo el Técnico puede gestionar incidencias.');
                }
                $id = $this->positiveId('idTicket', $input);
                if (isset($input['accion'])) {
                    $accion = (string) $input['accion'];
                    if (in_array($accion, ['iniciar', 'esperar'], true)) {
                        $prioridad = (string) ($input['prioridad'] ?? '');
                        $fechaLimite = (string) ($input['fechaLimite'] ?? '');
                        if (!in_array($prioridad, ['Baja', 'Media', 'Alta'], true) ||
                            !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $fechaLimite) ||
                            $fechaLimite < date('Y-m-d')) {
                            ApiResponse::send(422, 'error', 'Indica una prioridad y fecha límite válidas.');
                        }
                        $updated = $model->revisarTicket($id, $prioridad, $fechaLimite);
                        if ($updated && $accion === 'iniciar') {
                            $updated = $model->cambiarEstado($id, 'En Proceso');
                        }
                    } elseif ($accion === 'completar') {
                        $updated = $model->cambiarEstado($id, 'Resuelto');
                    } elseif ($accion === 'cancelar') {
                        $updated = $model->cambiarEstado($id, 'Cancelado');
                    } else {
                        ApiResponse::send(422, 'error', 'Acción no válida.');
                    }
                } else {
                    ApiResponse::send(403, 'error', 'Los tickets solo se gestionan mediante las acciones de revisión, inicio, resolución o cancelación.');
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
