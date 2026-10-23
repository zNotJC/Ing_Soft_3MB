INSERT INTO estado_prestamo
(
    nombre
)
SELECT
    'Pendiente'
WHERE NOT EXISTS
(
    SELECT 1
    FROM estado_prestamo
    WHERE nombre = 'Pendiente'
);

INSERT INTO estado_prestamo
(
    nombre
)
SELECT
    'Activo'
WHERE NOT EXISTS
(
    SELECT 1
    FROM estado_prestamo
    WHERE nombre = 'Activo'
);

INSERT INTO estado_prestamo
(
    nombre
)
SELECT
    'Cancelado'
WHERE NOT EXISTS
(
    SELECT 1
    FROM estado_prestamo
    WHERE nombre = 'Cancelado'
);

INSERT INTO equipo
(
    nombre,
    numeroSerie,
    idLugar
)
SELECT
    'Laptop Lenovo',
    'LEN-001',
    idLugar
FROM lugar
WHERE NOT EXISTS
(
    SELECT 1
    FROM equipo
    WHERE numeroSerie = 'LEN-001'
)
LIMIT 1;

INSERT INTO equipo
(
    nombre,
    numeroSerie,
    idLugar
)
SELECT
    'Laptop HP',
    'HP-001',
    idLugar
FROM lugar
WHERE NOT EXISTS
(
    SELECT 1
    FROM equipo
    WHERE numeroSerie = 'HP-001'
)
LIMIT 1;

INSERT INTO equipo
(
    nombre,
    numeroSerie,
    idLugar
)
SELECT
    'Laptop Dell',
    'DEL-001',
    idLugar
FROM lugar
WHERE NOT EXISTS
(
    SELECT 1
    FROM equipo
    WHERE numeroSerie = 'DEL-001'
)
LIMIT 1;

SELECT
    idEstado,
    nombre
FROM estado_prestamo
ORDER BY idEstado;

SELECT
    idEquipo,
    nombre,
    numeroSerie,
    idLugar
FROM equipo
ORDER BY idEquipo;
INSERT INTO estado_prestamo (nombre)
SELECT 'Devuelto'
WHERE NOT EXISTS (SELECT 1 FROM estado_prestamo WHERE nombre = 'Devuelto');
