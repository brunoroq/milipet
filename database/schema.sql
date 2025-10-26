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
  species ENUM('dogs','cats','birds','other') NULL,
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
  location VARCHAR(255) NOT NULL,
  foundation VARCHAR(100) NULL,
  image_url VARCHAR(255) NULL,
  contact_info TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO admins (name,email,password_hash) VALUES
('Administrador','admin@milipet.local', '$2y$10$Nw8pG6Zf5N0w8C3M5cY8uO0a8H9F4p2r6UuS8D9aQeR7tXzYp3y4a');
INSERT INTO categories (name) VALUES ('Alimentos'),('Accesorios'),('Higiene'),('Juguetes');
INSERT INTO products (name,description,price,stock,species,category_id,image_url,is_active) VALUES
('Alimento cachorro 10kg','Alimento premium para cachorro',29990,20,'dogs',1,'',1),
('Arnés tamaño S','Arnés acolchado para perros pequeños',11990,15,'dogs',2,'',1);
INSERT INTO campaigns (title,description,date,is_active) VALUES ('Jornada de adopción Pumay','Sábado con fundación aliada', CURDATE(),1);
