# Login Unificado - MiliPet

## 📋 Resumen de Cambios

Se ha unificado el sistema de login del proyecto MiliPet para que:
- **Use el layout público** (header + footer) en lugar del layout admin
- **Tenga textos genéricos** que no mencionen "administración"
- **Redirija según el rol** del usuario (admin/editor/cliente)
- **Use rutas consistentes** (`auth/login` en lugar de `auth/admin_login`)

---

## ✅ Cambios Implementados

### 1. Vista de Login (`app/views/auth/login.php`)

**ANTES:**
- Layout standalone con fondo verde sólido
- Título: "Panel de Administración"
- Sin header ni footer públicos

**DESPUÉS:**
- Usa layout público con header y footer normales
- Título genérico: "Iniciar sesión"
- Subtítulo: "Accede con tu cuenta de MiliPet"
- Integrada en `<section class="main-section">`
- Card centrado con Bootstrap
- Iconos y estilos modernos mantenidos

**Archivo:** Renombrado de `admin_login.php` a `login.php`

---

### 2. AuthController (`app/controllers/AuthController.php`)

**Cambios principales:**

#### `adminLoginForm()` (ahora login unificado)
- Renderiza `auth/login` con layout público
- Elimina restricción de "solo admin"

#### `adminLogin()` (procesamiento)
- **Validación por rol:** Ya no rechaza usuarios que no sean admin
- **Redirección inteligente:**
  ```php
  if ($role === 'admin' || $role === 'editor') {
      header('Location: /?r=admin/dashboard');
  } elseif ($role === 'cliente') {
      flash('success', '¡Bienvenido de vuelta, ' . htmlspecialchars($u['name']) . '!');
      header('Location: /?r=home');
  } else {
      flash('success', 'Sesión iniciada correctamente.');
      header('Location: /?r=home');
  }
  ```
- **Mensajes actualizados:** Todas las redirecciones usan `auth/login`

---

### 3. Sistema de Rutas (`public/index.php`)

**Rutas unificadas:**
```php
// Redirecciones legacy
if ($route === 'login' || $route === 'auth/admin_login') {
    header('Location: ' . url(['r' => 'auth/login']));
    exit;
}

// Rutas principales
if ($route === 'auth/login') { (new AuthController())->adminLoginForm(); exit; }
if ($route === 'auth/login_post' && $_SERVER['REQUEST_METHOD'] === 'POST') { (new AuthController())->adminLogin(); exit; }
if ($route === 'auth/logout') { (new AuthController())->logout(); exit; }
```

**Función `render()` mejorada:**
```php
function render($view, $vars = []) {
    $isAdmin = strpos($view, 'admin/') === 0;
    $isAuth = strpos($view, 'auth/') === 0;
    
    // Las vistas auth/* siempre usan layout público
    if ($isAdmin && !$isAuth) {
        // Layout admin (sin header/footer público)
        require_auth();
        ob_start();
        include __DIR__ . '/../app/views/' . $view . '.php';
        $content = ob_get_clean();
        include __DIR__ . '/../app/views/layout/admin_layout.php';
        return;
    }
    
    // Layout público (header + footer)
    extract($vars);
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    include __DIR__ . '/../app/views/layout/header.php';
    include __DIR__ . '/../app/views/' . $view . '.php';
    include __DIR__ . '/../app/views/layout/footer.php';
}
```

---

### 4. Helpers de Autenticación

**`app/helpers/auth_helper.php`:**
```php
function require_auth() {
    if (!is_authenticated()) {
        flash('error','Debes iniciar sesión.');
        header('Location: ' . url(['r'=>'auth/login']));
        exit;
    }
}

function require_admin() {
    $uid = $_SESSION['user_id'] ?? ($_SESSION['uid'] ?? null);
    $role = $_SESSION['role'] ?? null;
    if (!$uid || $role !== 'admin') {
        flash('error','Debes iniciar sesión.');
        header('Location: ' . url(['r'=>'auth/login']));
        exit;
    }
}
```

**`config/config.php`:**
```php
function requireRole(array $allowed) {
    $role = $_SESSION['role'] ?? null;
    $uid  = $_SESSION['uid'] ?? ($_SESSION['user_id'] ?? null);
    if (!$uid || !$role || !in_array($role, $allowed, true)) {
        flash('error','Debes iniciar sesión.');
        header('Location: ' . url(['r'=>'auth/login']));
        exit;
    }
}
```

---

### 5. Header Público (`app/views/layout/header.php`)

**Botón de usuario actualizado:**
```php
<a class="btn btn-outline-light rounded-pill px-3 header-icon" 
   href="<?= url(['r'=>'auth/login']) ?>" 
   title="Iniciar sesión">
   <i class="fa-regular fa-user"></i>
</a>
```

