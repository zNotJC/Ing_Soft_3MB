# JOJOLOS — Análisis de dominio actualizado (v1.3)

## Objetivo y alcance

Este análisis representa el dominio de JOJOLOS tomando como base el código y los scripts SQL de la versión 1.2, más las correcciones acordadas para la versión 1.3. Se distingue entre el modelo actual revisado, los cambios incorporados en el código y las funcionalidades que siguen pendientes de confirmar o implementar.

El análisis de dominio describe conceptos, atributos, responsabilidades y relaciones. No implica por sí solo que una operación ya haya sido probada en una base de datos real.

## 1. Actores y permisos

JOJOLOS maneja tres roles: **Administrador**, **Docente** y **Técnico**. La gestión y la consulta son permisos distintos.

| Funcionalidad | Administrador | Docente | Técnico |
|---|---|---|---|
| Iniciar/cerrar sesión | Sí | Sí | Sí |
| Registrar, modificar, desactivar, reactivar y eliminar usuarios | Gestiona | No | No |
| Consultar usuarios | Sí | No | No |
| Crear y modificar laboratorios | Gestiona | No | Solo consulta |
| Registrar, consultar, modificar y dar de baja equipos | Gestiona | No | Solo consulta |
| Consultar métricas | Sí | Según los permisos ya existentes; no se amplían aquí | Sí |
| Consultar tickets e incidencias | Sí, solo consulta | Consulta y registra según el módulo actual | Gestiona |
| Cambiar estado de tickets | No | No se agregan permisos nuevos | Sí |
| Consultar solicitudes | Sí, solo consulta | Consulta las propias | Consulta y gestiona |
| Crear solicitudes | No | Sí | No |
| Modificar/cancelar solicitudes propias pendientes | No | Sí | No |
| Aprobar/rechazar solicitudes | No | No | Sí |
| Consultar préstamos | Consulta todos | Consulta los propios | No se concede acceso en esta versión |
| Crear préstamos | No | Sí | No |
| Modificar, aceptar, cancelar y registrar devolución de préstamos | Gestiona los existentes | No | No |

### 1.1. Administrador

Administra las cuentas y el inventario. Puede consultar métricas, tickets/incidencias y solicitudes, pero **no gestiona solicitudes ni incidencias**. Puede gestionar préstamos existentes y modificar su prioridad, pero no crea préstamos. La gestión de inventario incluye laboratorios y equipos.

La eliminación definitiva de usuarios se bloquea si existen tickets, solicitudes o préstamos asociados. En ese caso se puede desactivar la cuenta, preservando la integridad referencial y el historial.

### 1.2. Docente

Conserva las funciones que ya tenía, sin ampliar permisos de tickets ni incidencias. Puede registrar solicitudes y crear solicitudes de préstamo. Las solicitudes de préstamo quedan inicialmente en estado `Pendiente`; el Administrador decide su aceptación o gestión posterior.

### 1.3. Técnico

Gestiona solicitudes e incidencias, actualiza estados de tickets y consulta métricas e inventario. En catálogos puede consultar laboratorios y equipos, pero no crear, modificar ni eliminar esos registros.

## 2. Clases y atributos

Los nombres de atributos siguientes corresponden al esquema propuesto para v1.3. Los estados y prioridades de tickets y préstamos pasan a ser atributos de texto de sus respectivas entidades, en lugar de claves foráneas a catálogos separados.

### 2.1. Usuario

```text
Usuario
---------------------------------
- cedula : String(8) [PK]
- usuario : String(20)
- correo : String(120)
- claveHash : String(255)
- activo : Boolean
- sesionActiva : Boolean
---------------------------------
+ iniciarSesion()
+ cerrarSesion()
```

Representa una cuenta del sistema. `claveHash` almacena el hash de la contraseña; no se debe intentar recuperar la contraseña original desde ese valor. `activo` permite desactivar y reactivar una cuenta. La sesión de navegador y la bandera persistida de sesión deben mantenerse sincronizadas.

### 2.2. Administrador

