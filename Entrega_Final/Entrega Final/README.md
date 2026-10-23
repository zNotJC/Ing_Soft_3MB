# JOJOLOS — Sistema de Gestión de Laboratorios

## Estructura del proyecto

- `baseDeDatos/DDL/`: definición de tablas e instalación desde cero.
- `baseDeDatos/DML/`: inserciones y operaciones de modificación de datos de prueba.
- `baseDeDatos/DQL/`: consultas SQL de recuperación de información.
- `docs/`: documentación de apoyo y materiales de la tercera entrega.
- `proyecto/app/config/`: conexión y configuración de la aplicación.
- `proyecto/app/controllers/`: controladores PHP, incluidos los endpoints de la API.
- `proyecto/app/models/`: acceso a datos y modelos.
- `proyecto/app/core/`: clases comunes para respuestas y control de API.
- `proyecto/app/routes/`: rutas de la API.
- `proyecto/app/views/`: vistas renderizadas por PHP.
- `proyecto/public/`: entrada web, recursos CSS/JavaScript y endpoints públicos de API.

## Instalación desde cero

1. Crear una base de datos vacía en MySQL.
2. Ejecutar `baseDeDatos/DDL/instalacionDesdeCero.sql`.
3. Configurar las variables de conexión en `proyecto/.env` (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`).
4. Verificar que PHP tenga habilitadas las extensiones PDO y PDO MySQL.
5. Instalar dependencias con Composer si no se distribuye `vendor/` en el entorno.
6. Configurar el servidor web para servir `proyecto/public/`. Si se accede por la carpeta `proyecto`, `proyecto/index.php` redirige a `public/index.php`.

## Cuenta inicial

- Usuario: `admin`
- Cédula: `69696969`
- Contraseña inicial: `clave123`

Cambiar la contraseña tras el primer ingreso. Esta cuenta se inserta por el script de instalación desde cero.

## Estados de trabajo

- Tickets, solicitudes y préstamos nuevos se registran como `En espera`.
- La prioridad inicial de tickets y préstamos es `-` hasta que el Técnico la establezca durante la revisión.
- Las transiciones disponibles dependen del rol y de las validaciones del endpoint correspondiente.

## API

Los endpoints se encuentran en `proyecto/public/api/` y responden JSON con `status`, `message` y `data`. Los métodos HTTP utilizados para operaciones CRUD son GET, POST, PUT y DELETE según el recurso.
