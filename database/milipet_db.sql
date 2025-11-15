-- phpMyAdmin SQL Dump-- database/schema.sql

-- Milipet Database - Nuevo EsquemaCREATE TABLE IF NOT EXISTS admins (

-- Generado: 2025-11-15  id INT AUTO_INCREMENT PRIMARY KEY,

-- Versión del servidor: 10.4.32-MariaDB  name VARCHAR(100) NOT NULL,

-- Versión de PHP: 8.2.12  email VARCHAR(150) NOT NULL UNIQUE,

  password_hash VARCHAR(255) NOT NULL,

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

START TRANSACTION;) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS categories (

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;  id INT AUTO_INCREMENT PRIMARY KEY,

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;  name VARCHAR(100) NOT NULL,

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

/*!40101 SET NAMES utf8mb4 */;) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--CREATE TABLE IF NOT EXISTS products (

-- Base de datos: `milipet_db`  id INT AUTO_INCREMENT PRIMARY KEY,

--  name VARCHAR(200) NOT NULL,

  description TEXT,

-- =========================================================  price INT NOT NULL DEFAULT 0,

--  BORRAR TABLAS EXISTENTES (RESPETANDO CLAVES FORÁNEAS)  stock INT NOT NULL DEFAULT 0,

-- =========================================================  category_id INT NULL,

  image_url VARCHAR(255) NULL,

DROP TABLE IF EXISTS cart_items;  is_active TINYINT(1) NOT NULL DEFAULT 1,

DROP TABLE IF EXISTS user_carts;  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

DROP TABLE IF EXISTS favorites;  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL

DROP TABLE IF EXISTS product_species;) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS products;

DROP TABLE IF EXISTS campaigns;CREATE TABLE IF NOT EXISTS campaigns (

DROP TABLE IF EXISTS categories;  id INT AUTO_INCREMENT PRIMARY KEY,

DROP TABLE IF EXISTS species;  title VARCHAR(200) NOT NULL,

DROP TABLE IF EXISTS users;  description TEXT,

DROP TABLE IF EXISTS roles;  date DATE NOT NULL,

DROP TABLE IF EXISTS admins;  is_active TINYINT(1) NOT NULL DEFAULT 1,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

-- =========================================================) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--  TABLA DE ROLES

-- =========================================================-- === Species (catálogo de especies) ===

