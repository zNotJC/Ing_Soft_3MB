<?php

require_once __DIR__ . '/ApiResponse.php';
require_once __DIR__ . '/../modelos/ConectorPDO.php';

abstract class ApiController
{
    protected function requireSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['cedula'])) {
            ApiResponse::send(401, 'error', 'Debes iniciar sesión.');
        }
    }

    protected function requireRole(array $roles): string
    {
        $this->requireSession();
        $role = (string) ($_SESSION['rol'] ?? '');
        if (!in_array($role, $roles, true)) {
            ApiResponse::send(403, 'error', 'No tienes permisos para realizar esta operación.');
        }
        return $role;
    }

    protected function connect(): array
    {
        try {
            $connector = new ConectorPDO();
            return [$connector, $connector->establecerConexion()];
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo conectar con la base de datos.');
        }
    }

    protected function methodNotAllowed(array $allowed): void
    {
        header('Allow: ' . implode(', ', $allowed));
        ApiResponse::send(405, 'error', 'Método HTTP no permitido.');
    }

    protected function positiveId(string $key, array $data = []): int
    {
        $id = filter_var($data[$key] ?? $_GET[$key] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            ApiResponse::send(400, 'error', 'Identificador no válido.');
        }
        return $id;
    }

    protected function requiredString(array $data, string $key, int $maxLength): string
    {
        $value = trim((string) ($data[$key] ?? ''));
        if ($value === '' || mb_strlen($value) > $maxLength) {
            ApiResponse::send(422, 'error', 'El campo ' . $key . ' es obligatorio y debe respetar la longitud máxima.');
        }
        return $value;
    }
}
