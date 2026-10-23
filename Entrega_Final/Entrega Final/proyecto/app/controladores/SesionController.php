<?php

require_once __DIR__ . '/../nucleo/ApiResponse.php';

final class SesionController
{
    public function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['cedula']) || empty($_SESSION['rol'])) {
            ApiResponse::send(401, 'error', 'La sesión no está iniciada.');
        }
        ApiResponse::send(200, 'success', 'Sesión activa.', [
            'cedula' => (string) $_SESSION['cedula'],
            'usuario' => (string) ($_SESSION['usuario'] ?? ''),
            'rol' => (string) $_SESSION['rol']
        ]);
    }
}
