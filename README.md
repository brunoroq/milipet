# MiliPet 🐾# MiliPet 🐾



Sistema de gestión para tienda de mascotas con catálogo de productos, campañas de adopción y panel administrativo. Desarrollado en PHP con arquitectura MVC y listo para ejecutar con Docker.Sistema de gestión para tienda de mascotas con catálogo de productos, campañas de adopción y panel administrativo. Desarrollado en PHP con arquitectura MVC y listo para ejecutar con Docker.



## 📋 Requisitos## 📋 Requisitos

- Docker y Docker Compose- Docker y Docker Compose

- Navegador web moderno- Navegador web moderno



## 🚀 Instalación Rápida## 🚀 Instalación Rápida



### 1. Clonar el repositorio### 1. Clonar el repositorio

```bash```bash

git clone <tu-repo-url>git clone <tu-repo-url>

cd milipetcd milipet

``````



### 2. Levantar los servicios con Docker### 2. Levantar los servicios con Docker

```bash```bash

docker compose up -ddocker compose up -d

``````



### 3. Importar la base de datos### 3. Importar la base de datos



**Opción A - Usando phpMyAdmin (Recomendado):****Opción A - Usando phpMyAdmin (Recomendado):**

1. Accede a phpMyAdmin: http://localhost:8081/1. Accede a phpMyAdmin: http://localhost:8081/

2. Credenciales:2. Credenciales:

   - Usuario: `appuser`   - Usuario: `appuser`

   - Contraseña: `apppass`   - Contraseña: `apppass`

3. Selecciona la base de datos `milipet_db`3. Selecciona la base de datos `milipet_db`

4. Ve a la pestaña "Importar"4. Ve a la pestaña "Importar"

5. Selecciona el archivo `database/milipet_db.sql`5. Selecciona el archivo `database/milipet_db.sql`

6. Click en "Continuar"6. Click en "Continuar"



**Opción B - Desde la terminal:****Opción B - Desde la terminal:**

```bash```bash

# Copiar el archivo SQL al contenedor# Copiar el archivo SQL al contenedor

docker cp database/milipet_db.sql milipet-db:/tmp/docker cp database/milipet_db.sql milipet-db:/tmp/



# Importar (te pedirá la contraseña: root)# Importar (te pedirá la contraseña: root)

docker exec -i milipet-db mysql -u root -p milipet_db < database/milipet_db.sqldocker exec -i milipet-db mysql -u root -p milipet_db < database/milipet_db.sql

``````



### 4. Acceder a la aplicación### 4. Acceder a la aplicación

- **Sitio web:** http://localhost:8080/- **Sitio web:** http://localhost:8080/

- **Panel admin:** http://localhost:8080/?r=auth/admin_login- **Panel admin:** http://localhost:8080/?r=auth/admin_login

- **phpMyAdmin:** http://localhost:8081/- **phpMyAdmin:** http://localhost:8081/



## 🔐 Credenciales por Defecto## 🔐 Credenciales por Defecto



### Base de Datos### Base de Datos

- **Host:** `db` (desde el contenedor) o `localhost` (desde el host)- **Host:** `db` (desde el contenedor) o `localhost` (desde el host)

- **Puerto:** 3306- **Puerto:** 3306

- **Nombre BD:** `milipet_db`- **Nombre BD:** `milipet_db`

- **Usuario:** `appuser`- **Usuario:** `appuser`

- **Contraseña:** `apppass`- **Contraseña:** `apppass`

- **Root:** `root`- **Root:** `root`



### Administrador### Administrador

- **Email:** `admin@milipet.cl`- **Email:** `admin@milipet.cl`

- **Contraseña:** `br1wlpro`- **Contraseña:** `br1wlpro`



> ⚠️ **Importante:** Cambia estas credenciales en producción.> ⚠️ **Importante:** Cambia estas credenciales en producción.



## 📊 Estructura de Base de Datos## 🔧 Configuración Adicional



### Tablas Principales### Cambiar Credenciales de Admin



