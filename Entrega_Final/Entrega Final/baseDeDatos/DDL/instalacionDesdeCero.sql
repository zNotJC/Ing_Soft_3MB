CREATE DATABASE IF NOT EXISTS jojolos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jojolos;

CREATE TABLE usuario (
  cedula CHAR(8) NOT NULL,
  usuario VARCHAR(20) NOT NULL UNIQUE,
  correo VARCHAR(120) NOT NULL UNIQUE,
  claveHash VARCHAR(255) NOT NULL,
  activo BOOLEAN NOT NULL DEFAULT TRUE,
  sesionActiva BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (cedula)
) ENGINE=InnoDB;

CREATE TABLE administrador (
  cedula CHAR(8) NOT NULL PRIMARY KEY,
  CONSTRAINT fk_administrador_usuario FOREIGN KEY (cedula) REFERENCES usuario(cedula)
) ENGINE=InnoDB;
CREATE TABLE docente (
  cedula CHAR(8) NOT NULL PRIMARY KEY,
  CONSTRAINT fk_docente_usuario FOREIGN KEY (cedula) REFERENCES usuario(cedula)
) ENGINE=InnoDB;
CREATE TABLE tecnico (
  cedula CHAR(8) NOT NULL PRIMARY KEY,
  CONSTRAINT fk_tecnico_usuario FOREIGN KEY (cedula) REFERENCES usuario(cedula)
) ENGINE=InnoDB;
CREATE TABLE acceso_sistema (
  idAcceso INT AUTO_INCREMENT PRIMARY KEY,
  cedulaUsuario CHAR(8) NOT NULL,
  rol VARCHAR(20) NOT NULL,
  fechaHora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_acceso_usuario FOREIGN KEY (cedulaUsuario) REFERENCES usuario(cedula),
  CONSTRAINT chk_acceso_rol CHECK (rol IN ('Administrador','Docente','Tecnico'))
) ENGINE=InnoDB;

CREATE TABLE laboratorio (
  idLaboratorio INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  estado VARCHAR(30) NOT NULL DEFAULT 'Disponible',
  CONSTRAINT chk_laboratorio_estado CHECK (estado IN ('Disponible','En uso'))
) ENGINE=InnoDB;
CREATE TABLE equipo (
  idEquipo INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  idLaboratorio INT NULL,
  tipo VARCHAR(50) NOT NULL DEFAULT 'Ordenadores',
  estado VARCHAR(30) NOT NULL DEFAULT 'Disponible',
  CONSTRAINT fk_equipo_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio) ON DELETE SET NULL,
  CONSTRAINT chk_equipo_estado CHECK (estado IN ('Disponible','En uso','Roto'))
) ENGINE=InnoDB;

CREATE TABLE ticket (
  idTicket INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  descripcion TEXT NOT NULL,
  fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fechaLimite DATE NULL,
  cedulaSolicitante CHAR(8) NOT NULL,
  idLaboratorio INT NOT NULL,
  idEquipo INT NOT NULL,
  prioridad VARCHAR(20) NOT NULL DEFAULT '-',
  estado VARCHAR(20) NOT NULL DEFAULT 'En espera',
  CONSTRAINT fk_ticket_solicitante FOREIGN KEY (cedulaSolicitante) REFERENCES usuario(cedula),
  CONSTRAINT fk_ticket_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio),
  CONSTRAINT fk_ticket_equipo FOREIGN KEY (idEquipo) REFERENCES equipo(idEquipo) ON DELETE SET NULL,
  CONSTRAINT chk_ticket_prioridad CHECK (prioridad IN ('-','Baja','Media','Alta')),
  CONSTRAINT chk_ticket_estado CHECK (estado IN ('En espera','Pendiente','En Proceso','Resuelto','Cancelado','Denegado'))
) ENGINE=InnoDB;

