-- Tabla para gestionar el slider de imágenes del hero del home
-- Este slider reemplaza la imagen fija home.hero_image permitiendo múltiples slides rotativas

CREATE TABLE IF NOT EXISTS `home_hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT 'Título opcional para futuro uso',
  `subtitle` varchar(255) DEFAULT NULL COMMENT 'Subtítulo opcional',
  `image_url` varchar(500) NOT NULL COMMENT 'URL de la imagen del slide',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización (menor primero)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Si el slide está activo (visible)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_order` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Slides del carousel hero del home';

-- Insertar slides de ejemplo usando la imagen actual del hero
INSERT INTO `home_hero_slides` (`title`, `subtitle`, `image_url`, `sort_order`, `is_active`) VALUES
('Bienvenido a MiliPet', 'Productos de calidad para tus mascotas', '/assets/img/hero-perro.webp', 1, 1);

-- Nota: Ejecutar este SQL en la base de datos:
-- docker exec -i mariadb mysql -u root -prootpassword milipet < database/home_hero_slides.sql
