# MiliPet

Sitio PHP para tienda de mascotas (catálogo, campañas/adopciones y panel administrativo básico) listo para correr en XAMPP.

## Requisitos
- Windows con [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- Navegador web

## Instalación rápida (Windows + XAMPP)
1) Instala y abre XAMPP, inicia servicios:
	- Apache: Start
	- MySQL: Start

2) Copia el proyecto en esta ruta exacta:
	- `C:\xampp\htdocs\milipet_site`

3) Crea la base de datos e importa el esquema:
	- Abre `http://localhost/phpmyadmin/`
	- Crea la BD `milipet_db` (collation: utf8mb4_general_ci)
    - Importa `database/milipet_db.sql`
	- Importa `database/schema.sql`
	  - Alternativa: `database/milipet_db.sql` incluye datos de ejemplo adicionales (si existe)

4) Configura credenciales de base de datos en `config/config.php`:
	- Variables usadas por la app:
	  - `DB_HOST` (por defecto `localhost`)
	  - `DB_NAME` (usar `milipet_db`)
	  - `DB_USER` (por defecto `root`)
	  - `DB_PASS` (vacío por defecto en XAMPP)

5) Abre el sitio:
	- Público: `http://localhost/milipet_site/public/`
	- Administración: `http://localhost/milipet_site/public/?r=auth/admin_login`

## Credenciales de administrador (por defecto)
- Email: `admin@milipet.local`
- Password: `Admin123!`

Usuario admin por defecto (si lo creas con la migración):
- Email: `admin@milipet.local`
- Password inicial sugerida: `Admin123!` (cámbiala en producción)

Si quieres cambiar la contraseña:
1) Abre `http://localhost/milipet_site/public/assets/make_hash.php` para generar un hash nuevo.
2) Copia el hash y actualiza el campo `password_hash` en la tabla `users` (phpMyAdmin). Si aún usas tabla `admins`, aplica la migración de merge.

### Migración merge de tabla legacy `admins` a `users`
Si vienes de una versión anterior que tenía la tabla `admins`, ejecuta:

1. Importa `database/migrations/2025_01_merge_admins_into_users.sql` en phpMyAdmin.
2. Verifica que los registros estén en `users` con rol `admin`.
3. (Opcional) Elimina la tabla legacy `admins` después de confirmar.

### Migración de roles y activación de admin
Para habilitar roles y acceso administrativo seguro, aplica la migración:

1) Abre phpMyAdmin y selecciona la BD `milipet_db`.
2) Ve a la pestaña SQL y pega el contenido de `database/migrations/2025_01_add_role_to_users.sql`:

```
ALTER TABLE users
	ADD COLUMN role ENUM('admin','editor','user') NOT NULL DEFAULT 'user',
	ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1;

-- Opcional: setea a tu correo como admin (ajusta el email)
UPDATE users SET role='admin', is_active=1 WHERE email='admin@milipet.local';
```

3) Ejecuta la consulta. Ahora podrás iniciar sesión en `?r=auth/admin_login` con ese usuario.

### Recordarme (sesión persistente)
- Requiere la tabla `remember_tokens` (importa `database/migrations/2025_11_add_remember_tokens.sql`).
- En el login de admin, marca "Recordarme (30 días)". Se genera un token seguro (selector + validador) y se guarda en cookie HttpOnly con SameSite=Lax.
- Al volver sin sesión activa, el sitio te reconocerá y rotará el token por seguridad.
- Para cerrar sesión "global" en el navegador actual, usa "Salir" (borra la cookie y el token asociado). Para revocar todos los tokens, puedes limpiar la tabla `remember_tokens` en phpMyAdmin.

### Carrito persistente por usuario
- Requiere la tabla `user_carts` (importa `database/migrations/2025_11_add_user_carts.sql`).
- Los usuarios con sesión activa (admin/editor) verán su carrito persistido en BD.
- Sin sesión, el carrito sigue usando localStorage del navegador.
- Al iniciar sesión, el carrito local se sincroniza automáticamente con el servidor vía endpoint `/api/cart_sync.php`.


### Limpieza automática de tokens expirados
Puedes automatizar la eliminación de tokens vencidos con el script CLI:

`scripts/cleanup_tokens.php`

Ejemplo de entrada en crontab (cada noche 03:15):
```
15 3 * * * /usr/bin/php /ruta/a/milipet/scripts/cleanup_tokens.php >> /ruta/a/milipet/var/log/cleanup_tokens.log 2>&1
```
El script imprime el número de tokens eliminados. Asegúrate que el usuario de cron tenga permisos de lectura sobre el proyecto.

## Subida de imágenes
- Las imágenes se guardan en `public/assets/img/`.
- Tamaños aceptados: JPG/PNG (máx. ~3MB a nivel de formulario).
- Al reemplazar una imagen local por otra, la anterior se elimina automáticamente.

## Personalización de la tienda
- Edita `config/config.php` en la clave `store` para actualizar:
  - `name`, `address`, `phone`, `email`
  - `social.whatsapp`, `social.instagram`, `social.facebook`
  - `business_hours`
- Font Awesome (opcional): si tienes un Kit, define la constante `FONTAWESOME_KIT` (o ignora; el sitio funciona sin eso).

## Rutas principales
- Home: `?r=home`
- Catálogo: `?r=catalog` (filtros por categoría/especie, búsqueda, ver detalle)
- Campañas/Estáticos: `?r=adoptions`, `?r=about`, `?r=policies`
- Admin: `?r=admin/dashboard`, `?r=admin/products`

## Notas y solución de problemas
- Asegúrate de abrir el sitio desde la carpeta `public/` (ver punto 5). Acceder desde la raíz puede causar rutas de assets (CSS/JS) rotas.
- Error de conexión a BD: revisa `DB_USER`/`DB_PASS` en `config/config.php` y que MySQL esté iniciado.
- Error de “Failed opening required config.php”: verifica que el proyecto esté en `C:\xampp\htdocs\milipet_site` y que existan las carpetas `config/`, `public/`, etc.
- CSS/JS no cargan: fuerza actualización con Ctrl+F5.

## Estructura del proyecto (resumen)
```
public/          # Punto de entrada (index.php), assets
app/             # MVC básico: controllers, models, views
config/          # Configuración app + DB
database/        # Esquema SQL y datos de ejemplo
```

## Desarrollo
- No requiere Composer ni Node; es PHP plano + Bootstrap (CDN).
- Si agregas nuevas tablas/cambios, actualiza `database/schema.sql`.

---

Hecho con cariño para MiliPet 🐾
