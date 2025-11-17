-- Tabla para bloques de contenido editable (mini-CMS)
CREATE TABLE IF NOT EXISTS content_blocks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  content_key VARCHAR(100) NOT NULL UNIQUE COMMENT 'Identificador único del bloque (ej: home.hero_title)',
  title VARCHAR(150) NOT NULL COMMENT 'Etiqueta descriptiva para el panel admin',
  content TEXT NULL COMMENT 'Texto principal del bloque',
  image_url VARCHAR(255) NULL COMMENT 'URL de imagen asociada (opcional)',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo',
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_content_key (content_key),
  INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar bloques iniciales para las secciones del sitio

-- Home / Hero Section
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('home.hero_title', 'Home - Título principal', 'Todo para tu mejor amigo', NULL),
('home.hero_subtitle', 'Home - Subtítulo hero', 'Alimentos, accesorios y amor por las mascotas. Descubre todo lo que necesitas para cuidar de tu compañero peludo.', NULL),
('home.hero_image', 'Home - Imagen hero', NULL, 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=800&q=80');

-- Home / Newsletter Section
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('home.newsletter_title', 'Home - Título newsletter', '¿Quieres recibir ofertas para tu mejor amigo?', NULL),
('home.newsletter_text', 'Home - Texto newsletter', 'Suscríbete a nuestro newsletter y recibe promociones exclusivas, consejos de cuidado y novedades sobre productos para mascotas.', NULL);

-- Home / About Preview
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('home.about_title', 'Home - Título sección nosotros', 'Conoce MiliPet', NULL),
('home.about_text', 'Home - Texto sección nosotros', 'Somos una empresa chilena dedicada al bienestar de tus mascotas. Con más de 10 años de experiencia, ofrecemos productos de calidad para perros, gatos, aves y más.', NULL);

-- About / Quiénes Somos
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('about.hero_title', 'Nosotros - Título principal', 'Quiénes Somos', NULL),
('about.hero_subtitle', 'Nosotros - Subtítulo', 'Conoce nuestra historia, misión y compromiso con las mascotas de Chile', NULL),
('about.history_title', 'Nosotros - Título historia', 'Nuestra Historia', NULL),
('about.history_text', 'Nosotros - Texto historia', 'MiliPet nació en 2013 con un objetivo claro: ofrecer productos de calidad para mascotas a precios justos. Lo que comenzó como una pequeña tienda familiar en Santiago, hoy se ha convertido en una empresa con presencia en todo Chile, sirviendo a miles de familias que aman a sus mascotas tanto como nosotros.', NULL),
('about.mission_title', 'Nosotros - Título misión', 'Nuestra Misión', NULL),
('about.mission_text', 'Nosotros - Texto misión', 'Proporcionar productos y servicios de excelencia que mejoren la calidad de vida de las mascotas y fortalezcan el vínculo con sus familias. Nos comprometemos a ofrecer asesoría experta, productos certificados y un servicio al cliente excepcional.', NULL),
('about.vision_title', 'Nosotros - Título visión', 'Nuestra Visión', NULL),
('about.vision_text', 'Nosotros - Texto visión', 'Ser la tienda de mascotas más confiable y querida de Chile, reconocida por nuestra pasión por los animales, compromiso con la calidad y contribución al bienestar animal en todas sus formas.', NULL);

-- Adoptions / Adopciones
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('adoptions.hero_title', 'Adopciones - Título principal', 'Adopta y Cambia una Vida', NULL),
('adoptions.hero_subtitle', 'Adopciones - Subtítulo', 'Encuentra a tu nuevo mejor amigo. Cada mascota merece un hogar lleno de amor.', NULL),
('adoptions.intro_text', 'Adopciones - Texto introductorio', 'En MiliPet creemos que cada animal merece una segunda oportunidad. Trabajamos con refugios y organizaciones de rescate para conectar mascotas en búsqueda de hogar con familias responsables y amorosas.', NULL),
('adoptions.process_title', 'Adopciones - Título proceso', '¿Cómo Adoptar?', NULL),
('adoptions.process_text', 'Adopciones - Texto proceso', 'El proceso es sencillo: 1) Visita nuestras instalaciones o contacta a los refugios asociados. 2) Conoce a las mascotas disponibles. 3) Completa el formulario de adopción. 4) Evaluación y entrevista. 5) ¡Lleva a tu nuevo amigo a casa!', NULL);

-- Policies / Políticas
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('policies.shipping_title', 'Políticas - Título envíos', 'Política de Envíos', NULL),
('policies.shipping_text', 'Políticas - Texto envíos', 'Realizamos envíos a todo Chile. Tiempo de entrega: 2-5 días hábiles en RM, 3-7 días en regiones. Envío gratis en compras sobre $50.000. Los productos se envían en embalaje seguro para garantizar que lleguen en perfectas condiciones.', NULL),
('policies.returns_title', 'Políticas - Título devoluciones', 'Política de Devoluciones', NULL),
('policies.returns_text', 'Políticas - Texto devoluciones', 'Tienes 30 días para devolver productos sin uso en su empaque original. Los alimentos y productos de higiene no admiten devolución por razones sanitarias. Reembolso completo o cambio por otro producto. Costos de envío de devolución a cargo del cliente.', NULL),
('policies.privacy_title', 'Políticas - Título privacidad', 'Política de Privacidad', NULL),
('policies.privacy_text', 'Políticas - Texto privacidad', 'Protegemos tu información personal. Tus datos solo se usan para procesar pedidos y mejorar tu experiencia. No compartimos información con terceros sin tu consentimiento. Cumplimos con la Ley 19.628 de Protección de Datos Personales de Chile.', NULL);

-- Footer
INSERT INTO content_blocks (content_key, title, content, image_url) VALUES
('footer.about_text', 'Footer - Texto sobre nosotros', 'MiliPet es tu aliado en el cuidado de mascotas. Ofrecemos productos de calidad, asesoría experta y un compromiso genuino con el bienestar animal.', NULL),
('footer.contact_phone', 'Footer - Teléfono contacto', '+56 9 1234 5678', NULL),
('footer.contact_email', 'Footer - Email contacto', 'contacto@milipet.cl', NULL),
('footer.contact_address', 'Footer - Dirección', 'Av. Providencia 1234, Santiago, Chile', NULL);