- **`roles`**: Roles de usuario (admin, editor, cliente)Si deseas usar una contraseña diferente:

- **`users`**: Usuarios del sistema con autenticación basada en roles

- **`species`**: Especies de mascotas (Perros, Gatos, Aves, Otros)```bash

- **`categories`**: Categorías de productos (Alimentos, Accesorios, Higiene, Juguetes)# Generar hash de contraseña

- **`products`**: Catálogo de productos con descripciones corta/largaphp tools/generate_hash.php TU_CONTRASEÑA

- **`product_species`**: Relación N:M entre productos y especies

- **`campaigns`**: Campañas de adopción con fechas de inicio/fin# Copiar el hash generado y ejecutar en MySQL:

- **`user_carts`**: Carritos de compra persistentesUPDATE users SET password = '<hash_generado>' 

- **`cart_items`**: Ítems dentro de los carritosWHERE email = 'admin@milipet.cl';

- **`favorites`**: Productos favoritos por usuario```



### Características del Nuevo Esquema### Crear Usuario Admin Adicional



#### Productos```bash

- ✅ **Descripción corta** (`short_desc`): Para listados y previews (max 255 caracteres)# Generar hash

- ✅ **Descripción larga** (`long_desc`): Detalle completo del productophp tools/generate_hash.php contraseña_nueva

- ✅ **Productos destacados** (`is_featured`): Marca productos especiales para mostrar en home

- ✅ **Relación N:M con especies**: Un producto puede ser para múltiples especies# Ejecutar en MySQL:

INSERT INTO users (role_id, name, email, password, is_active)

#### CampañasSELECT r.id, 'Nombre Usuario', 'email@dominio.com', '<hash_generado>', 1

- ✅ **Rango de fechas**: `start_date` y `end_date` para campañas con duraciónFROM roles r WHERE r.name = 'admin';

- ✅ **Banner personalizado**: `banner_image` para imágenes promocionales```

- ✅ **Validación automática**: Las campañas activas se filtran por fechas

### Variables de Entorno (Opcional)

#### Autenticación

- ✅ **Sistema de roles**: Separación clara entre admin, editor y clientePuedes ajustar puertos y credenciales editando `docker-compose.yml`:

- ✅ **Remember me**: Tokens de sesión persistente (30 días)

- ✅ **Última conexión**: Registro de `last_login` por usuario- `WEB_PORT` (por defecto: 8080)

- ✅ **Seguridad**: Contraseñas hasheadas con `password_hash()`- `PMA_PORT` (por defecto: 8081)

- `DB_PORT` (por defecto: 3306)

## 🔧 Configuración Adicional- `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASS`



### Cambiar Credenciales de Admin## Subida de imágenes

- Las imágenes se guardan en `public/assets/img/`.

Si deseas usar una contraseña diferente:- Tamaños aceptados: JPG/PNG (máx. ~3MB a nivel de formulario).

- Al reemplazar una imagen local por otra, la anterior se elimina automáticamente.

```bash

# Generar hash de contraseña## Personalización de la tienda

php tools/generate_hash.php TU_CONTRASEÑA- Edita `config/config.php` en la clave `store` para actualizar:

  - `name`, `address`, `phone`, `email`

# Copiar el hash generado y ejecutar en MySQL:  - `social.whatsapp`, `social.instagram`, `social.facebook`

UPDATE users SET password = '<hash_generado>'   - `business_hours`

WHERE email = 'admin@milipet.cl';- Font Awesome (opcional): si tienes un Kit, define la constante `FONTAWESOME_KIT` (o ignora; el sitio funciona sin eso).

```

## Rutas principales

### Crear Usuario Admin Adicional- Home: `http://localhost:8080/?r=home`

- Catálogo: `http://localhost:8080/?r=catalog` (filtros por categoría/especie, búsqueda, ver detalle)

```bash- Campañas/Estáticos: `http://localhost:8080/?r=adoptions`, `http://localhost:8080/?r=about`, `http://localhost:8080/?r=policies`

