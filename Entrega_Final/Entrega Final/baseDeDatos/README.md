# Organización de los scripts SQL

Esta organización sigue la separación usada en el ejemplo de referencia del profesor, adaptada a JOJOLOS.

## DDL — definición de datos
Contiene instrucciones que crean la base y las tablas. Para instalar JOJOLOS en una computadora nueva, ejecutar únicamente `DDL/instalacionDesdeCero.sql` sobre una base vacía.

## DML — manipulación de datos
Contiene instrucciones para insertar o modificar registros. Los scripts de esta carpeta no son necesarios para crear la estructura. Revisar su contenido antes de ejecutarlos, porque pueden insertar datos de ejemplo o modificar registros.

## DQL — consultas de datos
Contiene consultas `SELECT` utilizadas para consultar información y documentar las operaciones de lectura.

## Precauciones
- No ejecutar el instalador sobre una base con datos existentes.
- No ejecutar todos los archivos DDL indiscriminadamente: algunos crean tablas que ya están definidas en el instalador integrado.
- No incluir contraseñas reales ni datos personales en archivos de ejemplo.
- Los scripts deben corresponder al esquema utilizado por los modelos y controladores actuales.
