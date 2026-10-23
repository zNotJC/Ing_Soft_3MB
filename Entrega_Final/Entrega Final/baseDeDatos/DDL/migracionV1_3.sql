
SET @db_actual = DATABASE();

CREATE TABLE IF NOT EXISTS TECNICO (
    cedula CHAR(8) NOT NULL,
    CONSTRAINT pk_tecnico PRIMARY KEY (cedula),
    CONSTRAINT fk_tecnico_usuario FOREIGN KEY (cedula) REFERENCES USUARIO (cedula)
);

SET @existe_check_rol = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @db_actual
      AND TABLE_NAME = 'ACCESO_SISTEMA'
      AND CONSTRAINT_NAME = 'chk_acceso_rol'
      AND CONSTRAINT_TYPE = 'CHECK'
);
SET @sql = IF(@existe_check_rol > 0, 'ALTER TABLE ACCESO_SISTEMA DROP CONSTRAINT chk_acceso_rol', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @existe_check_rol = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @db_actual
      AND TABLE_NAME = 'ACCESO_SISTEMA'
      AND CONSTRAINT_NAME = 'chk_acceso_rol'
      AND CONSTRAINT_TYPE = 'CHECK'
);
SET @sql = IF(@existe_check_rol = 0, "ALTER TABLE ACCESO_SISTEMA ADD CONSTRAINT chk_acceso_rol CHECK (rol IN ('Administrador', 'Docente', 'Tecnico'))", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


SET @tiene_lugar = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='lugar');
SET @tiene_laboratorio = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='laboratorio');
SET @sql = IF(@tiene_lugar=1 AND @tiene_laboratorio=0, 'RENAME TABLE lugar TO laboratorio', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
 WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLugar' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE equipo DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
 WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLugar' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE ticket DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @tiene_col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLugar');
SET @nueva_col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLaboratorio');
SET @sql = IF(@tiene_col=1 AND @nueva_col=0, 'ALTER TABLE equipo CHANGE COLUMN idLugar idLaboratorio INT NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @tiene_col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLugar');
SET @nueva_col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLaboratorio');
SET @sql = IF(@tiene_col=1 AND @nueva_col=0, 'ALTER TABLE ticket CHANGE COLUMN idLugar idLaboratorio INT NOT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND CONSTRAINT_NAME='fk_equipo_laboratorio');
SET @sql = IF(@fk=0, 'ALTER TABLE equipo ADD CONSTRAINT fk_equipo_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND CONSTRAINT_NAME='fk_ticket_laboratorio');
SET @sql = IF(@fk=0, 'ALTER TABLE ticket ADD CONSTRAINT fk_ticket_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='tipo')=0, "ALTER TABLE equipo ADD COLUMN tipo VARCHAR(50) NOT NULL DEFAULT 'General'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='marca')=0, 'ALTER TABLE equipo ADD COLUMN marca VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='modelo')=0, 'ALTER TABLE equipo ADD COLUMN modelo VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='estado')=0, "ALTER TABLE equipo ADD COLUMN estado VARCHAR(30) NOT NULL DEFAULT 'Activo'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='estado')=0, "ALTER TABLE ticket ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='prioridad')=0, "ALTER TABLE ticket ADD COLUMN prioridad VARCHAR(20) NOT NULL DEFAULT 'Media'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @tiene_tabla = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='estado_ticket');
SET @tiene_col_estado = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idEstado');
SET @sql = IF(@tiene_tabla=1 AND @tiene_col_estado=1, 'UPDATE ticket t JOIN estado_ticket e ON e.idEstado=t.idEstado SET t.estado=e.nombre', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @tiene_tabla = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prioridad');
SET @tiene_col_prioridad = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idPrioridad');
SET @sql = IF(@tiene_tabla=1 AND @tiene_col_prioridad=1, 'UPDATE ticket t JOIN prioridad p ON p.idPrioridad=t.idPrioridad SET t.prioridad=p.nombre', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='estado')=0, "ALTER TABLE prestamo ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='prioridad')=0, "ALTER TABLE prestamo ADD COLUMN prioridad VARCHAR(20) NOT NULL DEFAULT 'Media'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @tiene_tabla = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='estado_prestamo');
SET @tiene_col_estado = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEstado');
SET @sql = IF(@tiene_tabla=1 AND @tiene_col_estado=1, 'UPDATE prestamo p JOIN estado_prestamo e ON e.idEstado=p.idEstado SET p.estado=e.nombre', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idEstado' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE ticket DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idPrioridad' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE ticket DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idEstado')=1, 'ALTER TABLE ticket DROP COLUMN idEstado', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idPrioridad')=1, 'ALTER TABLE ticket DROP COLUMN idPrioridad', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEstado' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL, 'SELECT 1', CONCAT('ALTER TABLE prestamo DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEstado')=1, 'ALTER TABLE prestamo DROP COLUMN idEstado', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

DROP TABLE IF EXISTS estado_ticket;
DROP TABLE IF EXISTS estado_prestamo;
DROP TABLE IF EXISTS prioridad;
