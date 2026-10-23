-- Requiere que ya existan usuario, laboratorio y equipo.
-- Para instalar todo el esquema desde cero, usar instalacionDesdeCero.sql.
CREATE TABLE TICKET (
    idTicket INT NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaLimite DATE NULL,
    cedulaSolicitante CHAR(8) NOT NULL,
    idLaboratorio INT NOT NULL,
    idEquipo INT NOT NULL,
    prioridad VARCHAR(20) NOT NULL DEFAULT '-',
    estado VARCHAR(20) NOT NULL DEFAULT 'En espera',
    CONSTRAINT pk_ticket PRIMARY KEY (idTicket),
    CONSTRAINT fk_ticket_solicitante FOREIGN KEY (cedulaSolicitante) REFERENCES USUARIO (cedula),
    CONSTRAINT fk_ticket_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES LABORATORIO (idLaboratorio),
    CONSTRAINT fk_ticket_equipo FOREIGN KEY (idEquipo) REFERENCES EQUIPO (idEquipo) ON DELETE RESTRICT,
    CONSTRAINT chk_ticket_prioridad CHECK (prioridad IN ('-', 'Baja', 'Media', 'Alta')),
    CONSTRAINT chk_ticket_estado CHECK (estado IN ('En espera', 'Pendiente', 'En Proceso', 'Resuelto', 'Cancelado', 'Denegado'))
) ENGINE=InnoDB;
