CREATE TABLE solicitud (
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    cedulaUsuario CHAR(8) NOT NULL,
    tipoSolicitud VARCHAR(30) NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    equipo VARCHAR(100) NULL,
    fechaSolicitud DATE NOT NULL,
    fechaNecesaria DATE NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    respuesta VARCHAR(500) NULL,

    CONSTRAINT fk_solicitud_usuario
        FOREIGN KEY (cedulaUsuario)
        REFERENCES usuario(cedula),

    CONSTRAINT chk_solicitud_tipo
        CHECK (
            tipoSolicitud IN (
                'Equipamiento',
                'Reparación',
                'Acceso',
                'Licencia',
                'Mobiliario',
                'Otro'
            )
        ),

    CONSTRAINT chk_solicitud_cantidad
        CHECK (cantidad > 0),

    CONSTRAINT chk_solicitud_estado
        CHECK (
            estado IN (
                'Pendiente',
                'Aprobada',
                'Rechazada',
                'Cancelada'
            )
        )
);