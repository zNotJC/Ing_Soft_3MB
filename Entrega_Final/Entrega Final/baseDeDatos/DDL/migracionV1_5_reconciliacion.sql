SET @db_actual = DATABASE();

SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND CONSTRAINT_NAME='chk_equipo_estado' AND CONSTRAINT_TYPE='CHECK')>0, 'ALTER TABLE equipo DROP CHECK chk_equipo_estado', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND CONSTRAINT_NAME='chk_solicitud_tipo' AND CONSTRAINT_TYPE='CHECK')>0, 'ALTER TABLE solicitud DROP CHECK chk_solicitud_tipo', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND CONSTRAINT_NAME='chk_ticket_estado' AND CONSTRAINT_TYPE='CHECK')>0, 'ALTER TABLE ticket DROP CHECK chk_ticket_estado', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND CONSTRAINT_NAME='chk_ticket_prioridad' AND CONSTRAINT_TYPE='CHECK')>0, 'ALTER TABLE ticket DROP CHECK chk_ticket_prioridad', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND CONSTRAINT_NAME='chk_prestamo_estado' AND CONSTRAINT_TYPE='CHECK')>0, 'ALTER TABLE prestamo DROP CHECK chk_prestamo_estado', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND CONSTRAINT_NAME='chk_prestamo_prioridad' AND CONSTRAINT_TYPE='CHECK')>0, 'ALTER TABLE prestamo DROP CHECK chk_prestamo_prioridad', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @tiene_lugar = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='lugar');
SET @tiene_lab = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='laboratorio');
SET @sql = IF(@tiene_lugar=1 AND @tiene_lab=0, 'RENAME TABLE lugar TO laboratorio', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
CREATE TABLE IF NOT EXISTS laboratorio (
  idLaboratorio INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  PRIMARY KEY (idLaboratorio),
  UNIQUE KEY uq_laboratorio_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS equipo (
  idEquipo INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  idLaboratorio INT NULL,
  tipo VARCHAR(50) NOT NULL DEFAULT 'Ordenadores',
  estado VARCHAR(30) NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (idEquipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ticket (
  idTicket INT NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(150) NOT NULL,
  descripcion TEXT NOT NULL,
  fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fechaLimite DATE NOT NULL,
  cedulaSolicitante CHAR(8) NULL,
  idLaboratorio INT NULL,
  prioridad VARCHAR(20) NOT NULL DEFAULT 'Media',
  estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (idTicket)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS prestamo (
  idPrestamo INT NOT NULL AUTO_INCREMENT,
  idEquipo INT NULL,
  cedulaUsuario CHAR(8) NULL,
  fechaPrestamo DATE NOT NULL,
  fechaDevolucion DATE NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
  prioridad VARCHAR(20) NOT NULL DEFAULT 'Media',
  PRIMARY KEY (idPrestamo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS solicitud (
  idSolicitud INT NOT NULL AUTO_INCREMENT,
  cedulaUsuario CHAR(8) NOT NULL,
  tipoSolicitud VARCHAR(30) NOT NULL DEFAULT 'Reparación',
  descripcion VARCHAR(500) NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  equipo VARCHAR(100) NULL,
  fechaSolicitud DATE NOT NULL,
  fechaNecesaria DATE NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
  respuesta VARCHAR(500) NULL,
  PRIMARY KEY (idSolicitud)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLugar' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NOT NULL, CONCAT('ALTER TABLE equipo DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLugar')=1 AND (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLaboratorio')=0, 'ALTER TABLE equipo CHANGE COLUMN idLugar idLaboratorio INT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLugar' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NOT NULL, CONCAT('ALTER TABLE ticket DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLugar')=1 AND (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLaboratorio')=0, 'ALTER TABLE ticket CHANGE COLUMN idLugar idLaboratorio INT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLaboratorio')=0, 'ALTER TABLE equipo ADD COLUMN idLaboratorio INT NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='tipo')=0, "ALTER TABLE equipo ADD COLUMN tipo VARCHAR(50) NOT NULL DEFAULT 'Ordenadores'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='estado')=0, "ALTER TABLE equipo ADD COLUMN estado VARCHAR(30) NOT NULL DEFAULT 'Disponible'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='nombre')=0, "ALTER TABLE equipo ADD COLUMN nombre VARCHAR(100) NOT NULL DEFAULT 'Equipo sin nombre'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO laboratorio (nombre) SELECT 'Laboratorio general' WHERE NOT EXISTS (SELECT 1 FROM laboratorio WHERE nombre='Laboratorio general');
SET @id_lab_general = (SELECT idLaboratorio FROM laboratorio WHERE nombre='Laboratorio general' ORDER BY idLaboratorio LIMIT 1);
UPDATE equipo e LEFT JOIN laboratorio l ON l.idLaboratorio=e.idLaboratorio SET e.idLaboratorio=@id_lab_general WHERE e.idLaboratorio IS NULL OR l.idLaboratorio IS NULL;
ALTER TABLE equipo MODIFY idLaboratorio INT NOT NULL;
UPDATE equipo SET tipo='Ordenadores' WHERE tipo IS NULL OR tipo='' OR tipo='General';
UPDATE equipo SET tipo='Ordenadores' WHERE LOWER(tipo) LIKE '%laptop%' OR LOWER(tipo) LIKE '%ordenador%' OR LOWER(tipo) LIKE '%computador%' OR LOWER(tipo)='pc';
UPDATE equipo SET tipo='Periféricos' WHERE LOWER(tipo) LIKE '%mouse%' OR LOWER(tipo) LIKE '%ratón%' OR LOWER(tipo) LIKE '%teclado%' OR LOWER(tipo) LIKE '%monitor%' OR LOWER(tipo) LIKE '%perif%';
UPDATE equipo SET tipo='Televisores' WHERE LOWER(tipo) LIKE '%televisor%' OR LOWER(tipo) LIKE '%television%' OR LOWER(tipo)='tv';
UPDATE equipo SET tipo='Aires Acondicionados' WHERE LOWER(tipo) LIKE '%aire%' OR LOWER(tipo) LIKE '%acondicionado%' OR LOWER(tipo) LIKE '%control de aire%';
UPDATE equipo SET tipo='Ordenadores' WHERE tipo NOT IN ('Ordenadores','Periféricos','Televisores','Aires Acondicionados');
UPDATE equipo SET estado=CASE estado WHEN 'Activo' THEN 'Disponible' WHEN 'En reparación' THEN 'Roto' WHEN 'De baja' THEN 'No disponible' ELSE estado END;
UPDATE equipo SET estado='Disponible' WHERE estado IS NULL OR estado NOT IN ('Disponible','No disponible','Roto','En uso');

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='titulo')=0, "ALTER TABLE ticket ADD COLUMN titulo VARCHAR(150) NOT NULL DEFAULT 'Incidencia'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='descripcion')=0, "ALTER TABLE ticket ADD COLUMN descripcion TEXT NULL", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='fechaRegistro')=0, 'ALTER TABLE ticket ADD COLUMN fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='fechaLimite')=0, 'ALTER TABLE ticket ADD COLUMN fechaLimite DATE NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='cedulaSolicitante')=0 AND (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='cedulaUsuario')=1, 'ALTER TABLE ticket CHANGE COLUMN cedulaUsuario cedulaSolicitante CHAR(8) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='cedulaSolicitante')=0, 'ALTER TABLE ticket ADD COLUMN cedulaSolicitante CHAR(8) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLaboratorio')=0, 'ALTER TABLE ticket ADD COLUMN idLaboratorio INT NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='prioridad')=0, "ALTER TABLE ticket ADD COLUMN prioridad VARCHAR(20) NOT NULL DEFAULT 'Media'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='estado')=0, "ALTER TABLE ticket ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idEstado')=1 AND (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='estado_ticket')=1, 'UPDATE ticket t JOIN estado_ticket e ON e.idEstado=t.idEstado SET t.estado=e.nombre', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idPrioridad')=1 AND (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prioridad')=1, 'UPDATE ticket t JOIN prioridad p ON p.idPrioridad=t.idPrioridad SET t.prioridad=p.nombre', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='cedulaUsuario')=0 AND (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='cedulaSolicitante')=1, 'ALTER TABLE solicitud CHANGE COLUMN cedulaSolicitante cedulaUsuario CHAR(8) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='cedulaUsuario')=0, 'ALTER TABLE solicitud ADD COLUMN cedulaUsuario CHAR(8) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='tipoSolicitud')=0, "ALTER TABLE solicitud ADD COLUMN tipoSolicitud VARCHAR(30) NOT NULL DEFAULT 'Reparación'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='descripcion')=0, "ALTER TABLE solicitud ADD COLUMN descripcion VARCHAR(500) NOT NULL DEFAULT ''", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='cantidad')=0, 'ALTER TABLE solicitud ADD COLUMN cantidad INT NOT NULL DEFAULT 1', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='equipo')=0, 'ALTER TABLE solicitud ADD COLUMN equipo VARCHAR(100) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='fechaSolicitud')=0, 'ALTER TABLE solicitud ADD COLUMN fechaSolicitud DATE NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='fechaNecesaria')=0, 'ALTER TABLE solicitud ADD COLUMN fechaNecesaria DATE NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='estado')=0, "ALTER TABLE solicitud ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND COLUMN_NAME='respuesta')=0, 'ALTER TABLE solicitud ADD COLUMN respuesta VARCHAR(500) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE solicitud SET fechaSolicitud=CURDATE() WHERE fechaSolicitud IS NULL;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEquipo')=0, 'ALTER TABLE prestamo ADD COLUMN idEquipo INT NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='cedulaUsuario')=0 AND (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='cedulaSolicitante')=1, 'ALTER TABLE prestamo CHANGE COLUMN cedulaSolicitante cedulaUsuario CHAR(8) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='cedulaUsuario')=0, 'ALTER TABLE prestamo ADD COLUMN cedulaUsuario CHAR(8) NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='fechaPrestamo')=0, 'ALTER TABLE prestamo ADD COLUMN fechaPrestamo DATE NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='fechaDevolucion')=0 AND (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='fechaEsperada')=1, 'ALTER TABLE prestamo CHANGE COLUMN fechaEsperada fechaDevolucion DATE NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='fechaDevolucion')=0, 'ALTER TABLE prestamo ADD COLUMN fechaDevolucion DATE NULL', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='estado')=0, "ALTER TABLE prestamo ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='prioridad')=0, "ALTER TABLE prestamo ADD COLUMN prioridad VARCHAR(20) NOT NULL DEFAULT 'Media'", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEstado')=1 AND (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='estado_prestamo')=1, 'UPDATE prestamo p JOIN estado_prestamo e ON e.idEstado=p.idEstado SET p.estado=e.nombre', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idPrioridad')=1 AND (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prioridad')=1, 'UPDATE prestamo p JOIN prioridad pr ON pr.idPrioridad=p.idPrioridad SET p.prioridad=pr.nombre', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idEstado' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NOT NULL, CONCAT('ALTER TABLE ticket DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'), 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idPrioridad' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NOT NULL, CONCAT('ALTER TABLE ticket DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'), 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEstado' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NOT NULL, CONCAT('ALTER TABLE prestamo DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'), 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idPrioridad' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NOT NULL, CONCAT('ALTER TABLE prestamo DROP FOREIGN KEY `', REPLACE(@fk,'`','``'), '`'), 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idEstado')=1, 'ALTER TABLE ticket DROP COLUMN idEstado', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idPrioridad')=1, 'ALTER TABLE ticket DROP COLUMN idPrioridad', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idEstado')=1, 'ALTER TABLE prestamo DROP COLUMN idEstado', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND COLUMN_NAME='idPrioridad')=1, 'ALTER TABLE prestamo DROP COLUMN idPrioridad', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE ticket t LEFT JOIN laboratorio l ON l.idLaboratorio=t.idLaboratorio SET t.idLaboratorio=@id_lab_general WHERE t.idLaboratorio IS NULL OR l.idLaboratorio IS NULL;
UPDATE ticket SET fechaLimite=CURDATE() WHERE fechaLimite IS NULL;
UPDATE ticket SET descripcion='' WHERE descripcion IS NULL;
UPDATE ticket SET prioridad='Media' WHERE prioridad IS NULL OR prioridad NOT IN ('Baja','Media','Alta');
UPDATE ticket SET estado='Pendiente' WHERE estado IS NULL OR estado NOT IN ('Pendiente','En Proceso','Resuelto','Cancelado');
UPDATE prestamo p LEFT JOIN equipo e ON e.idEquipo=p.idEquipo SET p.idEquipo=NULL WHERE p.idEquipo IS NOT NULL AND e.idEquipo IS NULL;
UPDATE prestamo SET fechaPrestamo=CURDATE() WHERE fechaPrestamo IS NULL;
UPDATE prestamo SET fechaDevolucion=fechaPrestamo WHERE fechaDevolucion IS NULL;
UPDATE prestamo SET prioridad='Media' WHERE prioridad IS NULL OR prioridad NOT IN ('Baja','Media','Alta');
UPDATE prestamo SET estado='Pendiente' WHERE estado IS NULL OR estado NOT IN ('Pendiente','Activo','Cancelado','Devuelto');

