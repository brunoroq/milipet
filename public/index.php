<?php
// public/index.php
session_start();
require_once __DIR__ . '/../app/helpers/auth_helper.php';
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
        require_auth();
        require __DIR__ . '/../app/controllers/AdminController.php';
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
        require __DIR__ . '/../app/controllers/AuthController.php';
        $controller = new AuthController();
        $action = $routeParts[1] ?? 'login';
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            http_response_code(404);
            require __DIR__ . '/../app/controllers/StaticController.php';
            (new StaticController())->notFound();
        }
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
        break;
    case 'admin/products':
        require __DIR__ . '/../app/controllers/AdminController.php';
        (new AdminController())->products();
        break;
    case 'admin/product/save':
        require __DIR__ . '/../app/controllers/AdminController.php';
        (new AdminController())->saveProduct();
        break;
    case 'admin/product/delete':
        require __DIR__ . '/../app/controllers/AdminController.php';
        (new AdminController())->deleteProduct();
        break;
    default:
        http_response_code(404);
        render('static/404');
        break;
}
