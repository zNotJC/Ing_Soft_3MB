CREATE TABLE LUGAR (
    idLugar INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,

    CONSTRAINT pk_lugar
        PRIMARY KEY (idLugar),

    CONSTRAINT uq_lugar
        UNIQUE (nombre)
);


CREATE TABLE PRIORIDAD (
    idPrioridad INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(20) NOT NULL,

    CONSTRAINT pk_prioridad
        PRIMARY KEY (idPrioridad),

    CONSTRAINT uq_prioridad
        UNIQUE (nombre)
);


CREATE TABLE ESTADO_TICKET (
    idEstado INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(20) NOT NULL,

    CONSTRAINT pk_estado_ticket
        PRIMARY KEY (idEstado),

    CONSTRAINT uq_estado_ticket
        UNIQUE (nombre)
);


CREATE TABLE TICKET (
    idTicket INT NOT NULL AUTO_INCREMENT,

    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,

    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaLimite DATE NOT NULL,

    cedulaSolicitante CHAR(8) NOT NULL,
    idLugar INT NOT NULL,
    idPrioridad INT NOT NULL,
    idEstado INT NOT NULL,

    CONSTRAINT pk_ticket
        PRIMARY KEY (idTicket),

    CONSTRAINT fk_ticket_solicitante
        FOREIGN KEY (cedulaSolicitante)
        REFERENCES USUARIO (cedula),

    CONSTRAINT fk_ticket_lugar
        FOREIGN KEY (idLugar)
        REFERENCES LUGAR (idLugar),

    CONSTRAINT fk_ticket_prioridad
        FOREIGN KEY (idPrioridad)
        REFERENCES PRIORIDAD (idPrioridad),

    CONSTRAINT fk_ticket_estado
        FOREIGN KEY (idEstado)
        REFERENCES ESTADO_TICKET (idEstado)
);