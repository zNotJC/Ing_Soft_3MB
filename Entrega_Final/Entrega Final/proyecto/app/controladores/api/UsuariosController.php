<?php
require_once __DIR__ . '/../../nucleo/ApiController.php';
require_once __DIR__ . '/../../modelos/AccesoDatosUsuario.php';

final class UsuariosController extends ApiController
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->requireRole(['Administrador']);
        [$connector, $connection] = $this->connect();
        try {
            $model = new AccesoDatosUsuario($connection);
            if ($method === 'GET') {
                ApiResponse::send(200, 'success', 'Usuarios consultados correctamente.', $model->listarUsuarios());
            }
            $input = ApiResponse::input();
            if ($method === 'POST') {
                $cedula = trim((string) ($input['cedula'] ?? ''));
                $usuario = $this->requiredString($input, 'usuario', 20);
                $correo = $this->requiredString($input, 'correo', 120);
                $contrasena = (string) ($input['contrasena'] ?? '');
                $rol = (string) ($input['rol'] ?? '');
                if (!preg_match('/^[0-9]{8}$/', $cedula) || !filter_var($correo, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z0-9]{4,20}$/', $usuario) || strlen($contrasena) < 8 || !in_array($rol, ['Administrador', 'Docente', 'Tecnico'], true)) {
                    ApiResponse::send(422, 'error', 'Los datos no son válidos. La contraseña debe tener al menos 8 caracteres.');
                }
                $model->crearUsuario($cedula, $usuario, $correo, password_hash($contrasena, PASSWORD_DEFAULT), $rol);
                ApiResponse::send(201, 'success', 'Usuario creado correctamente.', ['cedula' => $cedula]);
            }
            if ($method === 'PUT') {
                if (($input['accion'] ?? '') === 'activar') {
                    $cedula = trim((string) ($input['cedula'] ?? ''));
                    if (!preg_match('/^[0-9]{8}$/', $cedula)) ApiResponse::send(400, 'error', 'Cédula no válida.');
                    if (!$model->activarUsuario($cedula)) ApiResponse::send(404, 'error', 'No se encontró una cuenta inactiva con esa cédula.');
                    ApiResponse::send(200, 'success', 'Usuario activado correctamente.', ['cedula' => $cedula]);
                }
                $cedulaOriginal = trim((string) ($input['cedulaOriginal'] ?? $input['cedula'] ?? ''));
                $cedula = trim((string) ($input['cedula'] ?? ''));
                $usuario = $this->requiredString($input, 'usuario', 20);
                $correo = $this->requiredString($input, 'correo', 120);
                $contrasena = (string) ($input['contrasena'] ?? '');
                $rol = (string) ($input['rol'] ?? '');
                if (!preg_match('/^[0-9]{8}$/', $cedula) || !preg_match('/^[0-9]{8}$/', $cedulaOriginal) || !filter_var($correo, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z0-9]{4,20}$/', $usuario) || ($contrasena !== '' && strlen($contrasena) < 8) || !in_array($rol, ['Administrador', 'Docente', 'Tecnico'], true)) {
                    ApiResponse::send(422, 'error', 'Los datos del usuario no son válidos.');
                }
                if ($cedulaOriginal === (string) $_SESSION['cedula'] && $rol !== (string) ($_SESSION['rol'] ?? '')) {
                    ApiResponse::send(409, 'error', 'No puedes cambiar tu propio rol durante la sesión actual.');
                }
                if ($cedulaOriginal === (string) $_SESSION['cedula'] && $cedula !== $cedulaOriginal) {
                    ApiResponse::send(409, 'error', 'No puedes cambiar tu propia cédula durante la sesión actual.');
                }
                $hash = $contrasena !== '' ? password_hash($contrasena, PASSWORD_DEFAULT) : null;
                if (!$model->actualizarUsuario($cedulaOriginal, $cedula, $usuario, $correo, $hash, $rol)) {
                    ApiResponse::send(404, 'error', 'No se encontró el usuario o no se realizaron cambios.');
                }
                ApiResponse::send(200, 'success', 'Usuario actualizado correctamente.', ['cedula' => $cedula]);
            }
            if ($method === 'DELETE') {
                $cedula = trim((string) ($input['cedula'] ?? $_GET['cedula'] ?? ''));
                if (!preg_match('/^[0-9]{8}$/', $cedula)) ApiResponse::send(400, 'error', 'Cédula no válida.');
                if ($cedula === (string) $_SESSION['cedula']) ApiResponse::send(409, 'error', 'No puedes eliminar ni desactivar tu propia cuenta.');
                if (($input['accion'] ?? '') === 'desactivar') {
                    if (!$model->desactivarUsuario($cedula)) ApiResponse::send(404, 'error', 'No se encontró una cuenta activa con esa cédula.');
                    ApiResponse::send(200, 'success', 'Usuario desactivado correctamente.', ['cedula' => $cedula]);
                }
                if (!$model->eliminarUsuario($cedula)) {
                    ApiResponse::send(409, 'error', 'No se puede eliminar este usuario porque no existe o tiene solicitudes, tickets o préstamos asociados. Puedes desactivarlo.');
                }
                ApiResponse::send(200, 'success', 'Usuario eliminado correctamente.', ['cedula' => $cedula]);
            }
            $this->methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
        } catch (PDOException $error) {
            error_log($error->getMessage());
            ApiResponse::send(409, 'error', 'No se pudo guardar el usuario. Verifica que la cédula, el usuario y el correo no estén repetidos.');
        } catch (Throwable $error) {
            error_log($error->getMessage());
            ApiResponse::send(500, 'error', 'No se pudo completar la operación de usuarios.');
        } finally {
            $connector->desconectar();
        }
    }
}
