<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../models/AccesoDatosCatalogos.php';

final class CatalogosController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->requireRole(['Administrador']);
        $input = $method === 'GET' ? [] : ApiResponse::input();
        $tipo = (string) ($input['tipo'] ?? $_GET['tipo'] ?? '');
        if (!in_array($tipo, ['lugares', 'prioridades', 'estadosTicket', 'estadosPrestamo'], true)) {
            ApiResponse::send(400, 'error', 'Tipo de catálogo no válido.');
        }
        [$connector, $connection] = $this->connect();
        try {
            $model = new AccesoDatosCatalogos($connection);
            if ($method === 'GET') ApiResponse::send(200, 'success', 'Catálogo consultado correctamente.', $model->listar($tipo));
            if ($method === 'POST') {
                $nombre = $this->requiredString($input, 'nombre', 50);
                $this->validarNombre($tipo, $nombre);
                $id = $model->crear($tipo, $nombre);
                ApiResponse::send(201, 'success', 'Registro creado correctamente.', ['id' => $id]);
            }
            if ($method === 'PUT') {
                $id = $this->positiveId('id', $input);
                $nombre = $this->requiredString($input, 'nombre', 50);
                $this->validarNombre($tipo, $nombre);
                if (!$model->modificar($tipo, $id, $nombre)) ApiResponse::send(404, 'error', 'No se encontró el registro o no hubo cambios.');
                ApiResponse::send(200, 'success', 'Registro actualizado correctamente.', ['id' => $id]);
            }
            if ($method === 'DELETE') {
                $id = $this->positiveId('id', $input);
                if (in_array($tipo, ['estadosTicket', 'estadosPrestamo'], true) && $model->obtenerNombre($tipo, $id) !== null) ApiResponse::send(409, 'error', 'Los estados base del sistema no se pueden eliminar.');
                if (!$model->eliminar($tipo, $id)) ApiResponse::send(404, 'error', 'No se encontró el registro.');
                ApiResponse::send(200, 'success', 'Registro eliminado correctamente.', ['id' => $id]);
            }
            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (PDOException $error) {
            error_log($error->getMessage());
            ApiResponse::send(409, 'error', 'No se puede guardar o eliminar el registro porque puede estar repetido o relacionado con otros datos.');
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación del catálogo.');
        } finally {
            $connector->desconectar();
        }
    }

    private function validarNombre(string $tipo, string $nombre): void
    {
        $estados = [
            'estadosTicket' => ['Pendiente', 'En Proceso', 'Resuelto', 'Cancelado'],
            'estadosPrestamo' => ['Pendiente', 'Activo', 'Cancelado', 'Devuelto']
        ];
        if (isset($estados[$tipo]) && !in_array($nombre, $estados[$tipo], true)) {
            ApiResponse::send(422, 'error', 'Ese estado no está contemplado por las reglas actuales del sistema.');
        }
    }
}
