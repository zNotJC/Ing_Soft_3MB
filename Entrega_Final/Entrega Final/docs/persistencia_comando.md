# Persistencia y comandos

La persistencia se implementa con PDO y consultas preparadas. Los modelos de acceso a datos ejecutan consultas SQL; los controladores validan la petición y delegan las operaciones al modelo.

## Instalación

Ejecutar `baseDeDatos/DDL/instalacionDesdeCero.sql` sobre una base vacía y configurar las variables de conexión en `proyecto/.env`. Para una base v1.24 existente, consultar la sección opcional de actualización al final del mismo SQL y ejecutar únicamente su instrucción `ALTER TABLE`, nunca el instalador completo.

## API

Los recursos se enrutan mediante `proyecto/public/index.php?api=<recurso>`. `proyecto/app/rutas/api.php` deriva cada petición al controlador correspondiente, ubicado directamente en `proyecto/app/controladores/`. Se utilizan los métodos HTTP apropiados y respuestas JSON con estado, mensaje y datos.
