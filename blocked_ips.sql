-- Crear la tabla para almacenar IPs bloqueadas
CREATE TABLE blocked_ips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) UNIQUE NOT NULL,  -- IP del usuario (IPv4 o IPv6)
    failed_attempts INT DEFAULT 0,   -- Contador de intentos fallidos
    lock_time DATETIME NULL          -- Fecha de bloqueo
);