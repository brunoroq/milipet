-- database/schema.sql
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  category_id INT NULL,
  image_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS campaigns (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  date DATE NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- === Species (catálogo de especies) ===
CREATE TABLE IF NOT EXISTS species (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relación N:M producto_especie
CREATE TABLE IF NOT EXISTS product_species (
  product_id INT NOT NULL,
  species_id INT NOT NULL,
  PRIMARY KEY (product_id, species_id),
  CONSTRAINT fk_ps_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_ps_species FOREIGN KEY (species_id) REFERENCES species(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices de rendimiento
CREATE INDEX idx_ps_species ON product_species(species_id);
CREATE INDEX idx_products_is_active ON products(is_active);
CREATE INDEX idx_products_stock ON products(stock);
CREATE INDEX idx_products_name ON products(name);

-- Datos iniciales
INSERT INTO admins (name, email, password_hash) VALUES
('Administrador', 'admin@milipet.local', '$2y$10$Nw8pG6Zf5N0w8C3M5cY8uO0a8H9F4p2r6UuS8D9aQeR7tXzYp3y4a'); -- contraseña: Admin123!

INSERT INTO categories (name) VALUES ('Alimentos'), ('Accesorios'), ('Higiene'), ('Juguetes');

INSERT INTO species (name) VALUES ('Perros'),('Gatos'),('Aves'),('Otros');

INSERT INTO products (name, description, price, stock, category_id, image_url, is_active) VALUES
('Alimento cachorro 10kg', 'Alimento premium para cachorro', 29990, 20, 1, '', 1),
('Arnés tamaño S', 'Arnés acolchado para perros pequeños', 11990, 15, 2, '', 1);

-- Asignar especie por defecto a demo (Perros)
INSERT INTO product_species (product_id, species_id)
SELECT p.id, s.id FROM products p JOIN species s ON s.name='Perros' WHERE p.name IN ('Alimento cachorro 10kg','Arnés tamaño S');

INSERT INTO campaigns (title, description, date, is_active) VALUES
('Jornada de adopción Pumay', 'Sábado con fundación aliada', CURDATE(), 1);