---

### 6. Layout Admin (`app/views/layout/admin_layout.php`)

**Cambio en `<body>`:**
```html
<body class="admin-layout">
```

**Estilos CSS actualizados:**
```css
body.admin-layout {
  background: #f5f7fa;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
```

Esto permite que los estilos específicos del panel admin solo se apliquen cuando se usa ese layout, no cuando se muestra el login con el layout público.

---

## 🎯 Flujo Completo

### 1. Usuario hace clic en icono de usuario (header)
- **URL:** `/?r=auth/login`
- **Vista:** `app/views/auth/login.php`
- **Layout:** Público (header + footer normal)

### 2. Usuario ingresa credenciales y envía formulario
- **URL POST:** `/?r=auth/login_post`
- **Controlador:** `AuthController::adminLogin()`
- **Validación:** Email, password, cuenta activa

### 3. Sistema redirige según rol:

#### Si rol = `admin` o `editor`
```php
header('Location: /?r=admin/dashboard');
```
- **Layout:** Admin (sin header/footer público)
- **Vistas:** `admin/dashboard.php`, `admin/products.php`, etc.

#### Si rol = `cliente`
```php
flash('success', '¡Bienvenido de vuelta, ' . htmlspecialchars($u['name']) . '!');
header('Location: /?r=home');
```
- **Layout:** Público (header + footer)
- **Vista:** Home con mensaje de bienvenida

#### Si rol desconocido
```php
flash('success', 'Sesión iniciada correctamente.');
header('Location: /?r=home');
```
- **Layout:** Público
- **Vista:** Home

---

## 🔄 Compatibilidad con URLs Antiguas

Las rutas antiguas se redirigen automáticamente:

```php
// Estas URLs:
/?r=login
/?r=auth/admin_login

// Se redirigen a:
/?r=auth/login
```

---

## 🛡️ Seguridad

### Rate Limiting
- 5 intentos fallidos por email+IP
- Bloqueo de 15 minutos

### CSRF Protection
- Token CSRF en todos los formularios
- Validación en cada POST

### Session Security
- `session_regenerate_id(true)` al hacer login
- Timeout de inactividad (30 minutos)
- Cookies con `httponly`, `samesite: Lax`

### Remember Me
- Token seguro de 64 caracteres
- Hash SHA-256 en base de datos
- Expiración de 30 días

---

## 📁 Archivos Modificados

1. ✅ `app/views/auth/admin_login.php` → `app/views/auth/login.php` (renombrado)
2. ✅ `app/controllers/AuthController.php`
3. ✅ `public/index.php`
4. ✅ `app/helpers/auth_helper.php`
5. ✅ `config/config.php`
6. ✅ `app/views/layout/header.php`
7. ✅ `app/views/layout/admin_layout.php`

---

## 🧪 Testing

### Para probar como admin:
1. Visitar `http://localhost:8080/?r=auth/login`
2. Login con credenciales admin
3. Verificar redirección a `/?r=admin/dashboard`
4. Confirmar que el panel admin NO muestra header/footer públicos

### Para probar como cliente:
1. Crear usuario con `role_id = 3` (cliente) en tabla `users`
2. Login con esas credenciales
3. Verificar redirección a `/?r=home`
4. Confirmar mensaje: "¡Bienvenido de vuelta, [nombre]!"
5. Confirmar que se muestra header y footer normales

### Para probar logout:
1. Hacer login con cualquier rol
2. Visitar `/?r=auth/logout`
3. Verificar redirección a home
4. Confirmar que sesión se destruyó

---

## 📝 Notas Adicionales

### Variables de sesión establecidas al login:
```php
$_SESSION['user_id'] = (int)$u['id'];
$_SESSION['uid'] = (int)$u['id'];        // Compatibilidad
$_SESSION['email'] = $u['email'];
$_SESSION['name'] = $u['name'];
$_SESSION['role'] = $u['role_name'];     // 'admin', 'editor', 'cliente'
$_SESSION['last_activity'] = time();
```

### Protección de rutas admin:
```php
// En public/index.php, antes de acceder a AdminController:
if (function_exists('requireRole')) { 
    requireRole(['admin','editor']); 
}
```

### URLs principales:
- **Login:** `/?r=auth/login`
- **Logout:** `/?r=auth/logout`
- **Admin:** `/?r=admin/dashboard`
- **Home:** `/?r=home` o `/`

---

## 🔧 Pulido Final

### 1. `require_admin()` vs `requireRole()` - Uso Correcto

**`require_admin()`** (en `app/helpers/auth_helper.php`):
- **Uso:** Solo para rutas que EXCLUSIVAMENTE son para el rol `admin`
- **Comportamiento:** Rechaza cualquier rol que no sea exactamente `'admin'`
- **Ejemplo:** Funciones críticas como eliminar usuarios, cambiar configuración del sistema

