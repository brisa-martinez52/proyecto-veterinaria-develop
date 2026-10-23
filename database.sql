-- Base de Datos Veterinaria Patitas
-- Para importar en phpMyAdmin

CREATE DATABASE IF NOT EXISTS veterinaria_patitas;
USE veterinaria_patitas;

-- Tabla Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    es_admin TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Mascotas
CREATE TABLE mascotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    raza VARCHAR(100) NOT NULL,
    sexo ENUM('Macho', 'Hembra') NOT NULL,
    edad INT NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Veterinarios
CREATE TABLE veterinarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria VARCHAR(50) NOT NULL,
    imagen_url TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Turnos
CREATE TABLE turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mascota_id INT NOT NULL,
    veterinario_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('activo', 'cancelado', 'completado') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id) ON DELETE CASCADE,
    FOREIGN KEY (veterinario_id) REFERENCES veterinarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Compras
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Items de Compra
CREATE TABLE compra_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla Historial Médico
CREATE TABLE historial_medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT NOT NULL,
    veterinario_id INT NOT NULL,
    fecha DATE NOT NULL,
    diagnostico TEXT NOT NULL,
    tratamiento TEXT NOT NULL,
    vacunas TEXT,
    estudios TEXT,
    peso DECIMAL(5,2) NOT NULL,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id) ON DELETE CASCADE,
    FOREIGN KEY (veterinario_id) REFERENCES veterinarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar Usuario Administrador (password: admin123)
INSERT INTO usuarios (dni, nombre, apellido, email, telefono, password, es_admin) 
VALUES ('admin', 'Administrador', 'Sistema', 'admin@patitas.com', '2991234567', 
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insertar Veterinarios
INSERT INTO veterinarios (nombre, apellido, especialidad, telefono) VALUES
('María', 'González', 'Medicina General', '2991111111'),
('Carlos', 'Rodríguez', 'Cirugía', '2992222222'),
('Ana', 'Martínez', 'Dermatología', '2993333333');

-- Insertar Productos
INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen_url) VALUES
('Alimento Perro Adulto', 'Alimento balanceado premium para perros adultos 15kg', 25000.00, 50, 'Alimentos', 'https://images.unsplash.com/photo-1589924691995-400dc9ecc119?w=400'),
('Alimento Gato Adulto', 'Alimento balanceado para perros', 18000.00, 40, 'Alimentos', 'https://images.unsplash.com/photo-1591160690555-5debfba289f0?w=400'),
('Collar Antipulgas', 'Collar antipulgas y garrapatas de larga duración', 5000.00, 100, 'Accesorios', 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=400'),
('Juguete Interactivo', 'Juguete interactivo para mascotas con dispensador de premios', 3500.00, 75, 'Juguetes', 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400'),
('Cama Ortopédica', 'Cama ortopédica con memoria para mascotas grandes', 12000.00, 30, 'Accesorios', 'Imagenes/foto-shampoo.png'),
('Shampoo Medicado', 'Shampoo medicado para piel sensible 500ml', 2500.00, 60, 'Higiene', 'Imagenes/foto-shampoo.png');