CREATE TABLE IF NOT EXISTS species (

CREATE TABLE `roles` (  id INT AUTO_INCREMENT PRIMARY KEY,

    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  name VARCHAR(100) NOT NULL UNIQUE,

    `name` VARCHAR(50) NOT NULL COMMENT 'admin, editor, cliente, etc.',  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

    `description` VARCHAR(255) NULL,) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    UNIQUE KEY `uq_roles_name` (`name`)-- Relación N:M producto_especie

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;CREATE TABLE IF NOT EXISTS product_species (

  product_id INT NOT NULL,

--  species_id INT NOT NULL,

-- Volcado de datos para la tabla `roles`  PRIMARY KEY (product_id, species_id),

--  CONSTRAINT fk_ps_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

  CONSTRAINT fk_ps_species FOREIGN KEY (species_id) REFERENCES species(id) ON DELETE CASCADE

INSERT INTO `roles` (`name`, `description`, `is_active`) VALUES) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

('admin', 'Acceso total al panel de administración', 1),

('editor', 'Puede gestionar productos y campañas', 1),-- Índices de rendimiento

('cliente', 'Cliente de la tienda', 1);CREATE INDEX idx_ps_species ON product_species(species_id);

CREATE INDEX idx_products_is_active ON products(is_active);

-- =========================================================CREATE INDEX idx_products_stock ON products(stock);

--  TABLA DE USUARIOSCREATE INDEX idx_products_name ON products(name);

-- =========================================================

-- Datos iniciales

CREATE TABLE `users` (INSERT INTO admins (name, email, password_hash) VALUES

    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,('Administrador', 'admin@milipet.local', '$2y$10$Nw8pG6Zf5N0w8C3M5cY8uO0a8H9F4p2r6UuS8D9aQeR7tXzYp3y4a'); -- contraseña: Admin123!

    `role_id` INT UNSIGNED NOT NULL,

    `name` VARCHAR(100) NOT NULL,INSERT INTO categories (name) VALUES ('Alimentos'), ('Accesorios'), ('Higiene'), ('Juguetes');

    `email` VARCHAR(150) NOT NULL,

    `password` VARCHAR(255) NOT NULL COMMENT 'password_hash',INSERT INTO species (name) VALUES ('Perros'),('Gatos'),('Aves'),('Otros');

    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    `remember_token` VARCHAR(255) NULL,INSERT INTO products (name, description, price, stock, category_id, image_url, is_active) VALUES

    `last_login` DATETIME NULL,('Alimento cachorro 10kg', 'Alimento premium para cachorro', 29990, 20, 1, '', 1),

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,('Arnés tamaño S', 'Arnés acolchado para perros pequeños', 11990, 15, 2, '', 1);

    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY `uq_users_email` (`email`),-- Asignar especie por defecto a demo (Perros)

    INDEX `idx_users_role` (`role_id`),INSERT INTO product_species (product_id, species_id)

    CONSTRAINT `fk_users_role`SELECT p.id, s.id FROM products p JOIN species s ON s.name='Perros' WHERE p.name IN ('Alimento cachorro 10kg','Arnés tamaño S');

        FOREIGN KEY (`role_id`)

        REFERENCES `roles`(`id`)INSERT INTO campaigns (title, description, date, is_active) VALUES

        ON UPDATE CASCADE('Jornada de adopción Pumay', 'Sábado con fundación aliada', CURDATE(), 1);

        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
-- Contraseña: br1wlpro
--

INSERT INTO `users` (`role_id`, `name`, `email`, `password`, `is_active`) VALUES
((SELECT id FROM roles WHERE name = 'admin'), 'Administrador', 'admin@milipet.cl', '$2y$12$/ckGQdPTOJicDSZwiQinoOTSCpjZkk3RYe5RRUelGzhRZ1Z26bb0e', 1);

-- =========================================================
--  TABLA DE ESPECIES (PERRO, GATO, ETC.)
-- =========================================================

CREATE TABLE `species` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(80) NOT NULL,
    `description` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY `uq_species_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `species`
--

INSERT INTO `species` (`name`, `description`, `is_active`) VALUES
('Perros', 'Productos para perros', 1),
('Gatos', 'Productos para gatos', 1),
('Aves', 'Productos para aves', 1),
('Otros', 'Productos para otras mascotas', 1);

-- =========================================================
--  TABLA DE CATEGORÍAS (ALIMENTOS, JUGUETES, ETC.)
-- =========================================================

CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(80) NOT NULL,
    `description` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY `uq_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`name`, `description`, `is_active`) VALUES
('Alimentos', 'Comida y snacks para mascotas', 1),
('Accesorios', 'Collares, correas, placas identificadoras', 1),
('Higiene', 'Productos de limpieza y cuidado', 1),
('Juguetes', 'Juguetes y entretenimiento', 1);

-- =========================================================
--  TABLA DE PRODUCTOS
-- =========================================================

CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `short_desc` VARCHAR(255) NULL COMMENT 'Descripción corta para listados',
    `long_desc` TEXT NULL COMMENT 'Descripción completa',
    `price` DECIMAL(10,2) NOT NULL,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    `image_url` VARCHAR(255) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Producto destacado',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_products_category` (`category_id`),
    INDEX `idx_products_active` (`is_active`),
    INDEX `idx_products_featured` (`is_featured`),
    CONSTRAINT `fk_products_category`
        FOREIGN KEY (`category_id`)
        REFERENCES `categories`(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `chk_products_price` CHECK (`price` >= 0),
    CONSTRAINT `chk_products_stock` CHECK (`stock` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
-- Nota: Convertir description antigua a short_desc y long_desc
--

INSERT INTO `products` (`name`, `short_desc`, `long_desc`, `price`, `stock`, `category_id`, `image_url`, `is_featured`, `is_active`) VALUES
('Producto de ejemplo', 'Este es un producto de ejemplo', 'Descripción completa del producto de ejemplo. Aquí puedes agregar todos los detalles que necesites sobre el producto.', 15000, 10, 1, NULL, 0, 1);

-- =========================================================
--  TABLA PUENTE PRODUCTO-ESPECIE (N:M)
-- =========================================================

CREATE TABLE `product_species` (
    `product_id` INT UNSIGNED NOT NULL,
    `species_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `species_id`),
    INDEX `idx_ps_species` (`species_id`),
    CONSTRAINT `fk_ps_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `products`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_ps_species`
        FOREIGN KEY (`species_id`)
        REFERENCES `species`(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
--  TABLA DE CAMPAÑAS / PROMOCIONES
-- =========================================================

CREATE TABLE `campaigns` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `banner_image` VARCHAR(255) NULL COMMENT 'Ruta del banner',
    `start_date` DATE NULL COMMENT 'Fecha de inicio',
    `end_date` DATE NULL COMMENT 'Fecha de fin',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `chk_campaigns_dates`
        CHECK (`end_date` IS NULL OR `start_date` IS NULL OR `end_date` >= `start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campaigns`
--

INSERT INTO `campaigns` (`title`, `description`, `banner_image`, `start_date`, `end_date`, `is_active`) VALUES
('Jornada de adopción Pumay', 'Sábado con fundación aliada. Ven a conocer a tu próximo mejor amigo.', NULL, '2025-11-20', '2025-11-20', 1);

-- =========================================================
--  TABLAS DE CARRITO
-- =========================================================

-- Carrito: puede ser de un usuario logueado o de una sesión de invitado
CREATE TABLE `user_carts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL COMMENT 'NULL si es invitado',
    `session_id` VARCHAR(64) NULL COMMENT 'ID de sesión para invitados',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_carts_user` (`user_id`),
    INDEX `idx_carts_session` (`session_id`),
    CONSTRAINT `fk_carts_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ítems dentro del carrito
CREATE TABLE `cart_items` (
    `cart_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL COMMENT 'Precio al agregar',
    PRIMARY KEY (`cart_id`, `product_id`),
    CONSTRAINT `fk_ci_cart`
        FOREIGN KEY (`cart_id`)
        REFERENCES `user_carts`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_ci_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `products`(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `chk_ci_qty` CHECK (`quantity` > 0),
    CONSTRAINT `chk_ci_price` CHECK (`unit_price` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================================
--  TABLA DE FAVORITOS
-- =========================================================

CREATE TABLE `favorites` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_favorites_user_product` (`user_id`, `product_id`),
    INDEX `idx_favorites_product` (`product_id`),
    CONSTRAINT `fk_fav_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users`(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_fav_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `products`(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =========================================================
-- CREDENCIALES DE ACCESO
-- =========================================================
-- Email: admin@milipet.cl
-- Contraseña: br1wlpro
-- =========================================================
