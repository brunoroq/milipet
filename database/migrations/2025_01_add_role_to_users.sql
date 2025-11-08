ALTER TABLE users
  ADD COLUMN role ENUM('admin','editor','user') NOT NULL DEFAULT 'user',
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1;

-- Opcional: setea a tu correo como admin (ajusta el email)
UPDATE users SET role='admin', is_active=1 WHERE email='admin@milipet.local';
