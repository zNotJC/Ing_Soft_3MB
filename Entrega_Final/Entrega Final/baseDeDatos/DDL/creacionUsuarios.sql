CREATE TABLE USUARIO (
    cedula CHAR(8) NOT NULL,
    usuario VARCHAR(20) NOT NULL UNIQUE,
    correo VARCHAR(120) NOT NULL UNIQUE,
    claveHash VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    sesionActiva BOOLEAN NOT NULL DEFAULT FALSE,

    CONSTRAINT pk_usuario
        PRIMARY KEY (cedula)
);

CREATE TABLE ADMINISTRADOR (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_administrador
        PRIMARY KEY (cedula),

    CONSTRAINT fk_administrador_usuario
        FOREIGN KEY (cedula)
        REFERENCES USUARIO (cedula)
);

CREATE TABLE DOCENTE (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_docente
        PRIMARY KEY (cedula),

    CONSTRAINT fk_docente_usuario
        FOREIGN KEY (cedula)
        REFERENCES USUARIO (cedula)
);

CREATE TABLE TECNICO (
    cedula CHAR(8) NOT NULL,

    CONSTRAINT pk_tecnico
        PRIMARY KEY (cedula),

    CONSTRAINT fk_tecnico_usuario
        FOREIGN KEY (cedula)
        REFERENCES USUARIO (cedula)
);

CREATE TABLE ACCESO_SISTEMA (
    idAcceso INT AUTO_INCREMENT,
    cedulaUsuario CHAR(8) NOT NULL,
    rol VARCHAR(20) NOT NULL,
    fechaHora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_acceso_sistema
        PRIMARY KEY (idAcceso),

    CONSTRAINT fk_acceso_usuario
        FOREIGN KEY (cedulaUsuario)
        REFERENCES USUARIO (cedula),

    CONSTRAINT chk_acceso_rol
        CHECK (rol IN ('Administrador', 'Docente', 'Tecnico'))
);
