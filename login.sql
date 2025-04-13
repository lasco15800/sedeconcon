-- Crear la base de datos
CREATE DATABASE login_system;

-- Seleccionar la base de datos
USE login_system;

-- Crear la tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    failed_attempts INT DEFAULT 0,  -- Contador de intentos fallidos
    lock_time DATETIME NULL,        -- Fecha de bloqueo
    ip VARCHAR(45) NOT NULL         -- IP del usuario (IPv4 o IPv6)
);

-- Insertar un usuario de prueba (con contraseña encriptada)
INSERT INTO users (username, password) 
VALUES ('admin', SHA2('1234', 256)); -- La contraseña