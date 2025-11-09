# MiliPet

Sitio PHP para tienda de mascotas (catálogo, campañas/adopciones y panel administrativo básico) listo para correr con Docker.

## Requisitos
- Docker y Docker Compose
- Navegador web

## Puesta en marcha rápida (Docker)
1) Levanta los servicios:

	- En el directorio del proyecto, ejecuta:

	  - docker compose up -d

2) Accede a las aplicaciones:
	- Sitio web: http://localhost:8080/
	- Login de administrador: http://localhost:8080/?r=auth/admin_login
	- phpMyAdmin: http://localhost:8081/

3) Base de datos (importar esquema y datos):
	- Compose crea la BD `milipet_db` y el usuario por defecto.
	- Credenciales por defecto (configurables en `docker-compose.yml`):
	  - DB host: `db`
	  - DB name: `milipet_db`
	  - DB user: `appuser`
	  - DB pass: `apppass`
	  - Root pass: `root`
	- En phpMyAdmin (usuario `appuser` / `apppass`), selecciona `milipet_db` y importa en este orden:
	  1. `database/schema.sql`
	  2. (Opcional) `database/milipet_db.sql` para datos de ejemplo
	  3. Migraciones adicionales:
		  - `database/migrations/2025_01_add_role_to_users.sql`
		  - `database/migrations/2025_01_merge_admins_into_users.sql` (si vienes de tabla `admins`)
		  - `database/migrations/2025_11_add_remember_tokens.sql`
		  - `database/migrations/2025_11_add_user_carts.sql`

4) Variables de entorno (opcionales):
	- Puedes ajustar puertos y credenciales editando `docker-compose.yml` o usando variables del entorno antes de levantar los servicios:
	  - `WEB_PORT` (por defecto 8080)
	  - `PMA_PORT` (por defecto 8081)
	  - `DB_PORT` (por defecto 3306)
	  - `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASS`

5) Detener/inspeccionar
	- Detener: docker compose down
	- Ver logs: docker compose logs -f

## Credenciales de administrador (por defecto)
- Email: `admin@milipet.local`
- Password: `Admin123!`

Usuario admin por defecto (si lo creas con la migración):
- Email: `admin@milipet.local`
- Password inicial sugerida: `Admin123!` (cámbiala en producción)

Si quieres cambiar la contraseña:
1) Abre `http://localhost:8080/assets/make_hash.php` para generar un hash nuevo.
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
15 3 * * * /usr/bin/php /ruta/al/proyecto/milipet/scripts/cleanup_tokens.php >> /ruta/al/proyecto/milipet/var/log/cleanup_tokens.log 2>&1
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
- Home: `http://localhost:8080/?r=home`
- Catálogo: `http://localhost:8080/?r=catalog` (filtros por categoría/especie, búsqueda, ver detalle)
- Campañas/Estáticos: `http://localhost:8080/?r=adoptions`, `http://localhost:8080/?r=about`, `http://localhost:8080/?r=policies`
- Admin: `http://localhost:8080/?r=admin/dashboard`, `http://localhost:8080/?r=admin/products`

## Notas y solución de problemas
- El DocumentRoot ya apunta a `public/` en el contenedor; accede por `http://localhost:8080/`.
- Error de conexión a BD: revisa variables en `docker-compose.yml` y que los contenedores estén arriba (`docker compose ps`).
- Permisos de archivos (Linux): si ves problemas al escribir/guardar desde el contenedor, puedes descomentar la línea `user: "${HOST_UID:-1000}:${HOST_GID:-1000}"` en `docker-compose.yml`.
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
