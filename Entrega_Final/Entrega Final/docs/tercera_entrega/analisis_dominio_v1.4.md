# Análisis de dominio JOJOLOS — v1.4

## Roles y permisos
- **Administrador:** gestiona usuarios, laboratorios y equipos; consulta tickets/incidencias, solicitudes, métricas e historial. En préstamos solo consulta.
- **Docente:** conserva sus funciones existentes; crea solicitudes de tipo Laboratorio o Reparación y solicita préstamos propios. Puede consultar inventario y laboratorios en modo lectura según la instrucción más específica de v1.4.
- **Técnico:** gestiona solicitudes e incidencias/tickets; consulta inventario y laboratorios; gestiona préstamos (aceptación, modificación, cancelación y devolución) y consulta métricas e historial.

## Entidades
### Usuario
`cedula`, `usuario`, `correo`, `claveHash`, `activo`, `sesionActiva`. La cédula identifica la cuenta. La contraseña se conserva como hash.

### Laboratorio
`idLaboratorio`, `nombre`. Un laboratorio puede tener varios equipos.

### Equipo
`idEquipo`, `nombre`, `idLaboratorio`, `tipo`, `estado`. `tipo` solo admite Ordenadores, Periféricos, Televisores o Aires Acondicionados. `estado` admite Disponible, No disponible, Roto o En uso. Número de serie, marca y modelo se retiran.

### Solicitud
`idSolicitud`, `cedulaUsuario`, `tipoSolicitud`, `descripcion`, `cantidad`, `equipo`, `fechaSolicitud`, `fechaNecesaria`, `estado`, `respuesta`. `tipoSolicitud` solo admite Laboratorio o Reparación. El destino se selecciona de laboratorios/equipos existentes; no se escribe libremente.

### Ticket
`idTicket`, `titulo`, `descripcion`, `fechaRegistro`, `fechaLimite`, `cedulaSolicitante`, `idLaboratorio`, `prioridad`, `estado`. La prioridad y el estado son atributos, no catálogos independientes. El estado vencido puede calcularse según la fecha límite.

### Préstamo
`idPrestamo`, `idEquipo`, `cedulaUsuario`, `fechaPrestamo`, `fechaDevolucion`, `estado`, `prioridad`. La prioridad y el estado están almacenados directamente en el préstamo. El Docente lo solicita, el Técnico lo gestiona y el Administrador lo consulta.

### Historial
Vista de consulta consolidada de tickets, solicitudes y préstamos ordenada por fecha; no reemplaza los registros de cada entidad.

## Relaciones principales
- Usuario 1:N Solicitud.
- Usuario 1:N Ticket como solicitante.
- Usuario 1:N Préstamo.
- Laboratorio 1:N Equipo.
- Laboratorio 1:N Ticket.
- Equipo 1:N Préstamo a lo largo del tiempo.

## Estado de implementación
La v1.4 modifica interfaz, endpoints y consultas para reflejar estos permisos. Las operaciones deben validarse contra la base de datos local después de ejecutar la migración `baseDeDatos/DDL/migracionV1_4.sql`. No se declara validada la integración MySQL hasta probar la migración y los CRUD reales.