# Generar hash- Admin: `http://localhost:8080/?r=admin/dashboard`, `http://localhost:8080/?r=admin/products`

php tools/generate_hash.php contraseña_nueva

## Notas y solución de problemas

# Ejecutar en MySQL:- El DocumentRoot ya apunta a `public/` en el contenedor; accede por `http://localhost:8080/`.

INSERT INTO users (role_id, name, email, password, is_active)- Error de conexión a BD: revisa variables en `docker-compose.yml` y que los contenedores estén arriba (`docker compose ps`).

SELECT r.id, 'Nombre Usuario', 'email@dominio.com', '<hash_generado>', 1- Permisos de archivos (Linux): si ves problemas al escribir/guardar desde el contenedor, puedes descomentar la línea `user: "${HOST_UID:-1000}:${HOST_GID:-1000}"` en `docker-compose.yml`.

FROM roles r WHERE r.name = 'admin';- CSS/JS no cargan: fuerza actualización con Ctrl+F5.

```

## Estructura del proyecto (resumen)

### Variables de Entorno (Opcional)```

public/          # Punto de entrada (index.php), assets

Puedes ajustar puertos y credenciales editando `docker-compose.yml`:app/             # MVC básico: controllers, models, views

config/          # Configuración app + DB

- `WEB_PORT` (por defecto: 8080)database/        # Esquema SQL y datos de ejemplo

- `PMA_PORT` (por defecto: 8081)```

- `DB_PORT` (por defecto: 3306)

- `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASS`## Desarrollo

- No requiere Composer ni Node; es PHP plano + Bootstrap (CDN).

## 🛠️ Herramientas CLI- Si agregas nuevas tablas/cambios, actualiza `database/schema.sql`.



### Generar Hash de Contraseña---

```bash

# Modo interactivoHecho con cariño para MiliPet 🐾

php tools/generate_hash.php

# Con argumento
php tools/generate_hash.php micontraseña
```

### Verificar Estado de la Base de Datos
```bash
php tools/migrate_data.php
```

Este script verifica:
- Total de campañas y si tienen fechas asignadas
- Total de usuarios y sus roles
- Estado general de la base de datos

## 🎨 Personalización

### Configuración de la Tienda

Edita `config/store.php` para actualizar:

```php
define('STORE_NAME', 'MiliPet');
define('STORE_PHONE', '+56 9 5458036');
define('STORE_EMAIL', 'contacto@milipet.cl');
define('STORE_ADDRESS', 'Maipú, Chile');

// Redes sociales
const SOCIAL_MEDIA = [
    'whatsapp' => [
        'display' => '+56 9 5458036',
        'link' => 'https://wa.me/56954580360'
    ],
    'instagram' => '@mili_petshop',
    'facebook' => 'MiliPetChile'
];
```

### Subida de Imágenes
- **Ubicación:** `public/assets/img/`
- **Formatos:** JPG, PNG, WebP
- **Tamaño máximo:** 3MB
- **Gestión automática:** Las imágenes antiguas se eliminan al reemplazar

## 🗺️ Rutas Principales

### Sitio Público
- **Home:** `?r=home`
- **Catálogo:** `?r=catalog`
- **Detalle producto:** `?r=catalog/detail&id=X`
- **Adopciones:** `?r=adoptions`
- **Acerca de:** `?r=about`
- **Políticas:** `?r=policies`

### Panel Administrativo
- **Login:** `?r=auth/admin_login`
- **Dashboard:** `?r=admin/dashboard`
- **Productos:** `?r=admin/products`
- **Campañas:** `?r=admin/campaigns`

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
```bash
# Verificar que los contenedores estén corriendo
docker compose ps

# Ver logs del contenedor de base de datos
docker compose logs db

# Reiniciar servicios
docker compose restart
```

### CSS/JS no cargan
- Fuerza actualización con `Ctrl+F5`
- Verifica que la ruta sea `http://localhost:8080/` (con el puerto correcto)
- El DocumentRoot apunta a `public/` en el contenedor