```php
/**
 * Requiere que el usuario sea SOLO admin (rol estricto)
 * Para permitir admin Y editor, usa requireRole(['admin','editor']) en su lugar
 */
function require_admin() {
    $uid = $_SESSION['user_id'] ?? ($_SESSION['uid'] ?? null);
    $role = $_SESSION['role'] ?? null;
    if (!$uid || $role !== 'admin') {
        flash('error','Debes iniciar sesión.');
        header('Location: ' . url(['r'=>'auth/login']));
        exit;
    }
}
```

**`requireRole(array $allowed)`** (en `config/config.php`):
- **Uso:** Para rutas que aceptan MÚLTIPLES roles
- **Comportamiento:** Permite cualquier rol dentro del array `$allowed`
- **Ejemplo:** Panel de administración donde tanto admin como editor pueden trabajar

```php
/**
 * Verifica que el usuario tenga uno de los roles permitidos
 * @param array $allowed Array de roles permitidos ['admin','editor','cliente']
 */
function requireRole(array $allowed) {
    $role = $_SESSION['role'] ?? null;
    $uid  = $_SESSION['uid'] ?? ($_SESSION['user_id'] ?? null);
    if (!$uid || !$role || !in_array($role, $allowed, true)) {
        flash('error','Debes iniciar sesión.');
        header('Location: ' . url(['r'=>'auth/login']));
        exit;
    }
}
```

**Regla de oro:**
- Panel admin general → `requireRole(['admin','editor'])`
- Funciones críticas solo para admin → `require_admin()`

### 2. Ruta `admin/dashboard` - Configuración Verificada

En `public/index.php`, la ruta admin está configurada correctamente:

```php
switch ($routeParts[0]) {
    case 'admin':
        // ✅ Protección a nivel de router - Admin Y Editor permitidos
        if (function_exists('requireRole')) { 
            requireRole(['admin','editor']); 
        }
        
        $controller = new AdminController();
        
        // Manejar rutas dinámicamente
        $action = $routeParts[1] ?? 'dashboard';  // Default: dashboard
        
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            // 404 si el método no existe
            http_response_code(404);
            (new StaticController())->notFound();
        }
        break;
}
```

**Verificaciones completadas:**
- ✅ Método `dashboard()` existe en `AdminController.php`
- ✅ Vista `app/views/admin/dashboard.php` existe
- ✅ Protección `requireRole(['admin','editor'])` aplicada antes de acceder al controller
- ✅ Funciona para: `/?r=admin` (default) y `/?r=admin/dashboard` (explícito)

### 3. Flash Messages en Layout Público - Implementado

Se agregó el bloque de flash messages en `app/views/layout/header.php` (justo después de `<main>`):

```php
<!-- Main content -->
<main class="container mt-3">
	
	<!-- Flash Messages -->
	<?php if ($msg = flash('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-exclamation-circle me-2"></i>
			<div><?= htmlspecialchars($msg) ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
	
	<?php if ($msg = flash('success')): ?>
		<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-check-circle me-2"></i>
			<div><?= $msg ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
	
	<?php if ($msg = flash('warning')): ?>
		<div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-exclamation-triangle me-2"></i>
			<div><?= $msg ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
	
	<?php if ($msg = flash('info')): ?>
		<div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
			<i class="fas fa-info-circle me-2"></i>
			<div><?= htmlspecialchars($msg) ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
		</div>
	<?php endif; ?>
```

**Características:**
- ✅ Soporta 4 tipos: `error`, `success`, `warning`, `info`
- ✅ Iconos de Font Awesome para cada tipo
- ✅ Botón de cerrar con Bootstrap (`data-bs-dismiss="alert"`)
- ✅ Animación fade show
- ✅ Sanitización con `htmlspecialchars()` en error/info
- ✅ Permite HTML en success/warning (para iconos y links)

**Ahora los mensajes se muestran:**
- En login (si hay error de credenciales)
- En home (cuando cliente inicia sesión: "¡Bienvenido de vuelta!")
- En cualquier vista pública que use el layout estándar

---

## ✨ Resultado Final

- ✅ Login con aspecto profesional integrado al sitio público
- ✅ Textos genéricos sin mención a "panel admin"
- ✅ Header y footer normales en la página de login
- ✅ Redirección inteligente por rol
- ✅ Panel admin completamente separado (sin header/footer público)
- ✅ Sistema de rutas limpio y consistente
- ✅ Compatibilidad con URLs legacy
- ✅ Seguridad robusta mantenida

---

**Fecha:** 2025-01-17  
**Proyecto:** MiliPet  
**Branch:** cleanup-safe
