CREATE TABLE LABORATORIO (
    idLaboratorio INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    CONSTRAINT pk_laboratorio PRIMARY KEY (idLaboratorio),
    CONSTRAINT uq_laboratorio UNIQUE (nombre)
);

CREATE TABLE TICKET (
    idTicket INT NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaLimite DATE NOT NULL,
    cedulaSolicitante CHAR(8) NOT NULL,
    idLaboratorio INT NOT NULL,
    prioridad VARCHAR(20) NOT NULL DEFAULT 'Media',
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    CONSTRAINT pk_ticket PRIMARY KEY (idTicket),
    CONSTRAINT fk_ticket_solicitante FOREIGN KEY (cedulaSolicitante) REFERENCES USUARIO (cedula),
    CONSTRAINT fk_ticket_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES LABORATORIO (idLaboratorio),
    CONSTRAINT chk_ticket_prioridad CHECK (prioridad IN ('Baja', 'Media', 'Alta')),
    CONSTRAINT chk_ticket_estado CHECK (estado IN ('Pendiente', 'En Proceso', 'Resuelto', 'Cancelado'))
);
