CREATE DATABASE IF NOT EXISTS qkorte;
USE qkorte;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

INSERT INTO `roles`(`nombre`) VALUES 
  ('administrador'),
  ('cliente');

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL,
  apellidos VARCHAR(50),
  email VARCHAR(100) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol INT NOT NULL,

  FOREIGN KEY (rol) REFERENCES roles(id)
);

INSERT INTO `usuarios`(`nombre`, `apellidos`, `email`, `contrasena`, `rol`) 
VALUES ('administrador',"", 'qkorte@gmail.com',SHA1('Abc123.'), 1);

CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    duracion_estimada INT NOT NULL,
    informacion TEXT,
    precio_reserva DECIMAL(10,2) DEFAULT 0
);

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_servicio INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    informacion_adicional TEXT,
    estado VARCHAR(50) NOT NULL,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id)
);

CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cita INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha DATE NOT NULL,
    leido BOOLEAN,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_cita) REFERENCES citas(id)
);

CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana VARCHAR(25),
    horario TEXT,
    activo BOOLEAN
);

CREATE TABLE fechas_bloqueadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    repetir BOOLEAN
);
