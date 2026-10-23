INSERT INTO EQUIPO
    (nombre, numeroSerie, idLaboratorio, tipo, marca, modelo, estado)
SELECT
    'Laptop Lenovo', 'LEN-001', idLaboratorio, 'Ordenador', 'Lenovo', 'No especificado', 'Activo'
FROM LABORATORIO
WHERE nombre = (SELECT MIN(nombre) FROM LABORATORIO)
  AND NOT EXISTS (SELECT 1 FROM EQUIPO WHERE numeroSerie = 'LEN-001')
LIMIT 1;

INSERT INTO EQUIPO
    (nombre, numeroSerie, idLaboratorio, tipo, marca, modelo, estado)
SELECT
    'Laptop HP', 'HP-001', idLaboratorio, 'Ordenador', 'HP', 'No especificado', 'Activo'
FROM LABORATORIO
WHERE nombre = (SELECT MIN(nombre) FROM LABORATORIO)
  AND NOT EXISTS (SELECT 1 FROM EQUIPO WHERE numeroSerie = 'HP-001')
LIMIT 1;

INSERT INTO EQUIPO
    (nombre, numeroSerie, idLaboratorio, tipo, marca, modelo, estado)
SELECT
    'Laptop Dell', 'DEL-001', idLaboratorio, 'Ordenador', 'Dell', 'No especificado', 'Activo'
FROM LABORATORIO
WHERE nombre = (SELECT MIN(nombre) FROM LABORATORIO)
  AND NOT EXISTS (SELECT 1 FROM EQUIPO WHERE numeroSerie = 'DEL-001')
LIMIT 1;

SELECT idEquipo, nombre, numeroSerie, idLaboratorio, tipo, marca, modelo, estado
FROM EQUIPO ORDER BY idEquipo;
