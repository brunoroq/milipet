# Hero Slider Implementation - MiliPet

## 📋 Resumen de Implementación

Se ha implementado un sistema completo de carousel/slider para el hero de la página de inicio, reemplazando la imagen estática por un carrusel dinámico con múltiples slides administrables desde el panel de administración.

## ✅ Componentes Implementados

### 1. Base de Datos
- **Tabla:** `home_hero_slides`
- **Campos:**
  - `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
  - `title` (VARCHAR 255, nullable) - Solo referencia interna
  - `subtitle` (VARCHAR 255, nullable) - Solo referencia interna
  - `image_url` (VARCHAR 500, NOT NULL) - URL completa de la imagen
  - `sort_order` (INT, DEFAULT 0) - Orden de visualización
  - `is_active` (TINYINT, DEFAULT 1) - Estado de visibilidad
  - `created_at`, `updated_at` (TIMESTAMP)

### 2. Modelo
- **Archivo:** `app/models/HomeHeroSlide.php`
- **Métodos:**
  - `getActiveSlides()` - Obtiene slides activos ordenados
  - `getAllSlides()` - Obtiene todos los slides (admin)
  - `getById($id)` - Obtiene un slide específico
  - `create($data)` - Crea nuevo slide
  - `update($data)` - Actualiza slide existente
  - `delete($id)` - Elimina slide

### 3. Controlador
- **Archivo:** `app/controllers/AdminController.php`
- **Métodos agregados:**
  - `heroSlides()` - Lista y formulario de gestión
  - `saveHeroSlide()` - Guarda (crear/actualizar) con validación
  - `deleteHeroSlide()` - Elimina slide

- **Archivo:** `app/controllers/HomeController.php`
- **Modificación:**
  - Carga `HomeHeroSlide::getActiveSlides()`
  - Fallback a `ContentBlock` si no hay slides
  - Pasa `$heroSlides` a la vista

### 4. Vista Frontend
- **Archivo:** `app/views/home/index.php`
- **Cambios:**
  - Reemplazado `<img>` fijo por Bootstrap 5 Carousel
  - Diseño responsive (col-md-5 texto, col-md-7 carousel)
  - Transición fade entre slides
  - Indicadores (dots) y controles prev/next
  - Fallback a imagen estática si no hay slides

### 5. Vista Admin
- **Archivo:** `app/views/admin/hero_slides.php`
- **Características:**
  - Tabla con vista previa, URL, orden y estado
  - Formulario inline para crear/editar
  - Sticky sidebar con formulario
  - Modal de confirmación para eliminar
  - Validación de URL completa (http/https)
  - Auto-hide de alertas tras 5 segundos

### 6. Estilos CSS
- **Archivo:** `public/assets/css/style.css`
- **Nuevos estilos:**
  - `.hero-slide-wrapper` - Contenedor con aspect ratio 3:2
  - `.hero-carousel-img` - Imagen absoluta con object-fit cover
  - Controles circulares con hover (verde #2f7d32)
  - Indicadores con efecto scale activo
  - Transición fade suave entre slides
  - Responsive: aspect ratio 75% en móvil

### 7. Rutas
- **Archivo:** `public/index.php`
- **Nuevas rutas:**
  - `admin/hero-slides` → Lista y formulario
  - `admin/hero-slides/save` → Guardar (POST)
  - `admin/hero-slides/delete` → Eliminar (POST)
  - Todas requieren rol `admin` o `editor`

### 8. Navegación Admin
- **Archivo:** `app/views/layout/admin_layout.php`
- **Cambio:**
  - Agregado link "Hero Slides" con icono `fa-images`
  - Estado activo cuando se está en la página

## 🎨 Características Visuales

### Frontend (Carousel)
- ✅ Transición fade suave (0.6s)
- ✅ Auto-rotación cada 5 segundos
- ✅ Controles prev/next con hover
- ✅ Indicadores (dots) con estado activo
- ✅ Aspect ratio 3:2 (escritorio) y 4:3 (móvil)
- ✅ Rounded corners (1rem) con shadow-lg
- ✅ Lazy loading (primer slide eager, resto lazy)

### Backend (Admin)
- ✅ Tabla con miniaturas (60x40px)
- ✅ Badge de orden y estado (activo/inactivo)
- ✅ Formulario sticky en sidebar
- ✅ Vista previa de imagen al editar
- ✅ Mensajes flash con auto-hide
- ✅ Modal de confirmación para eliminar
- ✅ Validación de URL completa

## 🔧 Validaciones Implementadas

### Lado Servidor (AdminController)
1. **URL de imagen obligatoria**
2. **URL válida** (formato URL correcto)
3. **Protocolo HTTP/HTTPS** (debe empezar con http:// o https://)
4. **Sort order** (entero, default 0)
5. **is_active** (checkbox, default activo en crear)

### Lado Cliente (HTML5)
1. **Campo URL requerido** (`required`)
2. **Tipo URL** (`type="url"`)
3. **Placeholder con ejemplo**

## 📊 Flujo de Datos

```
Usuario Frontend
    ↓
