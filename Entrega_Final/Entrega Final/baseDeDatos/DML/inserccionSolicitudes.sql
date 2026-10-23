-- Crear una solicitud usando datos reales.
-- Antes de ejecutar, define en esta sesión MySQL:
-- @cedulaUsuario, @tipoSolicitud ('Laboratorio' o 'Reparación'), @descripcion,
-- @equipo (opcional) y @fechaNecesaria (requerida para solicitudes de Laboratorio).
-- La cantidad se fija en 1 porque el docente no la completa en la aplicación.

INSERT INTO solicitud (
    cedulaUsuario,
    tipoSolicitud,
    descripcion,
    cantidad,
    equipo,
    fechaSolicitud,
    fechaNecesaria,
    estado
)
SELECT
    @cedulaUsuario,
    @tipoSolicitud,
    @descripcion,
    1,
    NULLIF(@equipo, ''),
    CURRENT_DATE(),
    @fechaNecesaria,
    'En espera'
WHERE @cedulaUsuario IS NOT NULL
  AND @tipoSolicitud IN ('Laboratorio', 'Reparación')
  AND NULLIF(TRIM(@descripcion), '') IS NOT NULL
  AND (@tipoSolicitud <> 'Laboratorio' OR @fechaNecesaria IS NOT NULL);

SELECT ROW_COUNT() AS solicitudes_creadas;
