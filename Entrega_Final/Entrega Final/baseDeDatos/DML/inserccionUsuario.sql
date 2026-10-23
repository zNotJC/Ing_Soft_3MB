INSERT INTO USUARIO (cedula, usuario, correo, claveHash) VALUES
('48751236', 'admin1', 'admin1@jojolos.local', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('52381479', 'docente1', 'docente1@jojolos.local', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS'),
('45698712', 'tecnico1', 'tecnico1@jojolos.local', '$2y$12$ki0bVkt8cnZuR4v6aJvhhelaeQc1/4fec2txUcuG1Ybr4cvnhg2sS');

INSERT INTO ADMINISTRADOR (cedula)
VALUES ('48751236');

INSERT INTO DOCENTE (cedula)
VALUES ('52381479');

INSERT INTO TECNICO (cedula)
VALUES ('45698712');