UPDATE solicitud SET tipoSolicitud='Reparación' WHERE tipoSolicitud NOT IN ('Laboratorio','Reparación') OR tipoSolicitud IS NULL;
UPDATE solicitud SET estado='Pendiente' WHERE estado IS NULL OR estado NOT IN ('Pendiente','Aprobada','Rechazada','Cancelada');
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND CONSTRAINT_NAME='chk_solicitud_tipo' AND CONSTRAINT_TYPE='CHECK')=0, "ALTER TABLE solicitud ADD CONSTRAINT chk_solicitud_tipo CHECK (tipoSolicitud IN ('Laboratorio','Reparación'))", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND CONSTRAINT_NAME='chk_equipo_estado' AND CONSTRAINT_TYPE='CHECK')=0, "ALTER TABLE equipo ADD CONSTRAINT chk_equipo_estado CHECK (estado IN ('Disponible','No disponible','Roto','En uso'))", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND CONSTRAINT_NAME='chk_ticket_estado' AND CONSTRAINT_TYPE='CHECK')=0, "ALTER TABLE ticket ADD CONSTRAINT chk_ticket_estado CHECK (estado IN ('Pendiente','En Proceso','Resuelto','Cancelado'))", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND CONSTRAINT_NAME='chk_ticket_prioridad' AND CONSTRAINT_TYPE='CHECK')=0, "ALTER TABLE ticket ADD CONSTRAINT chk_ticket_prioridad CHECK (prioridad IN ('Baja','Media','Alta'))", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND CONSTRAINT_NAME='chk_prestamo_estado' AND CONSTRAINT_TYPE='CHECK')=0, "ALTER TABLE prestamo ADD CONSTRAINT chk_prestamo_estado CHECK (estado IN ('Pendiente','Activo','Cancelado','Devuelto'))", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='prestamo' AND CONSTRAINT_NAME='chk_prestamo_prioridad' AND CONSTRAINT_TYPE='CHECK')=0, "ALTER TABLE prestamo ADD CONSTRAINT chk_prestamo_prioridad CHECK (prioridad IN ('Baja','Media','Alta'))", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx = (SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='numeroSerie' AND NON_UNIQUE=0 LIMIT 1);
SET @sql = IF(@idx IS NOT NULL AND @idx <> 'PRIMARY', CONCAT('ALTER TABLE equipo DROP INDEX `', REPLACE(@idx,'`','``'), '`'), 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='idLaboratorio' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL AND (SELECT COUNT(*) FROM equipo e LEFT JOIN laboratorio l ON l.idLaboratorio=e.idLaboratorio WHERE l.idLaboratorio IS NULL)=0, 'ALTER TABLE equipo ADD CONSTRAINT fk_equipo_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio)', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @fk = (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='ticket' AND COLUMN_NAME='idLaboratorio' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql = IF(@fk IS NULL AND (SELECT COUNT(*) FROM ticket t LEFT JOIN laboratorio l ON l.idLaboratorio=t.idLaboratorio WHERE l.idLaboratorio IS NULL)=0, 'ALTER TABLE ticket ADD CONSTRAINT fk_ticket_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio)', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='numeroSerie')=1, 'ALTER TABLE equipo DROP COLUMN numeroSerie', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='marca')=1, 'ALTER TABLE equipo DROP COLUMN marca', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='modelo')=1, 'ALTER TABLE equipo DROP COLUMN modelo', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
DROP TABLE IF EXISTS estado_ticket;
DROP TABLE IF EXISTS estado_prestamo;
DROP TABLE IF EXISTS prioridad;