```text
Administrador
---------------------------------
- cedula : String(8) [PK, FK Usuario]
---------------------------------
+ registrarUsuario()
+ consultarUsuarios()
+ modificarUsuario()
+ desactivarUsuario()
+ activarUsuario()
+ eliminarUsuario()
+ gestionarLaboratorios()
+ gestionarEquipos()
+ consultarMetricas()
+ consultarEstadoIncidencias()
+ consultarSolicitudes()
+ gestionarPrestamos()
```

Es un subtipo de Usuario. Sus operaciones de gestión se limitan a usuarios, inventario y préstamos existentes; las solicitudes e incidencias son de consulta.

### 2.3. Docente

```text
Docente
---------------------------------
- cedula : String(8) [PK, FK Usuario]
---------------------------------
+ registrarSolicitud()
+ modificarSolicitudPropia()
+ cancelarSolicitudPropia()
+ solicitarPrestamo()
+ registrarTicket()
```

Es un subtipo de Usuario. `registrarTicket()` representa la función existente en el módulo, no una ampliación de permisos. Cualquier otra operación del Docente debe conservarse según lo que ya permita el proyecto.

### 2.4. Técnico

```text
Tecnico
---------------------------------
- cedula : String(8) [PK, FK Usuario]
---------------------------------
+ consultarLaboratorios()
+ consultarEquipos()
+ consultarMetricas()
+ consultarSolicitudes()
+ aprobarSolicitud()
+ rechazarSolicitud()
+ gestionarTickets()
+ cambiarEstadoTicket()
```

Es un subtipo de Usuario. No administra cuentas, laboratorios ni equipos.

### 2.5. Laboratorio

```text
Laboratorio
---------------------------------
- idLaboratorio : Integer [PK]
- nombre : String(50)
---------------------------------
+ registrar()
+ consultar()
+ modificar()
+ eliminarSiNoTieneEquipos()
```

Representa un laboratorio, taller o espacio de trabajo. El nombre formal del módulo reemplaza el término anterior «Lugares». La eliminación se impide si todavía contiene equipos.

### 2.6. Equipo

```text
Equipo
---------------------------------
- idEquipo : Integer [PK]
- nombre : String(100)
- numeroSerie : String(100) [único]
- idLaboratorio : Integer [FK]
- tipo : String(50)
- marca : String(50)
- modelo : String(50)
- estado : String(30)
---------------------------------
+ registrar()
+ consultar()
+ modificar()
+ actualizarEstado()
+ darDeBaja()
```

Representa ordenadores, laptops, PC, televisores, controles de aire acondicionado y otros dispositivos del inventario. El estado se administra en el propio registro del equipo (`Activo`, `En reparación`, `De baja`). El Administrador tiene CRUD; el Técnico solo consulta.

### 2.7. Solicitud

```text
Solicitud
---------------------------------
- idSolicitud : Integer [PK]
- cedulaUsuario : String(8) [FK Usuario]
- tipoSolicitud : String(30)
- descripcion : String(500)
- cantidad : Integer
- equipo : String(100), nullable
- fechaSolicitud : Date
- fechaNecesaria : Date, nullable
- estado : String(20)
- respuesta : String(500), nullable
---------------------------------
+ crear()
+ consultar()
+ modificar()
+ cancelar()
+ aprobar()
+ rechazar()
```

Representa una petición de un Docente. Los tipos admitidos en el esquema son `Equipamiento`, `Reparación`, `Acceso`, `Licencia`, `Mobiliario` y `Otro`. El estado es un atributo directo, con valores como `Pendiente`, `Aprobada`, `Rechazada` y `Cancelada`. El Técnico la atiende; el Administrador puede consultarla, pero no gestionarla.

### 2.8. Ticket

```text
Ticket
---------------------------------
- idTicket : Integer [PK]
- titulo : String(150)
- descripcion : Text
- fechaRegistro : DateTime
- fechaLimite : Date
- cedulaSolicitante : String(8) [FK Usuario]
- idLaboratorio : Integer [FK Laboratorio]
- prioridad : String(20)
- estado : String(20)
---------------------------------
+ crear()
+ consultar()
+ cambiarEstado()
+ cancelar()
+ modificarDatos()
```

Representa una incidencia que requiere seguimiento. La prioridad es un atributo directo (`Baja`, `Media` o `Alta`) y el estado también (`Pendiente`, `En Proceso`, `Resuelto` o `Cancelado`). La interfaz puede mostrar `Vencido` como estado calculado cuando se supera la fecha límite y el ticket no está cerrado; no es necesario guardar `Vencido` como un estado independiente.

