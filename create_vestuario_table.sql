-- Usar la base de datos
USE login_system;

-- Crear la tabla vestuario para el inventario
CREATE TABLE IF NOT EXISTS vestuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    runt VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    foto VARCHAR(255) NOT NULL,
    pdf VARCHAR(255) NOT NULL,
    uniforme_institucional BOOLEAN DEFAULT 0,
    guerrera BOOLEAN DEFAULT 0,
    polera_servicio BOOLEAN DEFAULT 0,
    pantalon_servicio BOOLEAN DEFAULT 0,
    porta_equipo BOOLEAN DEFAULT 0,
    quipi BOOLEAN DEFAULT 0,
    parca_polar BOOLEAN DEFAULT 0,
    corta_viento BOOLEAN DEFAULT 0,
    cinturon BOOLEAN DEFAULT 0,
    cinturon_na BOOLEAN DEFAULT 0,
    botas BOOLEAN DEFAULT 0,
    talla_botas VARCHAR(50),
    polera_instruccion BOOLEAN DEFAULT 0,
    pantalon_instruccion BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);