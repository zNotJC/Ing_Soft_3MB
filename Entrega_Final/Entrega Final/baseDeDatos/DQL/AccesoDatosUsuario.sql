SELECT
    u.cedula,
    u.usuario,
    u.correo,
    u.claveHash,
    u.activo,
    u.sesionActiva,
    CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
    CASE WHEN d.cedula IS NOT NULL THEN 1 ELSE 0 END AS docente
FROM usuario AS u
LEFT JOIN administrador AS a
    ON a.cedula = u.cedula
LEFT JOIN docente AS d
    ON d.cedula = u.cedula
WHERE u.cedula = :cedula;

UPDATE usuario
SET sesionActiva = TRUE
WHERE cedula = :cedula;

UPDATE usuario
SET sesionActiva = FALSE
WHERE cedula = :cedula;

SELECT
    a.fechaHora,
    u.usuario,
    a.rol
FROM acceso_sistema AS a
INNER JOIN usuario AS u
    ON u.cedula = a.cedulaUsuario
ORDER BY a.fechaHora DESC
LIMIT 10;
