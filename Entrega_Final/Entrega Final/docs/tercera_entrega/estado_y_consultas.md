# Tercera entrega — consultas, álgebra relacional y DML (v1.3)

## Contrato de la API

Las respuestas JSON de la API siguen la estructura:

```json
{
  "status": "success",
  "message": "Operación realizada.",
  "data": {}
}
```

Los errores usan `status: "error"` y un mensaje explicativo. Las rutas públicas reciben la petición y delegan en controladores; los modelos concentran el acceso a datos.

## Endpoints y métodos

- `GET /api/tickets.php`: consulta tickets, laboratorios y opciones de prioridad.
- `POST /api/tickets.php`: Docente o Técnico registran un ticket, según los permisos del módulo.
- `PUT /api/tickets.php`: solo Técnico modifica el estado o los datos permitidos.
- `DELETE /api/tickets.php`: solo Técnico cancela un ticket no cerrado.
- `GET /api/solicitudes.php`: Docente consulta las propias; Técnico y Administrador consultan el listado.
- `POST /api/solicitudes.php`: solo Docente crea solicitudes.
- `PUT /api/solicitudes.php`: Docente modifica las propias pendientes; Técnico aprueba o rechaza.
- `DELETE /api/solicitudes.php`: Docente cancela las propias y Técnico puede cancelar según las reglas del módulo.
- `GET /api/prestamos.php`: Administrador consulta todos; Docente consulta los propios.
- `POST /api/prestamos.php`: solo Docente crea una solicitud de préstamo.
- `PUT /api/prestamos.php`: solo Administrador modifica, acepta, cancela o registra devoluciones.
- `DELETE /api/prestamos.php`: solo Administrador puede cancelar un préstamo permitido.
- `GET /api/equipos.php`: Administrador y Técnico consultan inventario.
- `POST`, `PUT`, `DELETE /api/equipos.php`: solo Administrador gestiona equipos.
- `GET /api/catalogos.php?tipo=laboratorios|equipos`: Administrador y Técnico consultan catálogos.
- `POST`, `PUT`, `DELETE /api/catalogos.php`: solo Administrador gestiona laboratorios y equipos.
- `GET /api/usuarios.php`: solo Administrador consulta usuarios.
- `POST`, `PUT`, `DELETE /api/usuarios.php`: solo Administrador gestiona cuentas; la eliminación definitiva se rechaza si existen registros dependientes.
- `GET /api/dashboard.php`: Administrador y Técnico consultan métricas.

## Lista punteada de consultas utilizadas

1. Buscar un usuario por cédula y determinar el rol con `LEFT JOIN` a `ADMINISTRADOR`, `DOCENTE` y `TECNICO`.
2. Listar usuarios y su rol para la pantalla de administración.
3. Consultar tickets con el usuario solicitante y el laboratorio.
4. Listar laboratorios para los formularios de tickets y equipos.
5. Listar solicitudes unidas a su usuario solicitante.
6. Consultar las solicitudes de un Docente filtrando por `cedulaUsuario`.
7. Insertar una solicitud con estado inicial `Pendiente`.
8. Modificar una solicitud solo si permanece `Pendiente` y pertenece al Docente cuando corresponda.
9. Cambiar estado y respuesta de una solicitud desde el Técnico.
10. Consultar préstamos con equipo y usuario.
11. Listar préstamos de un Docente filtrando por su cédula.
12. Insertar un préstamo con estado inicial `Pendiente` y prioridad inicial `Media`.
13. Modificar equipo, fechas y prioridad de un préstamo existente.
14. Cambiar estado de un préstamo al aceptar, cancelar o devolver.
15. Listar equipos con su laboratorio y sus atributos de inventario.
16. Insertar, modificar y eliminar equipos; no se elimina un equipo que tenga préstamos asociados.
17. Consultar los últimos accesos del sistema.
18. Contar tickets pendientes y resueltos.
19. Contar solicitudes pendientes.
20. Contar préstamos activos y equipos registrados.
21. Construir el historial con `UNION ALL` de tickets, solicitudes y préstamos.

## Consultas expresadas con álgebra relacional

Notación: `σ` selección, `π` proyección, `⋈` unión/Join, `γ` agrupación y conteo.

