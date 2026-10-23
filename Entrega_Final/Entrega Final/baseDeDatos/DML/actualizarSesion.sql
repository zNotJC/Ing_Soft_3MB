-- Activar o cerrar la sesión de una cuenta.
-- Antes de ejecutar, define @cedula y @sesionActiva: 1 para iniciar,
-- 0 para cerrar. Para iniciar y registrar el acceso, define también @rol
-- ('Administrador', 'Docente' o 'Tecnico').

UPDATE usuario
SET sesionActiva = @sesionActiva
WHERE cedula = @cedula
  AND activo = TRUE
  AND @sesionActiva IN (0, 1)
  AND (
      @sesionActiva = 0
      OR (
          (@rol = 'Administrador' AND EXISTS (
              SELECT 1 FROM administrador AS a WHERE a.cedula = @cedula
          ))
          OR (@rol = 'Docente' AND EXISTS (
              SELECT 1 FROM docente AS d WHERE d.cedula = @cedula
          ))
          OR (@rol = 'Tecnico' AND EXISTS (
              SELECT 1 FROM tecnico AS t WHERE t.cedula = @cedula
          ))
      )
  );

INSERT INTO acceso_sistema (cedulaUsuario, rol)
SELECT u.cedula, @rol
FROM usuario AS u
WHERE u.cedula = @cedula
  AND u.activo = TRUE
  AND @sesionActiva = 1
  AND (
      (@rol = 'Administrador' AND EXISTS (
          SELECT 1 FROM administrador AS a WHERE a.cedula = u.cedula
      ))
      OR (@rol = 'Docente' AND EXISTS (
          SELECT 1 FROM docente AS d WHERE d.cedula = u.cedula
      ))
      OR (@rol = 'Tecnico' AND EXISTS (
          SELECT 1 FROM tecnico AS t WHERE t.cedula = u.cedula
      ))
  );

SELECT cedula, sesionActiva
FROM usuario
WHERE cedula = @cedula;
