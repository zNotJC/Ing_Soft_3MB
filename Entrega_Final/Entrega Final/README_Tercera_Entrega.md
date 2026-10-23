# JOJOLOS — Tercera entrega

## Preparación local con XAMPP

1. Inicia MySQL desde XAMPP.
2. En phpMyAdmin, confirma que existe la base de datos `jojolos`.
3. En `proyecto/.env`, configura la conexión a MySQL. El archivo se llama `.env`:
   - `DB_HOST=localhost`
   - `DB_PORT=3306`
   - `DB_NAME=jojolos`
   - `DB_USER=root`
   - `DB_PASS=` (vacío solo si tu usuario `root` no tiene contraseña).
4. Si la base de datos ya existía antes de agregar el rol Técnico, haz un respaldo desde phpMyAdmin y ejecuta una sola vez `baseDeDatos/DDL/migracionTecnicoYTickets.sql`, con `jojolos` seleccionada.
5. Si la base de datos es nueva, crea las tablas en el orden indicado en la documentación de persistencia y carga los DML de catálogos/datos de prueba.
6. Abre PowerShell en la carpeta `proyecto` y ejecuta:

   ```powershell
   php -S localhost:3000 -t public
   ```

7. Abre `http://localhost:3000/` o `http://localhost:3000/index.php`.

**Importante:** el servidor debe iniciarse con `public` como raíz web. Así las carpetas internas `app`, `models` y `controllers` no quedan expuestas por URL. No inicies el servidor desde la carpeta superior del repositorio sin `-t public`.

## Permisos principales

- **Administrador:** gestiona usuarios e inventario; consulta métricas y el estado de las incidencias. No gestiona solicitudes ni incidencias.
- **Técnico:** gestiona solicitudes e incidencias; visualiza el inventario sin modificarlo y consulta métricas.
- **Docente:** conserva sus funciones previas; no se amplían ni reducen sus permisos sin validar la lógica existente.

## API y seguridad

- Los endpoints públicos de `public/api` delegan el procesamiento a controladores.
- Las respuestas JSON utilizan `status`, `message` y `data`.
- Las consultas de datos deben utilizar sentencias preparadas.
- Los permisos se comprueban en el servidor, no solamente ocultando botones.
- No publiques el `.env` ni compartas credenciales reales.

## Verificación

La sintaxis de PHP puede revisarse ejecutando `php -l` sobre los archivos PHP. La integración completa debe probarse en el entorno local con la base de datos `jojolos`; no se considera validada hasta probar inicio de sesión, permisos y CRUD con datos reales.