1. **Usuario y rol**  
   `π cedula, usuario, correo, activo (USUARIO)`, complementado con `LEFT JOIN` a las tres tablas de rol.

2. **Tickets con solicitante y laboratorio**  
   `π idTicket, titulo, fechaRegistro, laboratorio.nombre, prioridad, estado (TICKET ⋈ USUARIO ⋈ LABORATORIO)`.

3. **Solicitudes con solicitante**  
   `π idSolicitud, tipoSolicitud, descripcion, cantidad, estado, usuario (SOLICITUD ⋈ USUARIO)`.

4. **Solicitudes propias de un Docente**  
   `σ cedulaUsuario = :cedula (SOLICITUD)`.

5. **Préstamos con equipo y solicitante**  
   `π idPrestamo, equipo.nombre, numeroSerie, usuario, fechaPrestamo, fechaDevolucion, estado, prioridad (PRESTAMO ⋈ EQUIPO ⋈ USUARIO)`.

6. **Préstamos de un Docente**  
   `σ PRESTAMO.cedulaUsuario = :cedula (PRESTAMO)`.

7. **Equipos con laboratorio**  
   `π idEquipo, nombre, numeroSerie, tipo, marca, modelo, estado, laboratorio.nombre (EQUIPO ⋈ LABORATORIO)`.

8. **Tickets pendientes**  
   `σ estado = 'Pendiente' (TICKET)`.

9. **Conteo de tickets por estado**  
   `γ estado, COUNT(idTicket) (TICKET)`.

10. **Conteo de solicitudes por estado**  
    `γ estado, COUNT(idSolicitud) (SOLICITUD)`.

11. **Conteo de préstamos por estado**  
    `γ estado, COUNT(idPrestamo) (PRESTAMO)`.

12. **Historial general**  
    Unión de las proyecciones de tickets, solicitudes y préstamos, con campos comunes `id`, `tipo`, `detalle`, `usuario`, `estado`, `fecha`.

## Lista de DML

- `INSERT INTO USUARIO`: registrar una cuenta.
- `INSERT INTO ADMINISTRADOR`, `DOCENTE` o `TECNICO`: asignar el subtipo de rol.
- `UPDATE USUARIO`: modificar datos de cuenta, contraseña hash, estado activo o sesión.
- `DELETE FROM USUARIO`: eliminación definitiva controlada; se realiza solo si no hay tickets, solicitudes o préstamos asociados.
- `UPDATE USUARIO SET activo = FALSE/TRUE`: desactivar o reactivar cuenta.
- `INSERT INTO SOLICITUD`: crear solicitud con estado inicial `Pendiente`.
- `UPDATE SOLICITUD`: modificar datos, aprobar, rechazar o cancelar.
- `INSERT INTO TICKET`: crear ticket con estado `Pendiente` y prioridad inicial.
- `UPDATE TICKET`: cambiar estado o actualizar datos permitidos.
- `INSERT INTO PRESTAMO`: crear solicitud de préstamo con estado `Pendiente` y prioridad `Media`.
- `UPDATE PRESTAMO`: modificar datos y prioridad; cambiar estado a `Activo`, `Cancelado` o `Devuelto`.
- `INSERT INTO LABORATORIO`: registrar laboratorio.
- `UPDATE LABORATORIO`: modificar nombre.
- `DELETE FROM LABORATORIO`: permitido únicamente si no contiene equipos.
- `INSERT INTO EQUIPO`: registrar equipo.
- `UPDATE EQUIPO`: modificar datos, laboratorio o estado.
- `DELETE FROM EQUIPO`: permitido únicamente si no tiene préstamos asociados.
- `INSERT INTO ACCESO_SISTEMA`: registrar acceso.

## Seguridad y persistencia

- Las operaciones con parámetros del usuario utilizan consultas preparadas.
- Las operaciones de escritura comprueban el rol en el backend; ocultar un botón en la interfaz no sustituye el control del servidor.
- Los datos sensibles de `.env` no deben compartirse ni subirse al repositorio.
- La migración v1.3 copia los estados y prioridades actuales antes de eliminar los catálogos antiguos.
- La sintaxis de PHP y JavaScript se comprobó en los archivos de esta versión. La integración con MySQL debe verificarse en la base real después de una copia de seguridad y de ejecutar la migración.
