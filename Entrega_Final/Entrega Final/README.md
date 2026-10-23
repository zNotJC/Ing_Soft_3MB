# JOJOLOS — Sistema de Gestión de Laboratorios

Versión de trabajo: **v1.21**.

## Estructura principal

- `baseDeDatos/`: scripts DDL, DML y DQL.
- `documentacion/`: documentación técnica consolidada.
- `proyecto/`: aplicación PHP, API, vistas, modelos, controladores y recursos públicos.

## Instalación desde cero

1. Crear una base de datos vacía en MySQL.
2. Ejecutar `baseDeDatos/DDL/instalacionDesdeCero.sql`.
3. Configurar la conexión en `proyecto/.env`.
4. Configurar el servidor web para apuntar a `proyecto/public`.
5. Iniciar sesión con una de las cuentas iniciales.

### Cuentas iniciales

| Rol | Cédula | Usuario | Contraseña |
|---|---|---|---|
| Administrador | `69696969` | `admin` | `clave123` |
| Técnico | `65656565` | `técnico` | `clave123` |
| Docente | `63636363` | `docente` | `clave123` |

## Historial de versiones documentales

- v1.2: estructura inicial del proyecto.
- v1.13: documentación de consultas y persistencia.
- v1.14: documentación de API REST.
- v1.18: revisión de flujos de tickets y préstamos.
- v1.19: ajustes de validación y filtros.
- v1.20: organización inicial de la documentación.
- **v1.21: ajustes de estados, cuentas iniciales, idioma/tema y organización de capas.**

La documentación completa se encuentra en `DOCUMENTACION_JOJOLOS.md`.