CREATE TABLE solicitud (
  idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
  cedulaUsuario CHAR(8) NOT NULL,
  tipoSolicitud VARCHAR(30) NOT NULL,
  descripcion VARCHAR(500) NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  equipo VARCHAR(100) NULL,
  idLaboratorio INT NULL,
  idEquipo INT NULL,
  fechaSolicitud DATE NOT NULL DEFAULT (CURRENT_DATE),
  fechaNecesaria DATE NULL,
  fechaInicio DATE NULL,
  fechaFin DATE NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'En espera',
  respuesta VARCHAR(500) NULL,
  CONSTRAINT fk_solicitud_usuario FOREIGN KEY (cedulaUsuario) REFERENCES usuario(cedula),
  CONSTRAINT fk_solicitud_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio),
  CONSTRAINT fk_solicitud_equipo FOREIGN KEY (idEquipo) REFERENCES equipo(idEquipo),
  CONSTRAINT chk_solicitud_tipo CHECK (tipoSolicitud IN ('Laboratorio','Reparación')),
  CONSTRAINT chk_solicitud_cantidad CHECK (cantidad > 0),
  CONSTRAINT chk_solicitud_estado CHECK (estado IN ('En espera','Pendiente','Aprobada','Rechazada','Cancelada','Denegada'))
) ENGINE=InnoDB;

CREATE TABLE prestamo (
  idPrestamo INT AUTO_INCREMENT PRIMARY KEY,
  idEquipo INT NOT NULL,
  idLaboratorio INT NULL,
  cedulaUsuario CHAR(8) NOT NULL,
  fechaPrestamo DATE NOT NULL,
  fechaDevolucion DATE NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'En espera',
  prioridad VARCHAR(20) NOT NULL DEFAULT '-',
  CONSTRAINT fk_prestamo_equipo FOREIGN KEY (idEquipo) REFERENCES equipo(idEquipo),
  CONSTRAINT fk_prestamo_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES laboratorio(idLaboratorio),
  CONSTRAINT fk_prestamo_usuario FOREIGN KEY (cedulaUsuario) REFERENCES usuario(cedula),
  CONSTRAINT chk_prestamo_estado CHECK (estado IN ('En espera','Pendiente','Aceptado','Activo','Cancelado','Devuelto','Denegado')),

  CONSTRAINT chk_prestamo_prioridad CHECK (prioridad IN ('-','Baja','Media','Alta'))
) ENGINE=InnoDB;



-- Usuarios iniciales para los tres roles (contraseña: clave123)
INSERT INTO usuario (cedula, usuario, correo, claveHash, activo, sesionActiva) VALUES
('69696969', 'admin', 'admin@jojolos.com', '$2y$12$dkc5b8xthsFX/kdQ2zBXcONZKFG/0Gm4O94tGEVYTYgPwcT9XFYlK', 1, 0),
('65656565', 'técnico', 'tecnico@jojolos.com', '$2y$12$dkc5b8xthsFX/kdQ2zBXcONZKFG/0Gm4O94tGEVYTYgPwcT9XFYlK', 1, 0),
('63636363', 'docente', 'docente@jojolos.com', '$2y$12$dkc5b8xthsFX/kdQ2zBXcONZKFG/0Gm4O94tGEVYTYgPwcT9XFYlK', 1, 0);

INSERT INTO administrador (cedula) VALUES ('69696969');
INSERT INTO tecnico (cedula) VALUES ('65656565');
INSERT INTO docente (cedula) VALUES ('63636363');

-- Laboratorio y equipos iniciales disponibles
INSERT INTO laboratorio (nombre, estado) VALUES ('Laboratorio 1', 'Disponible');
INSERT INTO equipo (nombre, idLaboratorio, tipo, estado) VALUES
('PC1', 1, 'Ordenadores', 'Disponible'),
('Periférico 1', 1, 'Periféricos', 'Disponible'),
('Televisor 1', 1, 'Televisores', 'Disponible'),
('Aire acondicionado 1', 1, 'Aires Acondicionados', 'Disponible');
