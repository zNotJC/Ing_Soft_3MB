<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/nucleo/ApiResponse.php';
require_once __DIR__ . '/../../app/modelos/ConectorPDO.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['cedula']) || (($_SESSION['rol'] ?? '') !== 'Administrador')) {
    ApiResponse::send(403, 'error', 'Solo el Administrador puede consultar el diagnóstico de esquema.');
}
try {
    $connector = new ConectorPDO();
    $pdo = $connector->establecerConexion();
    $required = [
        'usuario' => ['cedula','usuario','correo','claveHash','activo','sesionActiva'],
        'laboratorio' => ['idLaboratorio','nombre'],
        'equipo' => ['idEquipo','nombre','idLaboratorio','tipo','estado'],
        'ticket' => ['idTicket','titulo','descripcion','fechaRegistro','fechaLimite','cedulaSolicitante','idLaboratorio','prioridad','estado'],
        'solicitud' => ['idSolicitud','cedulaUsuario','tipoSolicitud','descripcion','cantidad','equipo','fechaSolicitud','fechaNecesaria','estado','respuesta'],
        'prestamo' => ['idPrestamo','idEquipo','cedulaUsuario','fechaPrestamo','fechaDevolucion','estado','prioridad']
    ];
    $missing = [];
    foreach ($required as $table => $columns) {
        $check = $pdo->prepare('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table');
        $check->execute(['table' => $table]);
        $present = $check->fetchAll(PDO::FETCH_COLUMN);
        if (!$present) { $missing[$table] = ['tabla ausente']; continue; }
        $absent = array_values(array_diff($columns, $present));
        if ($absent) { $missing[$table] = $absent; }
    }
    $connector->desconectar();
    if ($missing) { ApiResponse::send(409, 'error', 'El esquema de la base de datos no coincide con el requerido por JOJOLOS v1.5.', ['columnasFaltantes' => $missing]); }
    ApiResponse::send(200, 'success', 'El esquema principal coincide con las columnas requeridas por la aplicación.', ['tablasVerificadas' => array_keys($required)]);
} catch (Throwable $error) {
    error_log('[API diagnosticoBD] ' . $error->getMessage());
    ApiResponse::send(500, 'error', 'No se pudo diagnosticar la base de datos. Revisa la terminal del servidor.');
}
