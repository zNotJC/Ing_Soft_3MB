UPDATE usuario
SET sesionActiva = TRUE
WHERE cedula = :cedula;

UPDATE usuario
SET sesionActiva = FALSE
WHERE cedula = :cedula;