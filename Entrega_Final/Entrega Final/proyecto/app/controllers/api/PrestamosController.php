<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../models/AccesoDatosPrestamos.php';

final class PrestamosController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $role = $this->requireRole(['Administrador']);
        [$connector, $connection] = $this->connect();
        $model = new AccesoDatosPrestamos($connection);

        try {
            if ($method === 'GET') {
                ApiResponse::send(200, 'success', 'Préstamos consultados correctamente.', [
                    'prestamos' => $model->obtenerPrestamos(),
                    'equipos' => $model->obtenerEquipos(),
                    'estados' => $model->obtenerEstados()
                ]);
            }
            if ($role !== 'Administrador') {
                ApiResponse::send(403, 'error', 'No tienes permisos para gestionar préstamos.');
            }
            $input = ApiResponse::input();
            if ($method === 'POST') {
                $idEquipo = filter_var($input['idEquipo'] ?? null, FILTER_VALIDATE_INT);
                $cedula = (string) $_SESSION['cedula'];
                $fechaPrestamo = (string) ($input['fechaPrestamo'] ?? '');
                $fechaDevolucion = (string) ($input['fechaDevolucion'] ?? '');
                if (!$idEquipo || $cedula === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPrestamo) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDevolucion) || $fechaDevolucion < $fechaPrestamo) {
                    ApiResponse::send(422, 'error', 'Los datos del préstamo no son válidos.');
                }
                $model->crearPrestamo((int) $idEquipo, $cedula, $fechaPrestamo, $fechaDevolucion);
                ApiResponse::send(201, 'success', 'Préstamo registrado correctamente.', ['idPrestamo' => (int) $connection->lastInsertId()]);
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
                    if (!$idEquipo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPrestamo) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDevolucion) || $fechaDevolucion < $fechaPrestamo) {
                        ApiResponse::send(422, 'error', 'Los datos del préstamo no son válidos.');
                    }
                    $updated = $model->modificarPrestamo($id, (int) $idEquipo, $fechaPrestamo, $fechaDevolucion);
                }
                if ($updated === false) {
                    ApiResponse::send(409, 'error', 'El préstamo no existe o no admite esa modificación.');
                }
                ApiResponse::send(200, 'success', 'Préstamo actualizado correctamente.', ['idPrestamo' => $id]);
            }
            if ($method === 'DELETE') {
                $id = $this->positiveId('idPrestamo', $input);
                if (!$model->eliminarPrestamo($id)) {
                    ApiResponse::send(409, 'error', 'El préstamo no existe o no se puede cancelar.');
                }
                ApiResponse::send(200, 'success', 'Préstamo cancelado correctamente.', ['idPrestamo' => $id]);
            }
            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación de préstamos.');
        } finally {
            $connector->desconectar();
        }
    }
}
