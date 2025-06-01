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

INSERT INTO `usuarios`(`nombre`, `apellidos`, `email`, `contrasena`, `rol`) VALUES 
  ('Administrador',"", 'qkorte@gmail.com',SHA1('Abc123.'), 1);

CREATE TABLE sesiones (
    usuario_id INT PRIMARY KEY,
    token VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE
);
INSERT INTO `sesiones`(`usuario_id`) VALUES 
  (1);

CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    duracion_estimada INT NOT NULL,
    informacion TEXT,
    precio_reserva DECIMAL(10,2) DEFAULT 0,
    imagen VARCHAR(255) 
);

INSERT INTO `servicios`(`nombre`, `precio`, `duracion_estimada`, `informacion`, `precio_reserva`, `imagen`) VALUES 
       ('Lavar y peinar', 12.00, 25,
        'Disfruta de un relajante lavado y un innovador peinado a manos de nuestros profesionales.\n¡Sal con el look ideal y la melena impecable! ',
        0.00, 'service-1.jpg'),
       ('Mechas', 40.00, 90,
        'Disfruta de unas fantasticas mechas a manos de nuestros profesionales.\n¡Sal con el look ideal y un estilo fabuloso! ',
        0.00, 'service-2.jpg'),
       ('Tinte', 25.00, 60,
        'Disfruta de un nuevo color a manos de nuestros profesionales.\n¡Sal con el look ideal y un nuevo estilo! ',
        0.00, 'service-3.jpg'),
       ('Permanente', 35.00, 75,
        'Disfruta de una innovadora permanente a manos de nuestros profesionales.\n¡Sal con el look ideal y un peinado impecable! ',
        0.00, 'service-4.jpg');

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_servicio INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    informacion_adicional TEXT,
    estado VARCHAR(50) NOT NULL,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON UPDATE CASCADE ON DELETE CASCADE
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
    dia_semana ENUM('lunes','martes','miércoles','jueves','viernes','sábado','domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo BOOLEAN DEFAULT TRUE NOT NULL
);

INSERT INTO `horarios`(`dia_semana`, `hora_inicio`, `hora_fin`) VALUES 
      ('lunes', '10:00:00', '14:00:00'),
      ('lunes', '16:00:00', '20:00:00'),
      ('martes', '10:00:00', '14:00:00'),
      ('martes', '16:00:00', '20:00:00'),
      ('miércoles', '10:00:00', '14:00:00'),
      ('miércoles', '16:00:00', '20:00:00'),
      ('jueves', '10:00:00', '14:00:00'),
      ('jueves', '16:00:00', '20:00:00'),
      ('viernes', '10:00:00', '14:00:00'),
      ('viernes', '16:00:00', '20:00:00'),
      ('sábado', '16:00:00', '20:00:00'),
      ('domingo', '16:00:00', '20:00:00');

CREATE TABLE fechas_bloqueadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    repetir BOOLEAN DEFAULT FALSE
);

INSERT INTO `fechas_bloqueadas`(`fecha`, `repetir`) VALUES 
  ('2025-06-30',FALSE),
  ('2025-12-25',TRUE)
