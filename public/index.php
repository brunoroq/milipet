<?php
// public/index.php

// --- Seguridad de cookies de sesión (ANTES de session_start) ---
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_start();
require_once __DIR__ . '/../app/helpers/auth_helper.php';
require_once __DIR__ . '/../config/config.php';
// Generate a CSRF token for the session if not present
if (empty($_SESSION['csrf'])) {
    try {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    } catch (Exception $e) {
        // Fallback for older PHP or if random_bytes fails
        $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(16));
    }
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

// Intentar auto-login por cookie de recordarme antes de procesar rutas
if (function_exists('mp_remember_autologin')) { mp_remember_autologin(); }

// Si viene ?q=... pero no hay ruta, enviamos al catálogo
if (!isset($_GET['r']) && isset($_GET['q'])) {
    $qs = $_GET;
    $qs['r'] = 'catalog';
    header('Location: ?' . http_build_query($qs));
    exit;
}

function render($view, $vars = []) {
    // Si la vista es admin, utilizar header específico y proteger el acceso
    $isAdmin = strpos($view, 'admin/') === 0;
    if ($isAdmin) {
        // Forzar autenticación para cualquier render admin
        require_auth();
        extract($vars);
        include __DIR__ . '/../app/views/layout/admin_header.php';
        include __DIR__ . '/../app/views/' . $view . '.php';
        include __DIR__ . '/../app/views/layout/footer.php';
        return;
    }

    // Nota: no redirigimos automáticamente admins autenticados desde el frontend
    // Esto permite visualizar la web pública incluso con sesión admin activa.

    extract($vars);
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    include __DIR__ . '/../app/views/layout/header.php';
    include __DIR__ . '/../app/views/' . $view . '.php';
    include __DIR__ . '/../app/views/layout/footer.php';
}

$route = $_GET['r'] ?? 'home';

// Redirecciones legacy de login antiguo
if ($route === 'login' || $route === 'auth/login') {
    header('Location: ' . url(['r' => 'auth/admin_login']));
    exit;
}

// Rutas auth administración antes del switch general
if ($route === 'auth/admin_login') { (new AuthController())->adminLoginForm(); exit; }
if ($route === 'auth/admin_login_post' && $_SERVER['REQUEST_METHOD'] === 'POST') { (new AuthController())->adminLogin(); exit; }
if ($route === 'auth/logout') { (new AuthController())->logout(); exit; }
$routeParts = explode('/', $route);

// Redirigir rutas antiguas a las nuevas
if ($route === 'login') {
    header('Location: ?r=auth/login');
    exit;
}

// Manejar rutas con prefijos
switch ($routeParts[0]) {
    case 'admin':
        // Forzar autenticación a nivel de router antes de acceder a controladores admin
        if (function_exists('requireRole')) { requireRole(['admin','editor']); }
        $controller = new AdminController();
        
        // Manejar rutas de tres niveles (admin/product/save)
        if (count($routeParts) >= 3 && $routeParts[1] === 'product' && $routeParts[2] === 'save') {
            $controller->saveProduct();
        } elseif (count($routeParts) >= 3 && $routeParts[1] === 'product' && $routeParts[2] === 'delete') {
            $controller->deleteProduct();
        } else {
            // Manejar rutas de dos niveles (admin/dashboard, admin/products)
            $action = $routeParts[1] ?? 'dashboard';
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                http_response_code(404);
                require __DIR__ . '/../app/controllers/StaticController.php';
                (new StaticController())->notFound();
            }
        }
        break;

    case 'auth':
        // Ya manejado arriba; cualquier otra subruta se considera 404
        http_response_code(404);
        require __DIR__ . '/../app/controllers/StaticController.php';
        (new StaticController())->notFound();
        break;

    case 'home':
        require __DIR__ . '/../app/controllers/HomeController.php';
        (new HomeController())->index();
        break;

    case 'catalog':
        require __DIR__ . '/../app/controllers/CatalogController.php';
        (new CatalogController())->index();
        break;

    case 'product':
        require __DIR__ . '/../app/controllers/CatalogController.php';
        (new CatalogController())->detail();
        break;

    case 'about':
        require __DIR__ . '/../app/controllers/StaticController.php';
        (new StaticController())->about();
        break;

    case 'policies':
        require __DIR__ . '/../app/controllers/StaticController.php';
        (new StaticController())->policies();
        break;

    case 'adoptions':
        require __DIR__ . '/../app/controllers/StaticController.php';
        (new StaticController())->adoptions();
        break;

    case 'favorites':
        // Si no hay sesión de usuario, mostrar mensaje de que se requiere cuenta
        if (empty($_SESSION['uid'])) {
            render('catalog/favorites_guest');
            break;
        }
        // Vista basada en IDs enviados por query (?ids=1,2,3) o manejados por JS
        $idsParam = $_GET['ids'] ?? '';
        $ids = array_filter(array_map('intval', explode(',', $idsParam)), fn($v) => $v > 0);
        require_once __DIR__ . '/../app/models/Product.php';
        $products = Product::findByIds($ids);
        render('catalog/favorites', ['products' => $products, 'ids' => $ids]);
        break;

    case 'cart':
        require_once __DIR__ . '/../app/models/Product.php';
        $ids = [];
        $hasSession = !empty($_SESSION['uid']);
        
        if ($hasSession) {
            // Usuario con sesión: cargar carrito persistente de BD
            require_once __DIR__ . '/../app/models/UserCart.php';
            $ids = UserCart::getCartProductIds((int)$_SESSION['uid']);
        } else {
            // Sin sesión: usar IDs de localStorage vía query
            $idsParam = $_GET['ids'] ?? '';
            $ids = array_filter(array_map('intval', explode(',', $idsParam)), fn($v) => $v > 0);
        }
        
        $products = Product::findByIds($ids, false); // carrito puede incluir inactivos
        render('catalog/cart', ['products' => $products, 'ids' => $ids, 'hasSession' => $hasSession]);
        break;
    case 'admin/products':
        if (function_exists('requireRole')) { requireRole(['admin','editor']); }
        (new AdminController())->products();
        break;
    case 'admin/product/save':
        if (function_exists('requireRole')) { requireRole(['admin','editor']); }
        (new AdminController())->saveProduct();
        break;
    case 'admin/product/delete':
        if (function_exists('requireRole')) { requireRole(['admin','editor']); }
        (new AdminController())->deleteProduct();
        break;
    default:
        http_response_code(404);
        render('static/404');
        break;
}
