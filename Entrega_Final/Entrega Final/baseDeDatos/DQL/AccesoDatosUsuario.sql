-- Buscar una cuenta y determinar el rol mediante sus tablas de subtipo.
SELECT u.cedula, u.usuario, u.correo, u.claveHash, u.activo, u.sesionActiva,
       CASE WHEN a.cedula IS NOT NULL THEN 1 ELSE 0 END AS administrador,
       CASE WHEN d.cedula IS NOT NULL THEN 1 ELSE 0 END AS docente,
       CASE WHEN t.cedula IS NOT NULL THEN 1 ELSE 0 END AS tecnico
FROM usuario AS u
LEFT JOIN administrador AS a ON a.cedula = u.cedula
LEFT JOIN docente AS d ON d.cedula = u.cedula
LEFT JOIN tecnico AS t ON t.cedula = u.cedula
WHERE u.cedula = :cedula;
