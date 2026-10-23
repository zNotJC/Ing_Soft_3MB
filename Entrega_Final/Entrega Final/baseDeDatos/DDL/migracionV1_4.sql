-- JOJOLOS v1.4: migración de inventario y tipos de solicitudes.
-- Realizar copia de seguridad antes de ejecutar. Ejecutar sobre la base jojolos.
SET @db_actual = DATABASE();

SET @checkname = (SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND CONSTRAINT_TYPE='CHECK' AND CONSTRAINT_NAME='chk_equipo_estado' LIMIT 1);
SET @sql = IF(@checkname IS NOT NULL, 'ALTER TABLE equipo DROP CHECK chk_equipo_estado', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Equipos: conservar nombres y relaciones, retirar atributos que ya no se usan.
SET @fkname = (SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='numeroSerie' AND NON_UNIQUE=0 LIMIT 1);
SET @sql = IF(@fkname IS NOT NULL AND @fkname <> 'PRIMARY', CONCAT('ALTER TABLE equipo DROP INDEX `', REPLACE(@fkname,'`','``'), '`'), 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='numeroSerie')=1, 'ALTER TABLE equipo DROP COLUMN numeroSerie', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='marca')=1, 'ALTER TABLE equipo DROP COLUMN marca', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db_actual AND TABLE_NAME='equipo' AND COLUMN_NAME='modelo')=1, 'ALTER TABLE equipo DROP COLUMN modelo', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
UPDATE equipo SET tipo='Ordenadores' WHERE tipo='General' OR tipo IS NULL OR tipo='';
UPDATE equipo SET tipo='Ordenadores' WHERE LOWER(tipo) LIKE '%laptop%' OR LOWER(tipo) LIKE '%ordenador%' OR LOWER(tipo) LIKE '%computador%' OR LOWER(tipo)='pc';
UPDATE equipo SET tipo='Periféricos' WHERE LOWER(tipo) LIKE '%mouse%' OR LOWER(tipo) LIKE '%ratón%' OR LOWER(tipo) LIKE '%teclado%' OR LOWER(tipo) LIKE '%monitor%' OR LOWER(tipo) LIKE '%perif%';
UPDATE equipo SET tipo='Televisores' WHERE LOWER(tipo) LIKE '%televisor%' OR LOWER(tipo) LIKE '%television%' OR LOWER(tipo)='tv';
UPDATE equipo SET tipo='Aires Acondicionados' WHERE LOWER(tipo) LIKE '%aire%' OR LOWER(tipo) LIKE '%acondicionado%' OR LOWER(tipo) LIKE '%control de aire%';
UPDATE equipo SET tipo='Ordenadores' WHERE tipo NOT IN ('Ordenadores','Periféricos','Televisores','Aires Acondicionados');
UPDATE equipo SET estado=CASE estado WHEN 'Activo' THEN 'Disponible' WHEN 'En reparación' THEN 'Roto' WHEN 'De baja' THEN 'No disponible' ELSE estado END;
ALTER TABLE equipo MODIFY tipo VARCHAR(50) NOT NULL DEFAULT 'Ordenadores';
ALTER TABLE equipo MODIFY estado VARCHAR(30) NOT NULL DEFAULT 'Disponible';

-- Solicitudes: solo los tipos de uso definidos para el proyecto.
UPDATE solicitud SET tipoSolicitud='Reparación' WHERE tipoSolicitud NOT IN ('Laboratorio','Reparación');
SET @checkname = (SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=@db_actual AND TABLE_NAME='solicitud' AND CONSTRAINT_TYPE='CHECK' AND CONSTRAINT_NAME='chk_solicitud_tipo' LIMIT 1);
SET @sql = IF(@checkname IS NOT NULL, 'ALTER TABLE solicitud DROP CHECK chk_solicitud_tipo', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
ALTER TABLE solicitud ADD CONSTRAINT chk_solicitud_tipo CHECK (tipoSolicitud IN ('Laboratorio','Reparación'));

-- En el inventario los estados tienen una semántica visible para el usuario.
ALTER TABLE equipo ADD CONSTRAINT chk_equipo_estado CHECK (estado IN ('Disponible','No disponible','Roto','En uso'));
