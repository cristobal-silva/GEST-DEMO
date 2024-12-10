-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS dentista_pro;
USE dentista_pro;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('paciente', 'profesional', 'administrador') DEFAULT 'paciente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    duracion INT NOT NULL, -- Duración en minutos
    precio DECIMAL(10, 2) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de horarios
CREATE TABLE IF NOT EXISTS horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    dia_semana ENUM('lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
);

-- Tabla de reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    servicio_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('confirmada', 'modificada', 'cancelada') DEFAULT 'confirmada',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
);

-- Tabla de historial de reservas
CREATE TABLE IF NOT EXISTS historial_reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    cambio ENUM('modificado', 'cancelado') NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
);

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar datos iniciales (opcional)
-- Usuarios de ejemplo
INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES
('Administrador', 'admin@dentista.com', '$2y$10$EjemploHashParaAdmin', 'administrador'),
('Paciente', 'paciente@dentista.com', '$2y$10$EjemploHashParaPaciente', 'paciente'),
('Profesional', 'profesional@dentista.com', '$2y$10$EjemploHashParaProfesional', 'profesional');

-- Servicios de ejemplo
INSERT INTO servicios (nombre, descripcion, duracion, precio) VALUES
('Limpieza Dental', 'Limpieza profunda de los dientes y encías.', 30, 50.00),
('Consulta General', 'Consulta con diagnóstico inicial.', 20, 30.00),
('Ortodoncia', 'Sesión de revisión y ajustes de ortodoncia.', 40, 70.00);

-- Horarios de ejemplo
INSERT INTO horarios (servicio_id, dia_semana, hora_inicio, hora_fin) VALUES
(1, 'lunes', '09:00:00', '12:00:00'),
(1, 'miércoles', '14:00:00', '17:00:00'),
(2, 'martes', '10:00:00', '13:00:00'),
(3, 'jueves', '15:00:00', '18:00:00');

-- Reservas de ejemplo
INSERT INTO reservas (usuario_id, servicio_id, fecha, hora, estado) VALUES
(2, 1, '2024-12-08', '09:30:00', 'confirmada'),
(2, 3, '2024-12-09', '15:30:00', 'confirmada');

-- Notificaciones de ejemplo
INSERT INTO notificaciones (usuario_id, mensaje, leida) VALUES
(2, 'Recuerda tu cita de Limpieza Dental el 2024-12-08 a las 09:30.', FALSE),
(2, 'Tu cita de Ortodoncia ha sido confirmada para el 2024-12-09 a las 15:30.', FALSE);