**Límite del esquema actual:** Ticket está asociado a un solicitante y a un laboratorio, pero no tiene una clave foránea directa a Equipo ni una clave de Técnico asignado. No se debe dibujar esa relación como existente sin agregar primero el requisito y la migración correspondientes.

### 2.9. Préstamo

```text
Prestamo
---------------------------------
- idPrestamo : Integer [PK]
- idEquipo : Integer [FK Equipo]
- cedulaUsuario : String(8) [FK Usuario]
- fechaPrestamo : Date
- fechaDevolucion : Date
- estado : String(20)
- prioridad : String(20)
---------------------------------
+ crearSolicitud()
+ consultar()
+ modificar()
+ aceptar()
+ cancelar()
+ registrarDevolucion()
```

Representa la solicitud y seguimiento de un equipo prestado. El Docente crea el registro; el Administrador gestiona los préstamos existentes. El estado se almacena directamente (`Pendiente`, `Activo`, `Cancelado`, `Devuelto`) y la prioridad también (`Baja`, `Media`, `Alta`). La prioridad se modifica en el formulario de préstamo, no en un catálogo independiente.

**Nota de modelado:** en el esquema actual `fechaDevolucion` es obligatoria y funciona como fecha prevista de devolución. No existe un atributo separado para guardar la fecha real de devolución; si se necesita ese dato histórico, debe añadirse como requisito y atributo nuevo.

### 2.10. AccesoSistema

```text
AccesoSistema
---------------------------------
- idAcceso : Integer [PK]
- cedulaUsuario : String(8) [FK Usuario]
- rol : String(20)
- fechaHora : DateTime
---------------------------------
+ registrarAcceso()
+ consultarAccesos()
```

Registra accesos al sistema y permite mostrar los últimos accesos en el panel. El rol guardado debe corresponder a uno de los tres roles admitidos.

### 2.11. Historial

El historial que se presenta actualmente en la aplicación se construye consultando y uniendo tickets, solicitudes y préstamos. En el esquema revisado no se identifica una tabla independiente `HISTORIAL`; por eso no debe dibujarse como una tabla persistente sin modificar antes la base de datos.

### 2.12. Diagnóstico y nota de resolución: pendientes

Se había planteado agregar diagnósticos y notas de resolución por equipo/ticket. Sin embargo, en los DDL y modelos revisados no hay tablas independientes para estos conceptos. Por tanto, se documentan como **funcionalidad pendiente**, no como entidades implementadas. Para incorporarlas habrá que definir atributos, cardinalidades, formularios, API y permisos.

## 3. Relaciones y cardinalidades

Las cardinalidades describen la relación entre los registros del dominio. `1` significa uno obligatorio y `0..N` significa ninguno o muchos.

| Entidad A | Relación | Entidad B | Cardinalidad |
|---|---|---|---|
| Usuario | se especializa en | Administrador | Subtipo; la cédula es PK y FK |
| Usuario | se especializa en | Docente | Subtipo; la cédula es PK y FK |
| Usuario | se especializa en | Tecnico | Subtipo; la cédula es PK y FK |
| Laboratorio | contiene | Equipo | Laboratorio `1` — Equipo `0..N`; cada equipo tiene un laboratorio |
| Laboratorio | recibe | Ticket | Laboratorio `1` — Ticket `0..N`; cada ticket referencia un laboratorio |
| Usuario | registra | Ticket | Usuario `1` — Ticket `0..N`; cada ticket tiene un solicitante |
| Usuario | realiza | Solicitud | Usuario `1` — Solicitud `0..N`; cada solicitud tiene un usuario |
| Usuario | solicita | Prestamo | Usuario `1` — Préstamo `0..N`; cada préstamo referencia un usuario |
| Equipo | aparece en | Prestamo | Equipo `1` — Préstamo `0..N`; cada préstamo corresponde a un equipo |
| Usuario | genera | AccesoSistema | Usuario `1` — AccesoSistema `0..N`; cada acceso corresponde a un usuario |

### Reglas que no deben inventarse en el DER

