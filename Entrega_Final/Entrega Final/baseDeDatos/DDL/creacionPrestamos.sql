CREATE TABLE EQUIPO (
    idEquipo INT AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    idLaboratorio INT NOT NULL,
    tipo VARCHAR(50) NOT NULL DEFAULT 'Ordenadores',
    estado VARCHAR(30) NOT NULL DEFAULT 'Disponible',
    PRIMARY KEY (idEquipo),
    CONSTRAINT fk_equipo_laboratorio FOREIGN KEY (idLaboratorio) REFERENCES LABORATORIO (idLaboratorio),
    CONSTRAINT chk_equipo_estado CHECK (estado IN ('Disponible','No disponible','Roto','En uso'))
);

CREATE TABLE PRESTAMO (
    idPrestamo INT AUTO_INCREMENT,
    idEquipo INT NOT NULL,
    cedulaUsuario CHAR(8) NOT NULL,
    fechaPrestamo DATE NOT NULL,
    fechaDevolucion DATE NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    prioridad VARCHAR(20) NOT NULL DEFAULT 'Media',
    PRIMARY KEY (idPrestamo),
    CONSTRAINT fk_prestamo_equipo FOREIGN KEY (idEquipo) REFERENCES EQUIPO (idEquipo),
    CONSTRAINT fk_prestamo_usuario FOREIGN KEY (cedulaUsuario) REFERENCES USUARIO (cedula),
    CONSTRAINT chk_prestamo_estado CHECK (estado IN ('Pendiente', 'Activo', 'Cancelado', 'Devuelto')),
    CONSTRAINT chk_prestamo_prioridad CHECK (prioridad IN ('Baja', 'Media', 'Alta'))
);
