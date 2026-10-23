# Persistencia y comandos

La persistencia se implementa con PDO y consultas preparadas. Los modelos de acceso a datos ejecutan consultas SQL; los controladores validan la petición y delegan las operaciones al modelo.

## Instalación

Ejecutar `baseDeDatos/DDL/instalacionDesdeCero.sql` sobre una base vacía y configurar las variables de conexión en `proyecto/.env`.

## API

Los endpoints se encuentran en `proyecto/public/api/` y utilizan los métodos HTTP correspondientes a cada operación. Las respuestas usan JSON con estado, mensaje y datos.