- No hay relación directa Ticket–Equipo en el esquema actual.
- No hay una clave foránea que asigne un Ticket o Solicitud a un Técnico concreto; el Técnico gestiona esos módulos mediante permisos de rol.
- No se modela Solicitud–Ticket como relación obligatoria, porque el esquema actual no define esa FK ni la regla de creación.
- No se dibuja una entidad Historial persistente: el historial mostrado se obtiene de varias tablas.
- Diagnóstico y NotaResolucion quedan pendientes hasta que se diseñen e implementen.

## 4. Cambios de la versión 1.3

1. Cambiar el nombre del módulo y tabla `LUGAR` a `LABORATORIO`; la FK se normaliza como `idLaboratorio`.
2. Incorporar `tipo`, `marca`, `modelo` y `estado` en Equipo para describir los distintos dispositivos y su situación.
3. Eliminar los catálogos `ESTADO_TICKET`, `ESTADO_PRESTAMO` y `PRIORIDAD` después de copiar los valores actuales a sus registros.
4. Guardar `estado` y `prioridad` directamente en Ticket.
5. Guardar `estado` y `prioridad` directamente en Prestamo.
6. Permitir que el Administrador consulte Solicitudes desde la barra de navegación, sin habilitarle acciones de gestión.
7. Permitir que el Docente cree solicitudes de préstamo; el Administrador no crea préstamos.
8. Permitir al Administrador consultar, modificar, aceptar, cancelar y registrar devoluciones de préstamos existentes.
9. Mostrar Laboratorios y Equipos en Catálogos; el Técnico solo consulta.
10. Incorporar el botón de eliminación de usuarios, manteniendo la protección de registros relacionados.
11. Corregir el JavaScript de Solicitudes: se utilizaba `formularioModificar` sin declarar, lo que podía detener la ejecución del script antes de cargar los datos.
12. Mantener respuestas JSON con `status`, `message` y `data`, consultas preparadas y controles de rol en el servidor.

## 5. Estado de implementación y validación

| Elemento | Estado en los archivos v1.3 | Verificación pendiente |
|---|---|---|
| Permisos de roles y navegación | Ajustados en controladores y vista | Probar sesión con cada rol |
| CRUD de usuarios y botón Eliminar | Incorporado; eliminación bloqueada si hay registros dependientes | Probar con datos de prueba y usuarios relacionados |
| Solicitudes en carga infinita | Corregida referencia JavaScript y habilitada consulta del Administrador | Probar la API contra MySQL |
| Creación de préstamo por Docente | Incorporada en controlador y vista | Probar POST y visibilidad de préstamos propios |
| Gestión de préstamos por Administrador | Incluye modificación de prioridad y acciones de estado | Probar PUT/DELETE y transiciones de estado |
| Catálogo de Laboratorios y Equipos | Formulario y permisos por rol incorporados | Aplicar migración y probar CRUD |
| Estados y prioridades inline | Modelos y DDL actualizados | Ejecutar migración tras respaldo y verificar los datos |
| Seguridad SQL | Los cambios de acceso a datos usan consultas preparadas cuando reciben valores | Revisar y probar todas las rutas |
| Integración completa | No se ha ejecutado contra la base real del usuario | Prueba funcional de todos los CRUD y contratos JSON |

## 6. Recomendación para dibujar el análisis

Para un diagrama de clases similar al ejemplo:

1. Colocar `Usuario` arriba y conectarlo mediante generalización a `Administrador`, `Docente` y `Tecnico`.
2. Ubicar `Laboratorio` junto a `Equipo`, unidos por `contiene` con cardinalidad `1` a `0..N`.
3. Conectar `Usuario` con `Solicitud`, `Ticket`, `Prestamo` y `AccesoSistema` usando las cardinalidades de la tabla.
4. Conectar `Equipo` con `Prestamo`; no conectar Ticket con Equipo mientras no exista la FK.
5. Incluir `estado` y `prioridad` como atributos dentro de Ticket y Prestamo; no dibujar catálogos separados para ellos.
6. Marcar las operaciones de gestión solo en los roles que tienen esos permisos.
7. Añadir Diagnostico y NotaResolucion en un área aparte titulada «Pendiente de implementar» únicamente si el profesor exige mostrar la evolución futura.

