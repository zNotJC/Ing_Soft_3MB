# API de JOJOLOS — revisión v1.5

## Contrato uniforme
Todas las respuestas de los endpoints usan JSON con la estructura externa:

```json
{"status":"success","message":"Operación completada.","data":{}}
```

En errores, `status` toma el valor `error`, `message` explica el resultado y `data` puede contener detalles de validación no sensibles. Los endpoints envían `Content-Type: application/json`, `Cache-Control: no-store` y códigos HTTP coherentes (200, 201, 400, 401, 403, 404, 409, 422, 500).

## Recursos y métodos

| Recurso | GET | POST | PUT | DELETE |
|---|---|---|---|---|
| `api/equipos.php` | Consultar inventario y laboratorios | Crear equipo o laboratorio | Modificar equipo o laboratorio | Eliminar equipo o laboratorio, sujeto a relaciones |
| `api/tickets.php` | Consultar tickets, laboratorios y prioridades | Registrar ticket según permisos | Técnico cambia estado o modifica ticket | Técnico cancela un ticket abierto |
| `api/solicitudes.php` | Consultar solicitudes | Docente registra solicitud | Docente modifica la propia o Técnico resuelve | Cancelar según permisos |
| `api/prestamos.php` | Consultar préstamos y equipos disponibles | Docente solicita préstamo | Técnico gestiona el préstamo | Técnico cancela según estado |
| `api/historial.php` | Consultar historial agregado | No permitido | No permitido | No permitido |

## Capas
JavaScript actúa como cliente HTTP; los archivos de `public/api` enrutan la petición; los controladores validan autenticación, permisos, método y datos; los modelos ejecutan SQL preparado; `ConectorPDO` concentra la conexión. Los endpoints no deben ejecutar SQL directamente.

## Consideración sobre stateless
La API mantiene autenticación por sesión PHP/cookie porque es el mecanismo de inicio de sesión actual. Por ello, esta versión aplica recursos, métodos HTTP, contrato JSON y separación en capas, pero **no es completamente stateless** en el sentido estricto de REST. Para pasar a tokens tendría que diseñarse e implementarse un cambio de autenticación separado, no improvisado dentro de esta corrección.

## Diagnóstico del esquema
Con sesión de Administrador, se puede consultar `api/estadoBaseDatos.php`. El endpoint de solo lectura informa qué tablas o columnas esperadas faltan sin exponer credenciales ni mensajes SQL internos. Ejecutar la migración v1.5 después de respaldar la base de datos.
