<?php

final class ApiResponse
{
    public static function send(int $httpCode, string $status, string $message, mixed $data = null): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw ?: '{}', true);
            if (!is_array($data)) {
                self::send(400, 'error', 'El cuerpo JSON no es válido.');
            }
            return $data;
        }
        return $_POST;
    }
}
