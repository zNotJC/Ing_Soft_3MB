# Tercera entrega: estado técnico y consultas

## Estado de esta versión

La raíz web debe ser `proyecto/public`. Inicia el servidor desde `proyecto` con `php -S localhost:3000 -t public`; no uses la carpeta superior como raíz web.

### Implementado en el código
- API con contrato JSON uniforme: `status`, `message` y `data`.
- Endpoints que delegan en controladores; no contienen consultas SQL.
- Métodos GET, POST, PUT y DELETE para tickets, solicitudes, préstamos, usuarios, equipos y catálogos.
- Consultas GET para dashboard e historial.
- PDO con consultas preparadas en las operaciones que reciben datos.
- Roles Administrador, Docente y Técnico en la lógica de inicio de sesión.
- Registro de usuarios restringido al Administrador.
- Renderizado de las tablas actualizado con el DOM, sin `innerHTML` ni `insertAdjacentHTML` en los scripts JavaScript revisados.
- Vistas nuevas para gestión de usuarios, inventario y catálogos.

### Pendiente de verificación funcional
- Ejecutar la migración SQL sobre una copia de la base de datos y confirmar las claves foráneas.
- Probar todas las operaciones con una base de datos real y cuentas de cada rol.
- Confirmar el CRUD de punta a punta en cada pantalla y probar respuestas de error.
- Confirmar si la versión de MySQL instalada admite las restricciones CHECK y la sintaxis de migración incluida.
- Completar la cobertura de pruebas de seguridad y accesibilidad.

## Contrato JSON

Éxito:

```json
{
  "status": "success",
  "message": "Operación realizada correctamente.",
  "data": {}
}
```

Error:

```json
{
  "status": "error",
  "message": "Descripción del error.",
  "data": null
}
```

## Endpoints

- `GET /api/tickets.php`: tickets, lugares y prioridades.
- `POST /api/tickets.php`: registrar ticket.
- `PUT /api/tickets.php`: modificar ticket o cambiar su estado.
- `DELETE /api/tickets.php`: cancelar ticket, conservando su registro.
- `GET /api/solicitudes.php`: listar solicitudes; el Docente recibe las propias y el Técnico las solicitudes para gestión.
- `POST /api/solicitudes.php`: registrar solicitud.
- `PUT /api/solicitudes.php`: modificar una solicitud propia pendiente o cambiar su estado como Técnico.
- `DELETE /api/solicitudes.php`: cancelar una solicitud permitida.
- `GET /api/prestamos.php`: préstamos, equipos y estados.
- `POST`, `PUT`, `DELETE /api/prestamos.php`: crear, actualizar o cancelar préstamos.
- `GET`, `POST`, `PUT`, `DELETE /api/usuarios.php`: consultar, crear, modificar y desactivar cuentas; solo Administrador.
- `GET`, `POST`, `PUT`, `DELETE /api/equipos.php`: consultar y gestionar equipos; el Técnico solo consulta.
- `GET`, `POST`, `PUT`, `DELETE /api/catalogos.php?tipo=...`: gestionar lugares, prioridades y estados; solo Administrador.
- `GET /api/dashboard.php`: métricas y últimos accesos; solo Administrador y Técnico.
- `GET /api/historial.php`: consultar historial.

Los registros de acceso se consideran auditables: se consultan y agregan desde la aplicación, pero no se editan ni borran desde la interfaz.

## Lista de consultas utilizadas por el sistema

