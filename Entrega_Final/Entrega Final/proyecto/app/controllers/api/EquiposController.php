<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../models/AccesoDatosEquipos.php';

final class EquiposController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $role = $this->requireRole(['Administrador', 'Tecnico']);
        [$connector, $connection] = $this->connect();
        try {
            $model = new AccesoDatosEquipos($connection);
            if ($method === 'GET') {
                ApiResponse::send(200, 'success', 'Inventario consultado correctamente.', ['equipos' => $model->listar(), 'lugares' => $model->lugares()]);
            }
            if ($role !== 'Administrador') ApiResponse::send(403, 'error', 'El Técnico solo puede visualizar el inventario.');
            $input = ApiResponse::input();
            if ($method === 'POST' || $method === 'PUT') {
                $nombre = $this->requiredString($input, 'nombre', 100);
                $numeroSerie = $this->requiredString($input, 'numeroSerie', 100);
                $idLugar = filter_var($input['idLugar'] ?? null, FILTER_VALIDATE_INT);
                if (!$idLugar || $idLugar < 1) ApiResponse::send(422, 'error', 'Debes seleccionar un lugar válido.');
                if ($method === 'POST') {
                    $id = $model->crear($nombre, $numeroSerie, (int) $idLugar);
                    ApiResponse::send(201, 'success', 'Equipo registrado correctamente.', ['idEquipo' => $id]);
                }
                $idEquipo = $this->positiveId('idEquipo', $input);
                if (!$model->modificar($idEquipo, $nombre, $numeroSerie, (int) $idLugar)) ApiResponse::send(404, 'error', 'No se encontró el equipo o no hubo cambios.');
                ApiResponse::send(200, 'success', 'Equipo actualizado correctamente.', ['idEquipo' => $idEquipo]);
            }
            if ($method === 'DELETE') {
                $idEquipo = $this->positiveId('idEquipo', $input);
                if (!$model->eliminar($idEquipo)) ApiResponse::send(409, 'error', 'No se pudo eliminar el equipo. Puede tener préstamos asociados o no existir.');
                ApiResponse::send(200, 'success', 'Equipo eliminado correctamente.', ['idEquipo' => $idEquipo]);
            }
            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (PDOException $error) {
            error_log($error->getMessage());
            ApiResponse::send(409, 'error', 'No se pudo guardar el equipo. Verifica que el número de serie no esté repetido.');
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación de inventario.');
        } finally {
            $connector->desconectar();
        }
    }
}
