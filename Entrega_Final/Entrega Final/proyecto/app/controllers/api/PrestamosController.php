<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../models/AccesoDatosPrestamos.php';

final class PrestamosController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $role = $this->requireRole(['Administrador', 'Docente', 'Tecnico']);
        [$connector, $connection] = $this->connect();

        try {
            $model = new AccesoDatosPrestamos($connection);
            if ($method === 'GET') {
                $cedula = $role === 'Docente' ? (string) $_SESSION['cedula'] : null;
                ApiResponse::send(200, 'success', 'Préstamos consultados correctamente.', [
                    'prestamos' => $model->obtenerPrestamos($cedula),
                    'equipos' => $model->obtenerEquipos(),
                    'estados' => $model->obtenerEstados(),
                    'prioridades' => $model->obtenerPrioridades()
                ]);
            }

            $input = ApiResponse::input();
            if ($method === 'POST') {
                if ($role !== 'Docente') {
                    ApiResponse::send(403, 'error', 'El Administrador gestiona los préstamos existentes, pero no los registra.');
                }
                $idEquipo = filter_var($input['idEquipo'] ?? null, FILTER_VALIDATE_INT);
                $fechaPrestamo = (string) ($input['fechaPrestamo'] ?? '');
                $fechaDevolucion = (string) ($input['fechaDevolucion'] ?? '');
                if (!$idEquipo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPrestamo) ||
                    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDevolucion) || $fechaDevolucion < $fechaPrestamo) {
                    ApiResponse::send(422, 'error', 'Los datos del préstamo no son válidos.');
                }
                $model->crearPrestamo((int) $idEquipo, (string) $_SESSION['cedula'], $fechaPrestamo, $fechaDevolucion);
                ApiResponse::send(201, 'success', 'Préstamo solicitado correctamente.', [
                    'idPrestamo' => (int) $connection->lastInsertId()
                ]);
            }

            if ($role !== 'Tecnico') {
                ApiResponse::send(403, 'error', 'El Administrador solo consulta préstamos y el Docente solo crea los propios.');
            }

            if ($method === 'PUT') {
                $id = $this->positiveId('idPrestamo', $input);
                $accion = (string) ($input['accion'] ?? 'modificar');
                if ($accion === 'aceptar') {
                    $updated = $model->aceptarPrestamo($id);
                } elseif ($accion === 'devolver') {
                    $updated = $model->devolverPrestamo($id);
                } elseif ($accion === 'cancelar') {
                    $updated = $model->cancelarPrestamo($id);
                } else {
                    $idEquipo = filter_var($input['idEquipo'] ?? null, FILTER_VALIDATE_INT);
                    $fechaPrestamo = (string) ($input['fechaPrestamo'] ?? '');
                    $fechaDevolucion = (string) ($input['fechaDevolucion'] ?? '');
                    $prioridad = (string) ($input['prioridad'] ?? 'Media');
                    if (!$idEquipo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPrestamo) ||
                        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDevolucion) ||
                        $fechaDevolucion < $fechaPrestamo ||
                        !in_array($prioridad, ['Baja', 'Media', 'Alta'], true)) {
                        ApiResponse::send(422, 'error', 'Los datos del préstamo no son válidos.');
                    }
                    $updated = $model->modificarPrestamo($id, (int) $idEquipo, $fechaPrestamo, $fechaDevolucion, $prioridad);
                }
                if (!$updated) {
                    ApiResponse::send(409, 'error', 'El préstamo no existe o no admite esa operación.');
                }
                ApiResponse::send(200, 'success', 'Préstamo actualizado correctamente.', ['idPrestamo' => $id]);
            }

            if ($method === 'DELETE') {
                $id = $this->positiveId('idPrestamo', $input);
                if (!$model->cancelarPrestamo($id)) {
                    ApiResponse::send(409, 'error', 'El préstamo no existe o no puede cancelarse.');
                }
                ApiResponse::send(200, 'success', 'Préstamo cancelado correctamente.', ['idPrestamo' => $id]);
            }

            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (Throwable $error) {
            error_log('[API prestamos] ' . $error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación de préstamos. Comprueba las columnas de prestamo, equipo y usuario.');
        } finally {
            $connector->desconectar();
        }
    }
}