### Permisos de archivos (Linux)
Si tienes problemas al subir imágenes desde el panel admin:

```bash
# Dar permisos de escritura
chmod -R 775 public/assets/img/

# O descomentar en docker-compose.yml:
# user: "${HOST_UID:-1000}:${HOST_GID:-1000}"
```

### No puedo iniciar sesión como admin

1. Verifica que el usuario existe:
```sql
SELECT u.*, r.name as role_name 
FROM users u 
JOIN roles r ON u.role_id = r.id 
WHERE u.email = 'admin@milipet.cl';
```

2. Si no existe, créalo:
```bash
php tools/generate_hash.php br1wlpro
# Luego ejecuta el INSERT con el hash generado
```

3. Verifica que la contraseña es correcta en la base de datos (debe ser un hash largo que empiece con `$2y$`)

## 📁 Estructura del Proyecto

```
milipet/
├── app/
│   ├── controllers/     # Controladores (MVC)
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── CatalogController.php
│   │   └── HomeController.php
│   ├── models/          # Modelos de datos
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Species.php
│   │   └── Campaign.php
│   ├── views/           # Vistas (HTML/PHP)
│   │   ├── admin/
│   │   ├── catalog/
│   │   └── layout/
│   └── helpers/         # Funciones auxiliares
│       └── auth_helper.php
├── config/
│   ├── config.php       # Configuración general
│   ├── db.php           # Conexión a BD
│   └── store.php        # Datos de la tienda
├── database/
│   ├── milipet_db.sql   # Esquema completo con datos
│   └── milipet_db_old.sql  # Backup del esquema anterior
├── public/
│   ├── index.php        # Punto de entrada
│   └── assets/          # CSS, JS, imágenes
│       ├── css/
│       ├── js/
│       └── img/
├── tools/
│   ├── generate_hash.php    # Generar hashes de contraseña
│   └── migrate_data.php     # Verificar estado de BD
├── docker-compose.yml   # Configuración Docker
└── README.md
```

## 🚢 Despliegue en Producción

### Checklist de Seguridad

- [ ] Cambiar contraseña de admin
- [ ] Cambiar credenciales de base de datos
- [ ] Configurar `APP_ENV=production` en `config/config.php`
- [ ] Eliminar `tools/generate_hash.php` del servidor
- [ ] Configurar certificado SSL (HTTPS)
- [ ] Configurar backups automáticos de la base de datos
- [ ] Revisar permisos de archivos (no usar 777)
- [ ] Deshabilitar phpMyAdmin en producción

### Backup de Base de Datos

```bash
# Exportar
docker exec milipet-db mysqldump -u root -p milipet_db > backup_$(date +%Y%m%d).sql

# Restaurar
docker exec -i milipet-db mysql -u root -p milipet_db < backup_20251115.sql
```

## 💻 Desarrollo

- **Sin dependencias externas**: PHP plano + Bootstrap (CDN)
- **Sin build tools**: No requiere Composer ni Node.js
- **MVC simple**: Arquitectura clara y fácil de extender
- **Docker ready**: Ambiente de desarrollo consistente

### Comandos Útiles Docker

```bash
# Detener servicios
docker compose down

# Ver logs en tiempo real
docker compose logs -f

# Acceder al contenedor PHP
docker exec -it milipet-web bash

# Acceder al contenedor MySQL
docker exec -it milipet-db mysql -u root -p

# Reiniciar solo un servicio
docker compose restart web
```

### Extender la Aplicación

Para agregar nuevas funcionalidades:

1. **Nuevo modelo:** Crea un archivo en `app/models/`
2. **Nuevo controlador:** Crea un archivo en `app/controllers/`
3. **Nueva vista:** Crea un archivo en `app/views/`
4. **Nueva ruta:** Agrega el case en `public/index.php`
5. **Nueva tabla:** Actualiza `database/milipet_db.sql`

---

**Hecho con ❤️ para MiliPet** 🐾
