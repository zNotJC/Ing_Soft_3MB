CREATE TABLE IF NOT EXISTS TECNICO (
    cedula CHAR(8) NOT NULL,
    CONSTRAINT pk_tecnico PRIMARY KEY (cedula),
    CONSTRAINT fk_tecnico_usuario FOREIGN KEY (cedula) REFERENCES USUARIO (cedula)
);

SET @existe_check_rol = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ACCESO_SISTEMA'
      AND CONSTRAINT_NAME = 'chk_acceso_rol'
      AND CONSTRAINT_TYPE = 'CHECK'
);
SET @sql_migracion = IF(
    @existe_check_rol > 0,
    'ALTER TABLE ACCESO_SISTEMA DROP CONSTRAINT chk_acceso_rol',
    'SELECT 1'
);
PREPARE stmt_migracion FROM @sql_migracion;
EXECUTE stmt_migracion;
DEALLOCATE PREPARE stmt_migracion;

ALTER TABLE ACCESO_SISTEMA
    ADD CONSTRAINT chk_acceso_rol
    CHECK (rol IN ('Administrador', 'Docente', 'Tecnico'));

SET @fk_ticket_actual = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'TICKET'
      AND COLUMN_NAME = 'cedulaSolicitante'
      AND REFERENCED_TABLE_NAME IS NOT NULL
    LIMIT 1
);
SET @sql_migracion = IF(
    @fk_ticket_actual IS NULL,
    'SELECT 1',
    CONCAT('ALTER TABLE TICKET DROP FOREIGN KEY `', REPLACE(@fk_ticket_actual, '`', '``'), '`')
);
PREPARE stmt_migracion FROM @sql_migracion;
EXECUTE stmt_migracion;
DEALLOCATE PREPARE stmt_migracion;

ALTER TABLE TICKET
    ADD CONSTRAINT fk_ticket_solicitante
    FOREIGN KEY (cedulaSolicitante) REFERENCES USUARIO (cedula);

SET @fk_solicitud_actual = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'solicitud'
      AND COLUMN_NAME = 'cedulaUsuario'
      AND REFERENCED_TABLE_NAME IS NOT NULL
    LIMIT 1
);
SET @sql_migracion = IF(
    @fk_solicitud_actual IS NULL,
    'SELECT 1',
    CONCAT('ALTER TABLE solicitud DROP FOREIGN KEY `', REPLACE(@fk_solicitud_actual, '`', '``'), '`')
);
PREPARE stmt_migracion FROM @sql_migracion;
EXECUTE stmt_migracion;
DEALLOCATE PREPARE stmt_migracion;

ALTER TABLE solicitud MODIFY cedulaUsuario CHAR(8) NOT NULL;

ALTER TABLE solicitud
    ADD CONSTRAINT fk_solicitud_usuario
    FOREIGN KEY (cedulaUsuario) REFERENCES USUARIO (cedula);

INSERT INTO estado_prestamo (nombre)
SELECT 'Devuelto'
WHERE NOT EXISTS (
    SELECT 1 FROM estado_prestamo WHERE nombre = 'Devuelto'
);