HomeController::index()
    ↓
HomeHeroSlide::getActiveSlides()
    ↓ (WHERE is_active=1 ORDER BY sort_order)
BD: home_hero_slides
    ↓
$heroSlides array
    ↓
home/index.php (carousel)
    ↓
Bootstrap Carousel con fade
```

```
Usuario Admin
    ↓
AdminController::heroSlides()
    ↓
HomeHeroSlide::getAllSlides()
    ↓
admin/hero_slides.php
    ↓
Formulario POST → AdminController::saveHeroSlide()
    ↓ (validación + create/update)
BD: home_hero_slides
    ↓
Redirect con mensaje flash
```

## 🚀 Cómo Usar

### Frontend (Automático)
1. Los slides activos se muestran automáticamente en el home
2. Si no hay slides, se usa el ContentBlock 'home.hero_image' como fallback
3. El carousel rota cada 5 segundos
4. Usuario puede navegar con controles o indicadores

### Admin Panel
1. Ir a **Panel Admin → Hero Slides**
2. **Crear nuevo slide:**
   - Ingresar URL completa de imagen (obligatorio)
   - Opcionalmente: título y subtítulo (solo referencia)
   - Establecer orden numérico (0, 1, 2...)
   - Marcar como activo (por defecto activo)
   - Clic en "Crear slide"

3. **Editar slide:**
   - Clic en botón "Editar" (icono lápiz)
   - Modificar campos
   - Clic en "Actualizar slide"

4. **Eliminar slide:**
   - Clic en botón "Eliminar" (icono papelera)
   - Confirmar en modal

5. **Cambiar orden:**
   - Editar campo "Orden de visualización"
   - Los slides se ordenan de menor a mayor

## 📸 Recomendaciones de Imágenes

- **Resolución mínima:** 1200x800px (ratio 3:2)
- **Peso máximo:** 500KB para óptimo rendimiento
- **Formato:** JPG o WebP (mejor compresión)
- **Contenido:** Evitar texto importante en los bordes (safe area)
- **Optimización:** Comprimir antes de subir (TinyPNG, Squoosh, etc.)

## 🔒 Seguridad

- ✅ Autenticación requerida (admin/editor)
- ✅ Validación de URL en servidor
- ✅ Sanitización de HTML con `htmlspecialchars()`
- ✅ CSRF protection en formularios (checkCsrf)
- ✅ Prepared statements en queries SQL (PDO)

## 🐛 Debugging

### Si no se muestran slides:
1. Verificar que existan slides en la BD: `SELECT * FROM home_hero_slides WHERE is_active=1`
2. Verificar que HomeController esté cargando: `var_dump($heroSlides)` en vista
3. Revisar errores PHP: `tail -f /var/log/apache2/error.log` (Docker)

### Si el carousel no rota:
1. Verificar que Bootstrap JS esté cargado
2. Abrir consola del navegador (F12) y buscar errores JS
3. Verificar atributos `data-bs-ride="carousel"` y `data-bs-interval="5000"`

### Si no se puede crear slide:
1. Verificar conexión a BD (config/db.php)
2. Verificar formato de URL (debe ser http:// o https://)
3. Revisar logs de PHP para errores SQL

## 📦 Archivos Modificados/Creados

### Nuevos
- ✅ `database/home_hero_slides.sql`
- ✅ `app/models/HomeHeroSlide.php`
- ✅ `app/views/admin/hero_slides.php`

### Modificados
- ✅ `app/controllers/HomeController.php`
- ✅ `app/controllers/AdminController.php`
- ✅ `app/views/home/index.php`
- ✅ `app/views/layout/admin_layout.php`
- ✅ `public/index.php`
- ✅ `public/assets/css/style.css`

## 🎯 Próximos Pasos Opcionales

1. **Upload de imágenes:** Integrar sistema de upload directo (sin URL externa)
2. **CTA buttons:** Agregar botones de acción en cada slide
3. **Captions:** Mostrar título/subtítulo sobre la imagen
4. **Drag & drop:** Reordenar slides con drag and drop en admin
5. **Analytics:** Trackear qué slides tienen más interacción
6. **A/B Testing:** Probar diferentes imágenes automáticamente

---

**Implementado por:** GitHub Copilot  
**Fecha:** 2025  
**Framework:** PHP MVC Custom + Bootstrap 5