1. Buscar una cuenta por cédula, combinando `USUARIO` con `ADMINISTRADOR`, `DOCENTE` y `TECNICO`.
2. Listar usuarios y obtener su rol mediante uniones externas con las tablas de rol.
3. Insertar una cuenta y su subtipo de rol dentro de una transacción.
4. Actualizar los datos de una cuenta y, cuando corresponde, su subtipo de rol.
5. Desactivar una cuenta y marcar su sesión como inactiva.
6. Listar tickets con lugar, prioridad, estado y solicitante.
7. Insertar tickets y obtener su estado inicial.
8. Actualizar datos y estado de un ticket.
9. Listar solicitudes con el usuario solicitante; filtrar por cédula cuando consulta un Docente.
10. Insertar, modificar, responder, cambiar estado o cancelar solicitudes.
11. Listar préstamos con equipo, usuario y estado.
12. Insertar, modificar, aceptar, cancelar y registrar la devolución de préstamos.
13. Listar equipos con su lugar asociado; insertar, modificar y eliminar equipos sin préstamos activos.
14. Listar, insertar, modificar y eliminar elementos de los catálogos permitidos.
15. Contar tickets pendientes y resueltos.
16. Contar solicitudes pendientes y préstamos activos.
17. Consultar los últimos accesos, ordenados por fecha descendente.
18. Consultar los movimientos del historial y ordenarlos por fecha.

## Consultas representadas mediante álgebra relacional

Notación: `σ` selección, `π` proyección, `⋈` reunión, `γ` agrupación/agregación.

1. **Tickets pendientes**

   `σ nombreEstado = 'Pendiente' (TICKET ⋈ ESTADO_TICKET)`

2. **Tickets con lugar y prioridad**

   `π idTicket, titulo, fechaRegistro, lugar, prioridad, estado (TICKET ⋈ LUGAR ⋈ PRIORIDAD ⋈ ESTADO_TICKET)`

3. **Solicitudes de un Docente**

   `σ cedulaUsuario = cédulaSesion (SOLICITUD ⋈ USUARIO)`

4. **Equipos por lugar**

   `π idEquipo, nombre, numeroSerie, nombreLugar (EQUIPO ⋈ LUGAR)`

5. **Préstamos activos**

   `σ nombreEstado = 'Activo' (PRESTAMO ⋈ ESTADO_PRESTAMO ⋈ EQUIPO ⋈ USUARIO)`

6. **Cantidad de tickets por estado**

   `γ nombreEstado; COUNT(idTicket) (TICKET ⋈ ESTADO_TICKET)`

7. **Últimos accesos**

   `π fechaHora, usuario, rol (ACCESO_SISTEMA ⋈ USUARIO)`

Estas expresiones son la representación relacional de las consultas descritas; deben contrastarse con los nombres exactos del esquema desplegado al preparar la entrega final.

## Lista de DML

- `INSERT INTO USUARIO`: alta de cuenta.
- `INSERT INTO ADMINISTRADOR`, `DOCENTE` o `TECNICO`: asignación de rol dentro de una transacción.
- `UPDATE USUARIO`: modificar datos, cambiar rol, desactivar cuenta y finalizar sesión.
- `INSERT INTO TICKET`: alta de ticket.
- `UPDATE TICKET`: modificación, cambio de estado o cancelación lógica.
- `INSERT INTO solicitud`: alta de solicitud.
- `UPDATE solicitud`: modificación, respuesta, aprobación, rechazo o cancelación.
- `INSERT INTO prestamo`: alta de préstamo.
- `UPDATE prestamo`: modificación, aceptación, devolución o cancelación.
- `INSERT INTO equipo`, `UPDATE equipo`, `DELETE FROM equipo`: operaciones del inventario; la eliminación se bloquea si existen préstamos activos o pendientes.
- `INSERT`, `UPDATE` y `DELETE` en `lugar`, `prioridad`, `estado_ticket` y `estado_prestamo`: mantenimiento de catálogos.
- `INSERT INTO acceso_sistema`: registro de acceso.

## Seguridad

- Las consultas con datos externos usan sentencias preparadas PDO.
- Las tablas SQL y nombres de columna dinámicos se limitan a listas permitidas.
- Las API verifican sesión y rol en el servidor.
- Las contraseñas se almacenan con `password_hash`.
- Las cuentas se desactivan en lugar de borrar sus registros.
- Los tickets, solicitudes y préstamos se cancelan conservando su registro cuando corresponde.
- Las pantallas insertan datos mediante `textContent` y creación de nodos DOM.
