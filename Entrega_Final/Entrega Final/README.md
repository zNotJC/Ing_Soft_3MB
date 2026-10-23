# JOJOLOS — Proyecto

Sistema web para la gestión de usuarios, laboratorios, equipos, solicitudes, tickets e incidencias, préstamos e historial.

## Requisitos
- PHP compatible con el proyecto.
- MySQL o MariaDB.
- Composer, si se necesita instalar o reconstruir las dependencias.
- Un navegador web actualizado.

## Instalación en una computadora nueva

1. Instala e inicia MySQL/MariaDB.
2. Abre phpMyAdmin o una consola SQL.
3. Ejecuta una sola vez `baseDeDatos/DDL/instalacionDesdeCero.sql`. Este archivo crea la base `jojolos` y las tablas del sistema.
4. Configura `proyecto/.env` con los datos de conexión locales. Usa `proyecto/.env.example` como referencia; no compartas contraseñas reales.
5. Si las dependencias no están incluidas, entra en `proyecto` y ejecuta `composer install`.
6. Desde la carpeta `proyecto`, inicia el servidor local con:

   ```bash
   php -S localhost:3000 -t public
   ```

7. Abre `http://localhost:3000/`.

**Importante:** `instalacionDesdeCero.sql` es para una base vacía. No lo ejecutes sobre una base que ya contiene información. Antes de probar cambios sobre la base actual, realiza una copia de seguridad.

## Organización de la base de datos

- `baseDeDatos/DDL/`: definición de la estructura de la base de datos. `instalacionDesdeCero.sql` es el punto de entrada para una instalación nueva.
- `baseDeDatos/DML/`: sentencias para insertar o modificar datos.
- `baseDeDatos/DQL/`: consultas para recuperar información.

Los archivos individuales de creación de tablas se conservan como material de trabajo y referencia. Para una instalación nueva, utiliza el archivo integrado `instalacionDesdeCero.sql` en lugar de ejecutar los DDL por separado, ya que podrían intentar crear tablas que ya existen.

## Arquitectura y API

El proyecto utiliza PHP para el servidor y JavaScript con Fetch para consumir los endpoints de `proyecto/public/api/`. Los endpoints delegan el procesamiento en controladores y modelos; las consultas SQL se mantienen en la capa de acceso a datos.

Las respuestas de la API siguen el contrato JSON del proyecto: `status`, `message` y `data`.

| Método HTTP | Uso REST esperado |
|---|---|
| `GET` | Consultar colecciones o un recurso |
| `POST` | Crear un recurso |
| `PUT` | Modificar un recurso existente |
| `DELETE` | Eliminar o cancelar un recurso, según las reglas del módulo |

Los permisos se validan en el servidor. Ocultar una opción del menú no sustituye la comprobación del rol en el endpoint.

## Roles acordados

- **Administrador:** gestiona usuarios e inventario/laboratorios y consulta la información que corresponde a su rol.
- **Técnico:** gestiona solicitudes, tickets/incidencias y préstamos; puede consultar inventario y laboratorios sin modificarlos.
- **Docente:** utiliza las funciones habilitadas para solicitudes, tickets y préstamos; no accede a inventario y laboratorios.

## Verificación

Después de configurar la base de datos, prueba el inicio de sesión y cada módulo con usuarios de los roles correspondientes. Para revisar la sintaxis de un archivo PHP se puede ejecutar `php -l ruta/al/archivo.php`.

La integración solo se considera comprobada después de ejecutar las operaciones contra la base local y revisar las respuestas de la API y los registros de errores de PHP.
