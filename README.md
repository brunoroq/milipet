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
	- Administración: `http://localhost/milipet_site/public/?r=auth/login`

## Credenciales de administrador (por defecto)
- Email: `admin@milipet.local`
- Password: `Admin123!`

Si quieres cambiar la contraseña:
1) Abre `http://localhost/milipet_site/public/assets/make_hash.php` para generar un hash nuevo.
2) Copia el hash y actualiza el campo `password_hash` en la tabla `admins` (phpMyAdmin).

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
