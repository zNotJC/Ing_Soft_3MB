CREATE TABLE estado_prestamo
(
    idEstado INT AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL UNIQUE,

    PRIMARY KEY (idEstado)
);

CREATE TABLE equipo
(
    idEquipo INT AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    numeroSerie VARCHAR(100) NOT NULL UNIQUE,
    idLugar INT NOT NULL,

    PRIMARY KEY (idEquipo),

    FOREIGN KEY (idLugar)
        REFERENCES lugar(idLugar)
);

CREATE TABLE prestamo
(
    idPrestamo INT AUTO_INCREMENT,
    idEquipo INT NOT NULL,
    cedulaUsuario VARCHAR(20) NOT NULL,
    fechaPrestamo DATE NOT NULL,
    fechaDevolucion DATE NOT NULL,
    idEstado INT NOT NULL,

    PRIMARY KEY (idPrestamo),

    FOREIGN KEY (idEquipo)
        REFERENCES equipo(idEquipo),

    FOREIGN KEY (cedulaUsuario)
        REFERENCES usuario(cedula),

    FOREIGN KEY (idEstado)
        REFERENCES estado_prestamo(idEstado)
);