-- Crear una cuenta y asignarle un único rol.
-- Antes de ejecutar, define: @cedula, @usuario, @correo, @claveHash y @rol.
-- @rol debe ser 'Administrador', 'Docente' o 'Tecnico'.
-- @claveHash debe contener un hash generado con PHP password_hash(); nunca
-- guardes aquí una contraseña en texto plano.

SET @usuarioInsertado = 0;

START TRANSACTION;

INSERT INTO usuario (
    cedula,
    usuario,
    correo,
    claveHash,
    activo,
    sesionActiva
)
SELECT
    @cedula,
    @usuario,
    @correo,
    @claveHash,
    TRUE,
    FALSE
WHERE NULLIF(TRIM(@cedula), '') IS NOT NULL
  AND NULLIF(TRIM(@usuario), '') IS NOT NULL
  AND NULLIF(TRIM(@correo), '') IS NOT NULL
  AND NULLIF(@claveHash, '') IS NOT NULL
  AND @rol IN ('Administrador', 'Docente', 'Tecnico')
  AND NOT EXISTS (
      SELECT 1
      FROM usuario AS existente
      WHERE existente.cedula = @cedula
         OR existente.usuario = @usuario
         OR existente.correo = @correo
  );

SET @usuarioInsertado = ROW_COUNT();

INSERT INTO administrador (cedula)
SELECT @cedula
WHERE @usuarioInsertado = 1 AND @rol = 'Administrador';

INSERT INTO docente (cedula)
SELECT @cedula
WHERE @usuarioInsertado = 1 AND @rol = 'Docente';

INSERT INTO tecnico (cedula)
SELECT @cedula
WHERE @usuarioInsertado = 1 AND @rol = 'Tecnico';

COMMIT;

SELECT IF(@usuarioInsertado = 1, 'Usuario creado', 'No se creó: completa datos válidos y usa cédula, usuario y correo no registrados') AS resultado;
