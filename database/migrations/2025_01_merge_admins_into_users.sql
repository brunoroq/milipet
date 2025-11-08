-- Migra registros desde tabla legacy 'admins' hacia 'users'
-- Asegúrate de hacer backup antes de ejecutar.

INSERT INTO users (email, password_hash, role, is_active)
SELECT email, password_hash, 'admin' AS role, 1 AS is_active FROM admins
ON DUPLICATE KEY UPDATE
  password_hash = VALUES(password_hash),
  role = 'admin',
  is_active = 1;

-- Opcional: eliminar tabla legacy después de verificar
-- DROP TABLE admins;