-- Crear la tabla de categorías
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- Crear la tabla de permisos de usuario
CREATE TABLE user_permissions (
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    PRIMARY KEY (user_id, category_id)
);

-- Insertar categorías de ejemplo
INSERT INTO categories (name) VALUES ('Emergencias'), ('Vestuario'), ('Cerrar sesión');